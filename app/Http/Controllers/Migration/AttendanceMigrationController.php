<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class AttendanceMigrationController extends BaseMigrationController
{
    /**
     * Migrates `attendance` from old MySQL → `attendances` in new SQLite.
     *
     * The old schema stores one row per student per school year with a JSON
     * column containing 12 months of data. The new schema stores one row per
     * student per school year per month — so one old row explodes into up to
     * 12 new rows.
     *
     * Routes:
     *   GET    /migrate/attendances/preview  → preview()
     *   POST   /migrate/attendances          → migrate()
     *   DELETE /migrate/attendances/reset    → reset()
     *
     * Old JSON structure:
     *   {"1": {"A": 0, "T": 1}, "2": {"A": 0, "T": 0}, ... "12": {"A": 0, "T": 2}}
     *   Key   = month number (1–12)
     *   A     = absences   → days_absent  (⚠ schema needs days_absent not days_present)
     *   T     = tardiness  → days_tardiness
     *
     * Column mapping (old → new, one month per row):
     *   student_id  → student_id     (int, preserved)
     *   schoolyear  → school_year_id FK int — resolveSchoolYear() from base
     *   JSON key    → month          (tinyInteger 1–12)
     *   JSON A      → days_absent    (decimal 5,1)
     *   JSON T      → days_tardiness (decimal 5,1)
     *   (none)      → timestamps     preserved from old row
     *
     * ⚠ Migration note:
     *   Add `days_absent` to your attendances migration instead of `days_present`:
     *
     *   $table->decimal('days_absent', 5, 1)->default(0);
     */

    // Dedupe key for attendances: composite of student_id + school_year_id + month
    // Since runMigration() uses a single column for dedupe, we handle it manually here.

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $oldRow = DB::connection('old_db')->table('attendance')->first();

        return response()->json([
            'table'           => 'attendances',
            'old_row_count'   => DB::connection('old_db')->table('attendance')->count(),
            'new_row_count'   => DB::connection('new_db')->table('attendances')->count(),
            'expected_new'    => DB::connection('old_db')->table('attendance')->count() * 12,
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample_expanded' => $oldRow ? $this->expandRow($oldRow) : [],
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $this->buildSchoolYearMap(); // from BaseMigrationController

        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        DB::connection('new_db')->statement('PRAGMA foreign_keys = OFF');

        try {
            DB::connection('new_db')->transaction(function () use (&$stats) {

                DB::connection('old_db')
                    ->table('attendance')
                    ->orderBy('entry_id')
                    ->chunk(self::CHUNK_SIZE, function ($rows) use (&$stats) {

                        $toInsert = [];

                        foreach ($rows as $row) {
                            try {
                                $expanded = $this->expandRow($row);

                                foreach ($expanded as $monthRow) {
                                    // Dedupe: skip if student + school_year + month already exists
                                    $exists = DB::connection('new_db')
                                        ->table('attendances')
                                        ->where('student_id',     $monthRow['student_id'])
                                        ->where('school_year_id', $monthRow['school_year_id'])
                                        ->where('month',          $monthRow['month'])
                                        ->exists();

                                    if ($exists) {
                                        $stats['skipped']++;
                                        continue;
                                    }

                                    $toInsert[] = $monthRow;
                                }
                            } catch (Throwable $e) {
                                $stats['errors'][] = [
                                    'row'     => $row->entry_id,
                                    'message' => $e->getMessage(),
                                ];
                            }
                        }

                        if (empty($toInsert)) {
                            return;
                        }

                        // 6 columns per row: student_id, school_year_id, month,
                        // days_absent, days_tardiness, created_at, updated_at = 7
                        $batchSize = (int) floor(999 / 7);

                        foreach (array_chunk($toInsert, $batchSize) as $batch) {
                            try {
                                DB::connection('new_db')->table('attendances')->insert($batch);
                                $stats['inserted'] += count($batch);
                            } catch (Throwable $e) {
                                foreach ($batch as $mapped) {
                                    try {
                                        DB::connection('new_db')->table('attendances')->insert($mapped);
                                        $stats['inserted']++;
                                    } catch (Throwable $rowError) {
                                        $stats['errors'][] = [
                                            'row'     => $mapped['student_id'] . '/month' . $mapped['month'],
                                            'message' => $rowError->getMessage(),
                                        ];
                                    }
                                }
                            }
                        }
                    });
            });
        } finally {
            DB::connection('new_db')->statement('PRAGMA foreign_keys = ON');
        }

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'note'     => '',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('attendances')->delete();
        DB::connection('new_db')->table('attendances')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Expand one old row → up to 12 new rows (one per month)
    // -------------------------------------------------------------------------

    /**
     * Decodes the JSON attendance column and maps each month entry
     * to a flat row matching the new attendances schema.
     *
     * Skips months where both A and T are 0 AND the key is missing —
     * but preserves months explicitly set to 0 (student was present all month).
     */
    private function expandRow(object $row): array
    {
        $now           = now()->toDateTimeString();
        $schoolYearId  = $this->resolveSchoolYear($row->schoolyear ?? null);
        $attendance    = json_decode($row->attendance ?? '{}', associative: true);
        $expanded      = [];

        if (! is_array($attendance)) {
            return [];
        }

        foreach ($attendance as $month => $values) {
            $monthInt = (int) $month;

            if ($monthInt < 1 || $monthInt > 12) {
                continue; // skip malformed keys
            }

            $expanded[] = [
                'student_id'      => (int) $row->student_id,
                'school_year_id'  => $schoolYearId,
                'month'           => $monthInt,
                'days_absent'     => (float) ($values['A'] ?? 0),
                'days_tardiness'  => (float) ($values['T'] ?? 0),
                'created_at'      => $row->created_at ?? $now,
                'updated_at'      => $row->updated_at ?? $now,
            ];
        }

        return $expanded;
        // 7 columns — keep in sync with batchSize calculation above
    }
}
