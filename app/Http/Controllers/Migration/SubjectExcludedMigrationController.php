<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SubjectExcludedMigrationController extends BaseMigrationController
{
    /**
     * Migrates `subjects_excluded` from old MySQL → `subject_excluded` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/subject-excluded/preview  → preview()
     *   POST   /migrate/subject-excluded          → migrate()
     *   GET    /migrate/subject-excluded/unfilled → unfilledSchoolYears()
     *   DELETE /migrate/subject-excluded/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id    (int, preserved)
     *   subject_id  → subject_id    (string, preserved)
     *   schoolyear  → school_year_id via lookup on new_db.school_years.school_year
     *   (none)      → timestamps    now()
     *
     * ⚠ school_year_id:
     *   Same lookup strategy as other subject controllers.
     *   Unmatched rows get null. Call GET /migrate/subject-excluded/unfilled to list them.
     */

    private ?array $schoolYearMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {


        return response()->json([
            'table'           => 'subject_excluded',
            'old_count'       => DB::connection('old_db')->table('subjects_excluded')->count(),
            'new_count'       => DB::connection('new_db')->table('subject_excluded')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                ->table('subjects_excluded')
                ->limit(3)
                ->get()
                ->map(fn($r) => $this->mapRow($r)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {

        $this->buildSchoolYearMap();

        $stats = $this->runMigration(
            newTable: 'subject_excluded',
            oldTable: 'subjects_excluded',
            orderColumn: 'entry_id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 5, // student_id, subject_id, school_year_id, created_at, updated_at
            dedupeKey: 'entry_id'
        );

        $unfilled = DB::connection('new_db')
            ->table('subject_excluded')
            ->whereNull('school_year_id')
            ->count();

        return response()->json([
            'status'                  => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled_school_year_id' => $unfilled,
            'note'                    => $unfilled > 0
                ? "{$unfilled} row(s) have null school_year_id. Call GET /migrate/subject-excluded/unfilled."
                : 'All school_year_id values resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('subject_excluded')->delete();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'student_id'     => (int) $row->student_id,
            'subject_id'     => $row->subject_id,
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'created_at'     => $now,
            'updated_at'     => $now,
        ];
        // 5 columns — keep in sync with columnCount above
    }
}
