<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EnrollmentMigrationController extends BaseMigrationController
{
    /**
     * Migrates `enrollment` from old MySQL → `enrollments` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/enrollments/preview  → preview()
     *   POST   /migrate/enrollments          → migrate()
     *   GET    /migrate/enrollments/unfilled → unfilled()
     *   DELETE /migrate/enrollments/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id        → student_id     (int, preserved)
     *   schoolyear        → school_year_id FK — resolveSchoolYear() from base
     *   gradelevel        → grade_level_id FK — resolveGradeLevel() local lookup
     *   section_or_strand → section_id     FK — resolveSection() local lookup
     *   (none)            → timestamps     now()
     *
     * All three FKs fall back to null on no match.
     * Call GET /migrate/enrollments/unfilled to list unresolved rows.
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table' => 'enrollments',
            'old_count' => DB::connection('old_db')->table('enrollment')->count(),
            'new_count' => DB::connection('new_db')->table('enrollments')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'grade_level_map' => $this->buildGradeLevelMap(),
            'section_map' => $this->buildSectionMap(),
            'sample' => DB::connection('old_db')
                ->table('enrollment')
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
        // Pre-load all lookup maps before chunking — one query each
        $this->buildSchoolYearMap();
        $this->buildGradeLevelMap();
        $this->buildSectionMap();

        $stats = $this->runMigration(
            newTable: 'enrollments',
            oldTable: 'enrollment',
            orderColumn: 'entry_id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 6, // student_id, school_year_id, grade_level_id, section_id, created_at, updated_at
            dedupeKey: 'entry_id'
        );

        $unfilled = DB::connection('new_db')
            ->table('enrollments')
            ->where(
                fn($q) => $q
                    ->whereNull('school_year_id')
                    ->orWhereNull('grade_level_id')
            )
            ->count();

        return response()->json([
            'status' => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled' => $unfilled,
            'note' => $unfilled > 0
                ? "{$unfilled} row(s) have unresolved FKs. Call GET /migrate/enrollments/unfilled."
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
            ->table('enrollments')
            ->where(
                fn($q) => $q
                    ->whereNull('school_year_id')
                    ->orWhereNull('grade_level_id')
            )
            ->select(['id', 'student_id', 'school_year_id', 'grade_level_id', 'section_id'])
            ->get();

        return response()->json([
            'count' => $rows->count(),
            'rows' => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('enrollments')->delete();
        DB::connection('new_db')->table('enrollments')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        if (!$row->gradelevel) {
            return [];
        }

        return [
            'student_id' => (int) $row->student_id,
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'grade_level_id' => $this->resolveGradeLevel($row->gradelevel ?? null),
            'section_id' => $this->resolveSection($row->section_or_strand ?? null),
            'created_at' => $row->created_at ?? $now,
            'updated_at' => $row->updated_at ?? $now,
        ];
        // 6 columns — keep in sync with columnCount above
    }

}
