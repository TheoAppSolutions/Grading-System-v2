<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Cleans up `subjects.subject_code` after the old→new migration.
 *
 * Problem:
 *   subject_code values still carry the grade suffix (e.g. MATH01, CRE07,
 *   ENGLPR) even though grade is already stored separately in
 *   grade_level_id. This strips the last 2 characters off subject_code
 *   and de-duplicates rows that collapse onto the same code.
 *
 * Routes:
 *   GET    /cleanup/subjects/preview → preview()  (dry run, no writes)
 *   POST   /cleanup/subjects         → migrate()  (applies the plan)
 *
 * ⚠ In THIS schema, subject_id on subject_users / subject_units /
 *   subject_excluded / grades / awards is an INTEGER, not the varchar
 *   subject_code — meaning those tables reference subjects.id (the PK),
 *   not subject_code at all. So renaming subject_code needs NO cascade.
 *   But DELETING a duplicate row does: any child row pointing at the
 *   deleted row's id must be repointed to the surviving row's id, or it
 *   becomes orphaned. `grades` and `awards` are the highest-stakes tables
 *   in this list — get CHILD_TABLES right before running against real data.
 *
 * ⚠ subject_code has a UNIQUE constraint. Conflict-disambiguated codes are
 *   checked against the whole plan before anything is written; if two
 *   assignments would still collide, the later one falls back to
 *   "{code}_{id}" and gets logged under `code_collisions` for manual review
 *   rather than throwing a constraint violation mid-transaction.
 *
 * Duplicate rule:
 *   Same new code + same subject_title  → true duplicate → delete extras,
 *   keep the lowest id.
 *   Same new code + different subject_title → NOT a duplicate, just a code
 *   collision → the largest group keeps the clean code, the rest get a
 *   disambiguated code (CODE2, CODE3, ...) so nothing gets silently merged.
 *
 * ⚠ Edge case to review manually: codes that don't use a zero-padded
 *   2-digit grade suffix (e.g. SOCSCI7, SOCSCI8 vs SOCSCI10, or MATH3 vs
 *   MATH03) will NOT strip cleanly with a blind substr(-2) — they'll get
 *   flagged under `irregular_suffix` in the report instead of being
 *   auto-processed, so you can decide the correct code by hand.
 */
class SubjectCodeCleanupController extends Controller
{
    // Tables whose `subject_id` (integer) references subjects.id and must be
    // repointed whenever a duplicate subjects row is deleted.
    private const CHILD_TABLES = [
        'subject_users',
        'subject_units',
        'subject_excluded',
        'grades',
        'awards',
    ];

    private const CONNECTION = 'new_db'; // change to null to use default connection

    // -------------------------------------------------------------------------
    // Preview (dry run)
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $plan = $this->buildPlan();

