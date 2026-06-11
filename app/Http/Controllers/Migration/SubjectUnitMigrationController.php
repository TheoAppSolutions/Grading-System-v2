<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SubjectUnitMigrationController extends BaseMigrationController
{
    /**
     * Migrates `subjects_units` from old MySQL → `subject_units` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/subject-units/preview  → preview()
     *   POST   /migrate/subject-units          → migrate()
     *   DELETE /migrate/subject-units/reset    → reset()
     *
     * Column mapping (old → new):
     *   subject_id  → subject_id    (preserved as-is, nullable)
     *   units       → units         (float preserved)
     *   schoolyear  → school_year_id via lookup on new_db.school_years.school_year
     *   (none)      → timestamps    now()
     *
     * ⚠ school_year_id:
     *   Same lookup strategy as SubjectUserMigrationController.
     */

    private ?array $schoolYearMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        

        return response()->json([
            'table'           => 'subject_units',
            'old_count'       => DB::connection('old_db')->table('subjects_units')->count(),
            'new_count'       => DB::connection('new_db')->table('subject_units')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('subjects_units')
                                        ->limit(3)
                                        ->get()
                                        ->map(fn ($r) => $this->mapRow($r)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        
        $this->buildSchoolYearMap();

        $stats = $this->runMigration(
            newTable:    'subject_units',
            oldTable:    'subjects_units',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 5, // subject_id, units, school_year_id, created_at, updated_at
            dedupeKey:   'entry_id'
        );

        return response()->json([
            'status'                  => empty($stats['errors']) ? 'done' : 'done_with_errors',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('subject_units')->delete();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'subject_id'     => $row->subject_id ?? null,
            'units'          => (float) $row->units,
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 5 columns — keep in sync with columnCount above
    }
}
