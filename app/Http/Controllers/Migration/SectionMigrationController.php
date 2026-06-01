<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SectionMigrationController extends BaseMigrationController
{
    /**
     * Migrates `sections` from old MySQL → `sections` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/sections/preview  → preview()
     *   POST   /migrate/sections          → migrate()
     *   GET    /migrate/sections/unfilled → unfilled()
     *   DELETE /migrate/sections/reset    → reset()
     *
     * Column mapping (old → new):
     *   gradelevel  → grade_level_id  FK — looked up via grade_levels.label
     *   section     → section         (preserved as-is)
     *   schoolyear  → school_year_id  FK — resolveSchoolYear() from base
     *   (none)      → timestamps      now()
     *
     * ⚠ Run BEFORE student_sections and enrollments —
     *   those tables resolve section_id by looking up sections.title.
     *   school_years must also be migrated before this.
     */

    private ?array $gradeLevelMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'sections',
            'old_count'       => DB::connection('old_db')->table('sections')->count(),
            'new_count'       => DB::connection('new_db')->table('sections')->count(),
            'grade_level_map' => $this->buildGradeLevelMap(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                ->table('sections')
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
        $this->buildGradeLevelMap();
        $this->buildSchoolYearMap(); // from BaseMigrationController

        $stats = $this->runMigration(
            newTable: 'sections',
            oldTable: 'sections',
            orderColumn: 'entry_id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 5, // grade_level_id, school_year_id, section, created_at, updated_at
            dedupeKey: 'entry_id'
        );

        $unfilled = DB::connection('new_db')
            ->table('sections')
            ->where(
                fn($q) => $q
                    ->whereNull('grade_level_id')
                    ->orWhereNull('school_year_id')
            )
            ->count();

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled' => $unfilled,
            'note'     => $unfilled > 0
                ? "{$unfilled} row(s) have unresolved FKs. Call GET /migrate/sections/unfilled."
                : 'All FKs resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // List rows with any unresolved FK
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('sections')
            ->where(
                fn($q) => $q
                    ->whereNull('grade_level_id')
                    ->orWhereNull('school_year_id')
            )
            ->select(['id', 'section', 'grade_level_id', 'school_year_id'])
            ->get();

        return response()->json([
            'count' => $rows->count(),
            'rows'  => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('sections')->delete();
        DB::connection('new_db')->table('sections')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'grade_level_id' => $this->resolveGradeLevel($row->gradelevel ?? null),
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'section'        => $row->section,
            'created_at'     => $now,
            'updated_at'     => $now,
        ];
        // 5 columns — keep in sync with columnCount above
    }
}
