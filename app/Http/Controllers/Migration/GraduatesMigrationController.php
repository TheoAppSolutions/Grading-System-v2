<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Helpers\MigrationTools;
use App\Models\SchoolYear;
use App\Models\Graduate;
use App\Models\Enrollment;

class GraduatesMigrationController extends BaseMigrationController
{
    /**
     * Migrates `graduates` from old MySQL → `graduates` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/graduates/preview  → preview()
     *   POST   /migrate/graduates          → migrate()
     *   GET    /migrate/graduates/unfilled → unfilled()
     *   DELETE /migrate/graduates/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id    → student_id     (int, preserved)
     *   graduated_at  → school_year_id FK int — resolveSchoolYear() from base
     *                   old: varchar like '2022-2023'
     *                   new: FK to school_years.school_year
     *   (none)        → timestamps     now()
     *
     * Note: `graduated_at` was used as a school year string in the old schema.
     * It maps directly to school_year_id via the same resolveSchoolYear() lookup.
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'graduates',
            'old_count'       => DB::connection('old_db')->table('graduates')->count(),
            'new_count'       => DB::connection('new_db')->table('graduates')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('graduates')
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

        // $stats = $this->runMigration(
        //     newTable:    'graduates',
        //     oldTable:    'graduates',
        //     orderColumn: 'entry_id',
        //     mapper:      fn ($r) => $this->mapRow($r),
        //     columnCount: 4, // student_id, school_year_id, created_at, updated_at
        //     dedupeKey:   'entry_id'
        // );

        // $unfilled = DB::connection('new_db')
        //     ->table('graduates')
        //     ->whereNull('school_year_id')
        //     ->count();
        
        $stats = $this->syncFromEnrollments();

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            // 'unfilled' => $unfilled,
            'inserted' => $stats,
            // 'note'     => $unfilled > 0
            //     ? "{$unfilled} row(s) have unresolved school_year_id. Call GET /migrate/graduates/unfilled."
            //     : 'All school_year_id values resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // List rows with unresolved school_year_id
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('graduates')
            ->whereNull('school_year_id')
            ->select(['id', 'student_id', 'school_year_id'])
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
        $deleted = DB::connection('new_db')->table('graduates')->delete();

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

            // graduated_at was a school year string e.g. '2022-2023'
            // resolved to school_year_id FK via school_years.school_year
            'school_year_id' => $this->resolveSchoolYear($row->graduated_at ?? null),

            'created_at'     => $now,
            'updated_at'     => $now,
        ];
        // 4 columns — keep in sync with columnCount above
    }

    public function syncFromEnrollments(): array
    {
        $latestSchoolYearId = SchoolYear::orderByDesc('label')->value('id');
        $lastGradeLevelId = MigrationTools::last_grade_level_id();
        $pastSchoolYearIds = SchoolYear::where('id', '!=', $latestSchoolYearId)->pluck('id');
        $existingGraduateIds = Graduate::pluck('student_id')->all();
        $candidates = Enrollment::where('grade_level_id', $lastGradeLevelId)
            ->whereIn('school_year_id', $pastSchoolYearIds)
            ->whereNotIn('student_id', $existingGraduateIds)
            ->get()
            ->groupBy('student_id');

        $inserted = 0;

        foreach ($candidates as $row) {
            $row = $row[0];
            Graduate::create([
                'student_id'     => $row->student_id,
                'school_year_id' => $row->school_year_id,
            ]);
            $inserted++;
        }

        return [
            'status'                => 'done',
            'latest_school_year_id' => $latestSchoolYearId,
            'last_grade_level_id'   => $lastGradeLevelId,
            'candidates_found'      => $candidates->count(),
            'inserted'              => $inserted,
        ];
    }
}