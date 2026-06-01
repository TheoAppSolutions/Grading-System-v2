<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentInfoMigrationController extends BaseMigrationController
{
    /**
     * Migrates `students_info` from old MySQL → `student_infos` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/student-infos/preview  → preview()
     *   POST   /migrate/student-infos          → migrate()
     *   DELETE /migrate/student-infos/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id      (int, unique, preserved)
     *   birthdate   → birth_date      (renamed, date preserved)
     *   houseteam   → house_team      (renamed)
     *   contact     → contact         (preserved)
     *   address     → address         (preserved)
     *   email       → email_address   (renamed)
     *   guardian    → guardian        (preserved)
     *   occupation  → occupation      (preserved)
     *   created_at  → created_at      (original timestamp preserved)
     *   updated_at  → updated_at      (original timestamp preserved)
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'     => 'student_infos',
            'old_count' => DB::connection('old_db')->table('students_info')->count(),
            'new_count' => DB::connection('new_db')->table('student_infos')->count(),
            'sample'    => DB::connection('old_db')
                                ->table('students_info')
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
        $stats = $this->runMigration(
            newTable:    'student_infos',
            oldTable:    'students_info',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 10, // student_id, birth_date, house_team, contact, address, email_address, guardian, occupation, created_at, updated_at
            dedupeKey:   'student_id'
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
        $deleted = DB::connection('new_db')->table('student_infos')->delete();

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

            // Renamed: birthdate → birth_date
            'birth_date'    => $row->birthdate ?? null,

            // Renamed: houseteam → house_team
            'house_team'    => $row->houseteam    ?: null,

            'contact'       => $row->contact      ?: null,
            'address'       => $row->address      ?: null,

            // Renamed: email → email_address
            'email_address' => $row->email        ?: null,

            'guardian'      => $row->guardian     ?: null,
            'occupation'    => $row->occupation   ?: null,

            // Preserve original timestamps from old DB
            'created_at'    => $row->created_at ?? $now,
            'updated_at'    => $row->updated_at ?? $now,
        ];
        // 10 columns — keep in sync with columnCount above
    }
}