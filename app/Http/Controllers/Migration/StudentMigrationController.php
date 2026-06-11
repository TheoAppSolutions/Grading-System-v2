<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentMigrationController extends BaseMigrationController
{
    /**
     * Migrates `students` from old MySQL → `students` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/students/preview  → preview()
     *   POST   /migrate/students          → migrate()
     *   DELETE /migrate/students/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id    (int, preserved)
     *   student_lrn → student_lrn   (preserved)
     *   firstname   → first_name    (renamed)
     *   middlename  → middle_name   (renamed, nullable)
     *   lastname    → last_name     (renamed)
     *   suffix      → suffix        (empty string → null)
     *   gender      → gender        (preserved)
     *   active      → active        (int → tinyInteger)
     *
     * Dropped:
     *   section     → moved to student_sections table
     *
     * Generated:
     *   current_level → null  (no equivalent in old schema)
     *   timestamps    → now()
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'     => 'students',
            'old_count' => DB::connection('old_db')->table('students')->count(),
            'new_count' => DB::connection('new_db')->table('students')->count(),
            'sample'    => DB::connection('old_db')
                ->table('students')
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
        $stats = $this->runMigration(
            newTable: 'students',
            oldTable: 'students',
            orderColumn: 'entry_id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 11, // student_id, student_lrn, first_name, middle_name, last_name, suffix, gender, current_level, active, created_at, updated_at
            dedupeKey: 'student_id'
        );

        return response()->json([
            'status' => empty($stats['errors']) ? 'done' : 'done_with_errors',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('students')->delete();
        DB::connection('new_db')->table('students')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        if($row->student_id === 12341) {
            return []; // skip this record due to test data
        }

        return [
            'id'            => (int) $row->student_id,
            'student_lrn'   => $row->student_lrn ?: null,
            'first_name'    => $row->firstname,
            'middle_name'   => $row->middlename  ?: null,
            'last_name'     => $row->lastname,
            'suffix'        => ($row->suffix !== '' && $row->suffix !== null) ? $row->suffix : null,
            'gender'        => $row->gender,
            'current_level' => null,
            'active'        => (int) $row->active,
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 11 columns — keep in sync with columnCount above
    }
}
