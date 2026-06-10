<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeroomMigrationController extends BaseMigrationController
{
    /**
     * Migrates `homeroom` from old MySQL → `homerooms` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/homerooms/preview  → preview()
     *   POST   /migrate/homerooms          → migrate()
     *   GET    /migrate/homerooms/unfilled → unfilled()
     *   DELETE /migrate/homerooms/reset    → reset()
     *
     * Column mapping (old → new):
     *   advisory    → grade_level_id  FK int — resolveGradeLevel() via grade_levels.label
     *   schoolyear  → school_year_id  FK int — resolveSchoolYear() from base
     *   section     → section_id      FK int — looked up via sections.title
     *   user_id     → user_id         (int, preserved, nullable)
     *   user2_id    → user2_id        (int, preserved, nullable)
     *   (none)      → timestamps      now()
     *
     * ⚠ advisory examples: '7', 'Grade 7', 'Grade VII' — all normalised to label match.
     */

    private ?array $gradeLevelMap = null;
    private ?array $sectionMap    = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'homerooms',
            'old_count'       => DB::connection('old_db')->table('homeroom')->count(),
            'new_count'       => DB::connection('new_db')->table('homerooms')->count(),
            'grade_level_map' => $this->buildGradeLevelMap(),
            'section_map'     => $this->buildSectionMap(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                ->table('homeroom')
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
        $this->buildSectionMap();
        $this->buildSchoolYearMap(); // from BaseMigrationController

        $stats = $this->runMigration(
            newTable: 'homerooms',
            oldTable: 'homeroom',
            orderColumn: 'id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 7, // grade_level_id, school_year_id, section_id, user_id, user2_id, created_at, updated_at
            dedupeKey: 'id'
        );

        $unfilled = DB::connection('new_db')
            ->table('homerooms')
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
                ? "{$unfilled} row(s) have unresolved FKs. Call GET /migrate/homerooms/unfilled."
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
            ->table('homerooms')
            ->where(
                fn($q) => $q
                    ->whereNull('grade_level_id')
                    ->orWhereNull('school_year_id')
                    ->orWhereNull('section_id')
            )
            ->select(['id', 'grade_level_id', 'school_year_id', 'section_id', 'user_id'])
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
        $deleted = DB::connection('new_db')->table('homerooms')->delete();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        if (!$row->user_id) {
            return [];
        }

        return [
            'grade_level_id' => $this->resolveGradeLevel($row->advisory ?? null),
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null), // from base
            'section_id'     => $this->resolveSection($row->section ?? null),
            'user_id'        => $this->resolveUser($row->user_id  ?? null),
            'user2_id'       => $this->resolveUser($row->user2_id ?? null),
            'created_at'     => $now,
            'updated_at'     => $now,
        ];
        // 7 columns — keep in sync with columnCount above
    }
}
