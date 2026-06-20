<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeMigrationController extends BaseMigrationController
{
    /**
     * Migrates grades_1 through grades_12 from old MySQL → `grades` in new SQLite.
     *
     * Run one table at a time via:
     *   POST /migrate/grades/table/{num}   e.g. /migrate/grades/table/1
     *
     * Or migrate all tables via individual calls from the frontend/script.
     *
     * Routes:
     *   GET    /migrate/grades/preview         → preview()
     *   POST   /migrate/grades/table/{num}     → migrateTable()   ← one table per request
     *   GET    /migrate/grades/status          → status()
     *   GET    /migrate/grades/unfilled        → unfilled()
     *   DELETE /migrate/grades/reset           → reset()
     *   DELETE /migrate/grades/reset/{num}     → resetTable()
     */
    private const GRADE_TABLES = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    private const COLUMN_COUNT = 13;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $counts = [];
        foreach (self::GRADE_TABLES as $num) {
            $counts["grades_{$num}"] = DB::connection('old_db')->table("grades_{$num}")->count();
        }

        return response()->json([
            'new_table' => 'grades',
            'new_count' => DB::connection('new_db')->table('grades')->count(),
            'old_counts' => $counts,
            'old_total' => array_sum($counts),
            'grade_level_map' => $this->buildGradeLevelMap(),
            'subject_map' => $this->buildSubjectMap(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample' => DB::connection('old_db')
                ->table('grades_1')
                ->limit(3)
                ->get()
                ->map(fn ($r) => $this->mapRow($r, 1)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate a single table — call this per request to avoid timeout
    // -------------------------------------------------------------------------

    /**
     * Migrates one grades_N table per HTTP request.
     *
     * Usage:
     *   POST /migrate/grades/table/1
     *   POST /migrate/grades/table/2
     *   ...
     *   POST /migrate/grades/table/12
     *
     * Each call is independent and idempotent — re-running skips already
     * inserted rows based on entry_id dedupe.
     */
    public function migrateTable(int $num): JsonResponse
    {
        if (! in_array($num, self::GRADE_TABLES, strict: true)) {
            return response()->json([
                'error' => "Invalid table number [{$num}]. Must be 1–12.",
            ], 422);
        }

        // Extend execution time for this single-table request
        set_time_limit(300);

        $this->buildGradeLevelMap();
        $this->buildSubjectMap();
        $this->buildSchoolYearMap();

        $gradeLevelId = $this->resolveGradeLevel($num);
        if (config('app.name') === 'HCGS') {
            if ($num === 11) {
                $num = 'kindy';
            } elseif ($num === 12) {
                $num = 'prep';
            }
        }
// RE CHECK GRADES MIGRATION ON BIS
        $oldTable = "grades_{$num}";

        $stats = $this->runMigration(
            newTable: 'grades',
            oldTable: $oldTable,
            orderColumn: 'entry_id',
            mapper: fn ($r) => $this->mapRow($r, $num, $gradeLevelId),
            columnCount: self::COLUMN_COUNT,
            dedupeKey: 'entry_id'
        );

        $remaining = array_filter(
            self::GRADE_TABLES,
            fn ($n) => $n > $num
        );

        return response()->json([
            'status' => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'table' => $oldTable,
            'grade_level_id' => $gradeLevelId,
            'next' => ! empty($remaining)
                ? 'POST /migrate/grades/table/'.min($remaining)
                : null,
            'all_done' => empty($remaining),
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Status — how many rows per table have been migrated so far
    // -------------------------------------------------------------------------

    public function status(): JsonResponse
    {
        $rows = [];
        foreach (self::GRADE_TABLES as $num) {
            $gradeLevelId = $this->resolveGradeLevel($num);
            $oldCount = DB::connection('old_db')->table("grades_{$num}")->count();
            $newCount = $gradeLevelId
                ? DB::connection('new_db')->table('grades')->where('grade_level_id', $gradeLevelId)->count()
                : 0;

            $rows[] = [
                'table' => "grades_{$num}",
                'grade_level_id' => $gradeLevelId,
                'old_count' => $oldCount,
                'migrated' => $newCount,
                'remaining' => max(0, $oldCount - $newCount),
                'done' => $newCount >= $oldCount,
            ];
        }

        $totalOld = array_sum(array_column($rows, 'old_count'));
        $totalMigrated = array_sum(array_column($rows, 'migrated'));

        return response()->json([
            'total_old' => $totalOld,
            'total_migrated' => $totalMigrated,
            'total_remaining' => max(0, $totalOld - $totalMigrated),
            'all_done' => $totalMigrated >= $totalOld,
            'tables' => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // Unfilled
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('grades')
            ->where(
                fn ($q) => $q
                    ->whereNull('subject_id')
                    ->orWhereNull('school_year_id')
            )
            ->select(['id', 'student_id', 'subject_id', 'grade_level_id', 'school_year_id', 'quarter'])
            ->get();

        return response()->json([
            'count' => $rows->count(),
            'rows' => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset all
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('grades')->delete();
        DB::connection('new_db')->table('grades')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Reset a single grade level (useful when re-running one table)
    // -------------------------------------------------------------------------

    public function resetTable(int $num): JsonResponse
    {
        if (! in_array($num, self::GRADE_TABLES, strict: true)) {
            return response()->json([
                'error' => "Invalid table number [{$num}]. Must be 1–12.",
            ], 422);
        }

        $gradeLevelId = $this->resolveGradeLevel($num);

        if (! $gradeLevelId) {
            return response()->json([
                'error' => "Could not resolve grade_level_id for grades_{$num}.",
            ], 422);
        }

        $deleted = DB::connection('new_db')
            ->table('grades')
            ->where('grade_level_id', $gradeLevelId)
            ->delete();

        return response()->json([
            'status' => 'reset',
            'table' => "grades_{$num}",
            'grade_level_id' => $gradeLevelId,
            'deleted' => $deleted,
        ]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow($row, $tableNum, $gradeLevelId = null): array
    {
        $now = now()->toDateTimeString();

        return [
            'student_id' => (int) $row->student_id,
            'user_id' => (int) $this->resolveUser($row->user_id ?? null),
            'subject_id' => $this->resolveSubject($row->subject_id ?? null),
            'grade_level_id' => $gradeLevelId ?? $this->resolveGradeLevel($tableNum),
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'quarter' => (int) $row->quarter,

            // Old schema defaulted 0 meaning "not yet graded" — map to null
            'midquarter' => ($row->midquarter != 0) ? (float) $row->midquarter : null,
            'final' => ($row->final != 0) ? (float) $row->final : null,
            'effort' => ($row->effort != 0) ? (float) $row->effort : null,
            'deportment' => ($row->deportment != 0) ? (float) $row->deportment : null,

            'remarks' => isset($row->remarks) ? $row->remarks : null,
            'created_at' => $row->created_at ?? $now,
            'updated_at' => $row->updated_at ?? $now,
        ];
        // 13 columns — keep in sync with COLUMN_COUNT above
    }
}
