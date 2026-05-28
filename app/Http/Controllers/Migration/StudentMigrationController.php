<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentMigrationController extends BaseMigrationController
{
    /**
     * Migrates `students` from old MySQL → new SQLite.
     *
     * Routes:
     *   GET    /migrate/students/preview  → preview()
     *   POST   /migrate/students          → migrate()
     *   DELETE /migrate/students/reset    → reset()
     *
     * Column mapping (old → new):
     *   firstname   → first_name
     *   middlename  → middle_name
     *   lastname    → last_name
     *   suffix ''   → null  (new schema is nullable)
     *   section     → (dropped)
     *   (none)      → current_level  null
     *   (none)      → timestamps
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        

        return response()->json([
            'table'      => 'students',
            'old_count'  => DB::connection('old_db')->table('students')->count(),
            'new_count'  => DB::table('students')->count(),
            'sample'     => DB::connection('old_db')
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
            columnCount: 11,       // number of columns in mapRow() return value
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
        $deleted = DB::table('students')->delete();
        DB::table('students')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'student_id'    => (int) $row->student_id,
            'student_lrn'   => $row->student_lrn ?: null,
            'first_name'    => $row->firstname,
            'middle_name'   => $row->middlename  ?: null,
            'last_name'     => $row->lastname,
            'suffix'        => ($row->suffix !== '' && $row->suffix !== null) ? $row->suffix : null,
            'gender'        => $row->gender,
            'current_level' => null,
            'active'        => (int) $row->active,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];
        // 11 columns — keep in sync with columnCount above
    }
}
