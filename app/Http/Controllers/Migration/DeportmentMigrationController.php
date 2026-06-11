<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeportmentMigrationController extends BaseMigrationController
{
    /**
     * Migrates `deportment` from old MySQL → `deportments` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/deportments/preview  → preview()
     *   POST   /migrate/deportments          → migrate()
     *   DELETE /migrate/deportments/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id     (int, preserved)
     *   user_id     → user_id        FK int — preserved
     *   deportment  → deportment     (float, preserved, nullable)
     *   quarter     → quarter        (int → tinyInteger, compatible)
     *   schoolyear  → school_year_id FK int — resolveSchoolYear() from base
     *   (none)      → timestamps     now()
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'deportments',
            'old_count'       => DB::connection('old_db')->table('deportment')->count(),
            'new_count'       => DB::connection('new_db')->table('deportments')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('deportment')
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
        $this->buildSchoolYearMap(); // from BaseMigrationController

        $stats = $this->runMigration(
            newTable:    'deportments',
            oldTable:    'deportment',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 7, // student_id, user_id, school_year_id, deportment, quarter, created_at, updated_at
            dedupeKey:   'entry_id'
        );

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('deportments')->delete();
        DB::connection('new_db')->table('deportments')->truncate();

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
            'user_id'        => (int) $row->user_id,
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null), // from base
            'deportment'     => $row->deportment !== null ? (float) $row->deportment : null,
            'quarter'        => (int) $row->quarter,
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 7 columns — keep in sync with columnCount above
    }
}