        return response()->json([
            'status' => 'preview',
            'summary' => [
                'total_rows'          => $plan['total_rows'],
                'renames'             => count($plan['renames']),
                'duplicates_to_delete'=> count($plan['deletes']),
                'conflicts_resolved'  => count($plan['conflicts']),
                'irregular_suffix'    => count($plan['irregular']),
                'code_collisions'     => count($plan['code_collisions']),
            ],
            'renames' => $plan['renames'],
            'duplicates_to_delete' => $plan['deletes'],
            'conflicts_resolved' => $plan['conflicts'],
            'irregular_suffix' => $plan['irregular'],
            'code_collisions' => $plan['code_collisions'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Apply
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $plan = $this->buildPlan();
        $db = $this->conn();

        // Backup current state before touching anything.
        $backupPath = 'subject_cleanup_backups/subjects_' . now()->format('Ymd_His') . '.json';
        Storage::put($backupPath, $db->table('subjects')->orderBy('id')->get()->toJson(JSON_PRETTY_PRINT));

        $db->transaction(function () use ($plan, $db) {

            // 1. Delete duplicate rows, repointing any child rows that
            //    referenced the deleted row's id over to the survivor's id.
            //    (subject_id on child tables is the integer PK, not the code —
            //    renaming subject_code below needs no cascade at all.)
            foreach ($plan['deletes'] as $delete) {
                foreach (self::CHILD_TABLES as $table) {
                    $db->table($table)
                        ->where('subject_id', $delete['id'])
                        ->update(['subject_id' => $delete['survivor_id']]);
                }
                $db->table('subjects')->where('id', $delete['id'])->delete();
            }

            // 2. Apply renames (clean strips + conflict-resolved codes) to surviving rows.
            foreach ($plan['renames'] as $rename) {
                $db->table('subjects')->where('id', $rename['id'])->update([
                    'subject_code' => $rename['new_code'],
                    'updated_at'   => now(),
                ]);
            }

            foreach ($plan['conflicts'] as $conflict) {
                foreach ($conflict['rows'] as $row) {
                    $db->table('subjects')->where('id', $row['id'])->update([
                        'subject_code' => $row['new_code'],
                        'updated_at'   => now(),
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'done',
            'backup' => $backupPath,
            'renamed' => count($plan['renames']),
            'duplicates_deleted' => count($plan['deletes']),
            'conflicts_resolved' => count($plan['conflicts']),
            'irregular_suffix_skipped' => count($plan['irregular']),
            'code_collisions_deduped' => count($plan['code_collisions']),
            'child_tables_id_remapped' => self::CHILD_TABLES,
        ]);
    }

    // -------------------------------------------------------------------------
    // Plan builder (shared by preview + migrate)
    // -------------------------------------------------------------------------

    private function buildPlan(): array
    {
        $rows = $this->conn()->table('subjects')->orderBy('id')->get();

        $renames   = [];
        $deletes   = [];
        $conflicts = [];
        $irregular = [];

        $groups = []; // newCode => [rows...]

        foreach ($rows as $row) {
            $oldCode = (string) $row->subject_code;

            if (strlen($oldCode) <= 2 || !$this->hasCleanTwoCharSuffix($oldCode)) {
                $irregular[] = [
                    'id' => $row->id,
                    'subject_code' => $oldCode,
                    'reason' => strlen($oldCode) <= 2
                        ? 'code too short to strip safely'
                        : 'suffix is not a plain 2-char grade code (e.g. single-digit grade like SOCSCI7, or MATH3 vs MATH03) — review manually',
                ];
                continue;
            }

            $newCode = substr($oldCode, 0, -2);
            $groups[$newCode][] = $row;
        }

        foreach ($groups as $newCode => $groupRows) {
            // Sub-group by normalized title.
            $byTitle = [];
            foreach ($groupRows as $row) {
                $titleKey = Str::of((string) $row->subject_title)->upper()->trim()->__toString();
                $byTitle[$titleKey][] = $row;
            }

            if (count($byTitle) === 1) {
                // All rows under this code share the same title.
                $titleRows = array_values($byTitle)[0];
                usort($titleRows, fn($a, $b) => $a->id <=> $b->id);

                $survivor = array_shift($titleRows); // lowest id kept
                $renames[] = [
                    'id' => $survivor->id,
                    'old_code' => $survivor->subject_code,
                    'new_code' => $newCode,
                    'subject_title' => $survivor->subject_title,
                ];

                foreach ($titleRows as $dupe) {
                    $deletes[] = [
                        'id' => $dupe->id,
                        'old_code' => $dupe->subject_code,
                        'survivor_id' => $survivor->id,
                        'survivor_new_code' => $newCode,
                        'subject_title' => $dupe->subject_title,
                    ];
                }
                continue;
            }

            // Different titles collapsing onto the same stripped code — a genuine
            // conflict, not a duplicate. Largest sub-group keeps the clean code.
            uasort($byTitle, fn($a, $b) => count($b) <=> count($a));
            $titleGroupsInOrder = array_values($byTitle);

            $primaryRows = $titleGroupsInOrder[0];
            usort($primaryRows, fn($a, $b) => $a->id <=> $b->id);
            $primarySurvivor = array_shift($primaryRows);

            $renames[] = [
                'id' => $primarySurvivor->id,
                'old_code' => $primarySurvivor->subject_code,
                'new_code' => $newCode,
                'subject_title' => $primarySurvivor->subject_title,
            ];
            foreach ($primaryRows as $dupe) {
                $deletes[] = [
                    'id' => $dupe->id,
                    'old_code' => $dupe->subject_code,
                    'survivor_id' => $primarySurvivor->id,
                    'survivor_new_code' => $newCode,
                    'subject_title' => $dupe->subject_title,
                ];
            }

            $conflictEntry = [
                'stripped_code' => $newCode,
                'kept_for' => $primarySurvivor->subject_title,
                'rows' => [],
            ];

            $suffixCounter = 2;
            for ($i = 1; $i < count($titleGroupsInOrder); $i++) {
                $subRows = $titleGroupsInOrder[$i];
                usort($subRows, fn($a, $b) => $a->id <=> $b->id);
                $subSurvivor = array_shift($subRows);

                $disambiguatedCode = $newCode . $suffixCounter;
                $suffixCounter++;

                $conflictEntry['rows'][] = [
                    'id' => $subSurvivor->id,
                    'old_code' => $subSurvivor->subject_code,
                    'new_code' => $disambiguatedCode,
                    'subject_title' => $subSurvivor->subject_title,
                ];

                foreach ($subRows as $dupe) {
                    $deletes[] = [
                        'id' => $dupe->id,
                        'old_code' => $dupe->subject_code,
                        'survivor_id' => $subSurvivor->id,
                        'survivor_new_code' => $disambiguatedCode,
                        'subject_title' => $dupe->subject_title,
                    ];
                }
            }

            $conflicts[] = $conflictEntry;
        }

        $collisions = $this->resolveCodeCollisions($renames, $conflicts);

        return [
            'total_rows' => $rows->count(),
            'renames'    => $renames,
            'deletes'    => $deletes,
            'conflicts'  => $conflicts,
            'irregular'  => $irregular,
            'code_collisions' => $collisions,
        ];
    }

    /**
     * subject_code is unique() at the DB level. Walk every planned code
     * assignment (renames, then conflict rows) in order; if a code has
     * already been claimed, fall back to "{code}_{id}" and log it instead
     * of letting the transaction fail on a constraint violation.
     * Mutates $renames / $conflicts in place via reference.
     */
    private function resolveCodeCollisions(array &$renames, array &$conflicts): array
    {
        $used = [];
        $collisions = [];

        foreach ($renames as &$rename) {
            $rename['new_code'] = $this->dedupeAgainst($rename['new_code'], $rename['id'], $used, $collisions);
        }
        unset($rename);

        foreach ($conflicts as &$conflict) {
            foreach ($conflict['rows'] as &$row) {
                $row['new_code'] = $this->dedupeAgainst($row['new_code'], $row['id'], $used, $collisions);
            }
            unset($row);
        }
        unset($conflict);

        return $collisions;
    }

    private function dedupeAgainst(string $code, int $id, array &$used, array &$collisions): string
    {
        if (!isset($used[$code])) {
            $used[$code] = $id;
            return $code;
        }

        $fallback = $code . '_' . $id;
        $used[$fallback] = $id;
        $collisions[] = [
            'id' => $id,
            'wanted_code' => $code,
            'assigned_code' => $fallback,
            'reason' => "code '{$code}' already claimed by subjects.id {$used[$code]}",
        ];

        return $fallback;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * A "clean" 2-char suffix is either two digits (01, 07, 10 truncated to
     * "10"'s last 2 = "10") or two letters (PR, JR). This rejects mixed/odd
     * suffixes like a single trailing digit with no padding (SOCSCI7) so
     * they get flagged instead of silently mangled.
     */
    private function hasCleanTwoCharSuffix(string $code): bool
    {
        $suffix = substr($code, -2);
        $stem   = substr($code, 0, -2);

        if ($stem === '') {
            return false;
        }

        // both digits, or both letters — reject mixed (e.g. "I7")
        $bothDigits = ctype_digit($suffix);
        $bothLetters = ctype_alpha($suffix);

        return $bothDigits || $bothLetters;
    }

    private function conn()
    {
        return self::CONNECTION ? DB::connection(self::CONNECTION) : DB::connection();
    }
}