<?php

namespace App\Http\Controllers\Migration;

use App\Models\Config\CardTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SchoolYearMigrationController extends BaseMigrationController
{
    /**
     * Migrates `schoolyear` from old MySQL → `school_years` + `school_year_infos` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/school-years/preview  → preview()
     *   POST   /migrate/school-years          → migrate()
     *   DELETE /migrate/school-years/reset    → reset()
     *
     * school_years column mapping (old → new):
     *   schoolyear         → school_year
     *   schoolyear_start   → month_start       (int, preserved)
     *   schoolyear_end     → month_end         (int, preserved)
     *   active (varchar)   → active            tinyInteger via coerceActive()
     *   school_days (JSON) → attendance_entries count of array entries (fallback 10 if null)
     *   school_days (JSON) → (used for school_year_infos — see below)
     *   (none)             → timestamps
     *
     * school_year_infos mapping (old school_days JSON → new rows):
     *   school_days is a JSON array e.g. ["49", "48", "47", "44"]
     *   Each element becomes one school_year_infos row:
     *     index + 1 → coverage  (1-based)
     *     value     → days
     *
     *   Example:
     *     ["49", "48", "47", "44"] →
     *       {school_year_id: X, coverage: 1, days: 49}
     *       {school_year_id: X, coverage: 2, days: 48}
     *       {school_year_id: X, coverage: 3, days: 47}
     *       {school_year_id: X, coverage: 4, days: 44}
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $sample = DB::connection('old_db')->table('schoolyear')->limit(3)->get();

        return response()->json([
            'table'              => 'school_years',
            'old_count'          => DB::connection('old_db')->table('schoolyear')->count(),
            'new_count'          => DB::connection('new_db')->table('school_years')->count(),
            'new_infos_count'    => DB::connection('new_db')->table('school_year_infos')->count(),
            'sample_school_year' => $sample->map(fn($r) => $this->mapRow($r)),
            'sample_infos'       => $sample->flatMap(fn($r) => $this->expandSchoolDays($r, 0)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $schoolYearStats = $this->runMigration(
            newTable: 'school_years',
            oldTable: 'schoolyear',
            orderColumn: 'entry_id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 8,
            dedupeKey: 'schoolyear'
        );

        // After school_years are inserted, migrate school_year_infos
        $infoStats = $this->migrateSchoolYearInfos();

        // Seed school_year_card_templates — 3 rows per school year
        $cardTemplateStats = $this->migrateCardTemplates();

        return response()->json([
            'status' => (empty($schoolYearStats['errors']) && empty($infoStats['errors']) && empty($cardTemplateStats['errors']))
                ? 'done'
                : 'done_with_errors',
            'school_years'               => $schoolYearStats,
            'school_year_infos'          => $infoStats,
            'school_year_card_templates' => $cardTemplateStats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset — clears both tables (infos first due to FK)
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $cardTemplatesDeleted = DB::connection('new_db')->table('school_year_card_templates')->delete();
        $infosDeleted         = DB::connection('new_db')->table('school_year_infos')->delete();
        $schoolYearDeleted    = DB::connection('new_db')->table('school_years')->delete();

        DB::connection('new_db')->table('school_year_card_templates')->truncate();
        DB::connection('new_db')->table('school_year_infos')->truncate();
        DB::connection('new_db')->table('school_years')->truncate();

        return response()->json([
            'status'                           => 'reset',
            'school_years_deleted'             => $schoolYearDeleted,
            'school_year_infos_deleted'        => $infosDeleted,
            'school_year_card_templates_deleted' => $cardTemplatesDeleted,
        ]);
    }

    // -------------------------------------------------------------------------
    // school_years row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'label'              => $row->schoolyear,
            'month_start'        => (string) ($row->schoolyear_start ?? 1),
            'month_end'          => (string) ($row->schoolyear_end   ?? 12),
            'active'             => $this->coerceActive($row->active),
            'attendance_entries' => count(json_decode($row->school_days ?? '[]', associative: true) ?: []) ?: 10,
            'created_at'         => $now,
            'updated_at'         => $now,
        ];
        // 8 columns — keep in sync with columnCount above
    }

    // -------------------------------------------------------------------------
    // school_year_infos migration
    // -------------------------------------------------------------------------

    /**
     * Reads school_days JSON from each old schoolyear row, resolves the new
     * school_year_id, then inserts one school_year_infos row per array element.
     *
     * school_days JSON: ["49", "48", "47", "44"]
     * → coverage 1 = 49 days, coverage 2 = 48 days, etc.
     */
    private function migrateSchoolYearInfos(): array
    {
        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        // Build school_year string → new school_years.id map
        $schoolYearIdMap = DB::connection('new_db')
            ->table('school_years')
            ->pluck('id', 'label')
            ->all();

        DB::connection('new_db');//->statement('PRAGMA foreign_keys = OFF');

        try {
            DB::connection('new_db')->transaction(function () use (&$stats, $schoolYearIdMap) {

                DB::connection('old_db')
                    ->table('schoolyear')
                    ->whereNotNull('school_days')
                    ->orderBy('entry_id')
                    ->chunk(self::CHUNK_SIZE, function ($rows) use (&$stats, $schoolYearIdMap) {

                        $toInsert = [];

                        foreach ($rows as $row) {
                            $schoolYearId = $schoolYearIdMap[$row->schoolyear] ?? null;

                            if (! $schoolYearId) {
                                $stats['errors'][] = [
                                    'row'     => $row->schoolyear,
                                    'message' => "school_year_id not found for [{$row->schoolyear}]",
                                ];
                                continue;
                            }

                            $expanded = $this->expandSchoolDays($row, $schoolYearId);

                            foreach ($expanded as $infoRow) {
                                // Dedupe by unique constraint: school_year_id + coverage
                                $exists = DB::connection('new_db')
                                    ->table('school_year_infos')
                                    ->where('school_year_id', $infoRow['school_year_id'])
                                    ->where('coverage',       $infoRow['coverage'])
                                    ->exists();

                                if ($exists) {
                                    $stats['skipped']++;
                                    continue;
                                }

                                $toInsert[] = $infoRow;
                            }
                        }

                        if (empty($toInsert)) {
                            return;
                        }

                        // 4 columns: school_year_id, coverage, days, created_at, updated_at = 5
                        $batchSize = (int) floor(999 / 5);

                        foreach (array_chunk($toInsert, $batchSize) as $batch) {
                            try {
                                DB::connection('new_db')->table('school_year_infos')->insert($batch);
                                $stats['inserted'] += count($batch);
                            } catch (\Throwable $e) {
                                foreach ($batch as $infoRow) {
                                    try {
                                        DB::connection('new_db')->table('school_year_infos')->insert($infoRow);
                                        $stats['inserted']++;
                                    } catch (\Throwable $rowError) {
                                        $stats['errors'][] = [
                                            'row'     => "sy:{$infoRow['school_year_id']} cov:{$infoRow['coverage']}",
                                            'message' => $rowError->getMessage(),
                                        ];
                                    }
                                }
                            }
                        }
                    });
            });
        } finally {
            DB::connection('new_db');//->statement('PRAGMA foreign_keys = ON');
        }

        return $stats;
    }

    // -------------------------------------------------------------------------
    // Expand school_days JSON → school_year_infos rows
    // -------------------------------------------------------------------------

    /**
     * Converts ["49", "48", "47", "44"] into:
     *   [{school_year_id: X, coverage: 1, days: 49, ...},
     *    {school_year_id: X, coverage: 2, days: 48, ...}, ...]
     *
     * @param  object $row          Old schoolyear row
     * @param  int    $schoolYearId Resolved new school_years.id (0 for preview)
     * @return array
     */
    private function expandSchoolDays(object $row, int $schoolYearId): array
    {
        $decoded = json_decode($row->school_days ?? '[]', associative: true);

        if (! is_array($decoded) || empty($decoded)) {
            return [];
        }

        $now  = now()->toDateTimeString();
        $rows = [];

        foreach (array_values($decoded) as $index => $days) {
            $rows[] = [
                'school_year_id' => $schoolYearId,
                'coverage'       => $index + 1,       // 1-based index
                'days'           => (int) $days,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        return $rows;
        // 5 columns — keep in sync with batchSize calculation above
    }

    // -------------------------------------------------------------------------
    // school_year_card_templates seeder
    // -------------------------------------------------------------------------

    /**
     * For every school year in new_db, inserts 3 rows into school_year_card_templates:
     *   coverage 1 → card_template_id = 1, category = 'primary'
     *   coverage 2 → card_template_id = 2, category = 'secondary'
     *   coverage 3 → card_template_id = 3, category = 'prep'
     *
     * Idempotent — skips rows where school_year_id + category already exists.
     */
    private function migrateCardTemplates(): array
    {
        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        $templates = CardTemplate::all();

        $schoolYears = DB::connection('new_db')
            ->table('school_years')
            ->pluck('id')
            ->all();

        $now      = now()->toDateTimeString();
        $toInsert = [];

        foreach ($schoolYears as $schoolYearId) {
            foreach ($templates as $template) {
                // Dedupe by school_year_id + category
                $exists = DB::connection('new_db')
                    ->table('school_year_card_templates')
                    ->where('school_year_id', $schoolYearId)
                    // ->where('category',       $template->category)
                    ->exists();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                $toInsert[] = [
                    'school_year_id'   => $schoolYearId,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }

        if (empty($toInsert)) {
            return $stats;
        }

        // 5 columns: school_year_id, category, created_at, updated_at
        $batchSize = (int) floor(999 / 5);

        DB::connection('new_db')->transaction(function () use ($toInsert, $batchSize, &$stats) {
            foreach (array_chunk($toInsert, $batchSize) as $batch) {
                try {
                    DB::connection('new_db')->table('school_year_card_templates')->insert($batch);
                    $stats['inserted'] += count($batch);
                } catch (\Throwable $e) {
                    foreach ($batch as $row) {
                        try {
                            DB::connection('new_db')->table('school_year_card_templates')->insert($row);
                            $stats['inserted']++;
                        } catch (\Throwable $rowError) {
                            $stats['errors'][] = [
                                'row'     => "sy:{$row['school_year_id']}",// cat:{$row['category']}",
                                'message' => $rowError->getMessage(),
                            ];
                        }
                    }
                }
            }
        });

        return $stats;
    }
}
