<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceMigrationController extends BaseMigrationController
{
    private ?array $studentIds = null;

    /**
     * Migrates `attendance` from old MySQL → `attendances` in new SQLite.
     *
     * 1-to-1 row mapping — JSON column preserved verbatim.
     * Only schoolyear varchar → school_year_id FK needs resolving.
     *
     * Routes:
     *   GET    /migrate/attendances/preview  → preview()
     *   POST   /migrate/attendances          → migrate()
     *   GET    /migrate/attendances/unfilled → unfilled()
     *   DELETE /migrate/attendances/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id     (int, preserved)
     *   schoolyear  → school_year_id FK int — resolveSchoolYear() from base
     *   attendance  → attendance     JSON preserved verbatim
     *   created_at  → created_at     original timestamp preserved
     *   updated_at  → updated_at     original timestamp preserved
     *
     * JSON structure (preserved as-is):
     *   {"1": {"A": 0, "T": 0}, "2": {"A": 0, "T": 1}, ... "12": {"A": 0, "T": 2}}
     *   Key = month number, A = absences, T = tardiness
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'attendances',
            'old_count'       => DB::connection('old_db')->table('attendance')->count(),
            'new_count'       => DB::connection('new_db')->table('attendances')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('attendance')
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
        $this->studentIds = DB::connection('new_db')
            ->table('students')
            ->pluck('id')
            ->map(fn ($studentId) => (int) $studentId)
            ->flip()
            ->all();

        $stats = $this->runMigration(
            newTable:    'attendances',
            oldTable:    'attendance',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 5, // student_id, school_year_id, attendance, created_at, updated_at
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
        $deleted = DB::connection('new_db')->table('attendances')->delete();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();
        $studentId = (int) $row->student_id;
        $schoolYearId = $this->resolveSchoolYear($row->schoolyear ?? null);

        if (! isset($this->studentIds[$studentId])) {
            Log::channel('migrationlog')->warning('Skipped orphan attendance row', [
                'student_id' => $studentId,
                'schoolyear' => $row->schoolyear ?? null,
                'reason' => 'student does not exist in new_db.students',
            ]);

            return [];
        }

        if ($schoolYearId === null) {
            Log::channel('migrationlog')->warning('Skipped attendance row with unknown school year', [
                'student_id' => $studentId,
                'schoolyear' => $row->schoolyear ?? null,
                'reason' => 'school year does not exist in new_db.school_years',
            ]);

            return [];
        }

        return [
            'student_id'     => $studentId,
            'school_year_id' => $schoolYearId,
            'attendance'     => $row->attendance, // JSON string preserved verbatim
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 5 columns — keep in sync with columnCount above
    }
}
