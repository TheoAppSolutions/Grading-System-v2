<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            'created_at' => $now,
            'updated_at' => $now,
        ];
        // 6 columns — keep in sync with columnCount above
    }

    // -------------------------------------------------------------------------
    // Sync current_level
    // -------------------------------------------------------------------------

    /**
     * After enrollment migration, call this to backfill students.current_level.
     *
     * Strategy:
     *   1. For each student, find their latest enrollment row by the highest
     *      school_year_id value (higher id = more recent school year).
     *   2. Take that enrollment's grade_level_id.
     *   3. Update students.current_level with that value.
     *
     * Route:
     *   POST /migrate/enrollments/sync-current-level
     *
     * Safe to re-run — uses upsert-style update, not insert.
     * Students with no enrollment rows are left untouched (current_level stays null).
     */
    public function syncCurrentLevels(): JsonResponse
    {
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Fetch the latest enrollment per student:
        // latest = enrollment with the highest school_year_id for that student.
        // We use a subquery to get max(school_year_id) per student_id, then
        // join back to get the full row (to access grade_level_id).
        $latestEnrollments = DB::connection('new_db')
            ->table('enrollments as e')
            ->joinSub(
                DB::connection('new_db')
                    ->table('enrollments')
                    ->select('student_id', DB::raw('MAX(school_year_id) as max_sy_id'))
                    ->whereNotNull('grade_level_id')
                    ->groupBy('student_id'),
                'latest',
                fn($join) => $join
                    ->on('e.student_id', '=', 'latest.student_id')
                    ->on('e.school_year_id', '=', 'latest.max_sy_id')
            )
            ->select('e.student_id', 'e.grade_level_id')
            ->get();

        foreach ($latestEnrollments as $enrollment) {
            if ($enrollment->grade_level_id === null) {
                $skipped++;

                continue;
            }

            try {
                $affected = DB::connection('new_db')
                    ->table('students')
                    ->where('id', $enrollment->student_id)
                    ->update(['current_level' => $enrollment->grade_level_id]);

                $affected ? $updated++ : $skipped++;
            } catch (Throwable $e) {
                $errors[] = [
                    'student_id' => $enrollment->student_id,
                    'message' => $e->getMessage(),
                ];
                Log::error('syncCurrentLevels error', [
                    'student_id' => $enrollment->student_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => empty($errors) ? 'done' : 'done_with_errors',
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }
}
