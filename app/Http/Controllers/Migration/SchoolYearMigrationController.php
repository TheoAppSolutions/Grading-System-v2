<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SchoolYearMigrationController extends BaseMigrationController
{
    /**
     * Migrates `schoolyear` from old MySQL → `school_years` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/school-years/preview  → preview()
     *   POST   /migrate/school-years          → migrate()
     *   DELETE /migrate/school-years/reset    → reset()
     *
     * Column mapping (old → new):
     *   schoolyear         → school_year
     *   schoolyear_start   → month_start  (int → string)
     *   schoolyear_end     → month_end    (int → string)
     *   active (varchar)   → active (tinyInteger) via coerceActive()
     *   school_days (JSON) → (dropped, no equivalent)
     *   days_settings      → attendance_entries  hardcoded to default 10
     *   card_template      → card_template_id    null (string path ≠ FK int)
     *   (none)             → timestamps
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {

        return response()->json([
            'table' => 'school_years',
            'old_count' => DB::connection('old_db')->table('schoolyear')->count(),
            'new_count' => DB::table('school_years')->count(),
            'sample' => DB::connection('old_db')
                ->table('schoolyear')
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
            newTable: 'school_years',
            oldTable: 'schoolyear',
            orderColumn: 'entry_id',
            mapper: fn ($r) => $this->mapRow($r),
            columnCount: 8,           // number of columns in mapRow() return value
            dedupeKey: 'schoolyear' // old column name used for duplicate detection
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
        $deleted = DB::table('school_years')->delete();
        DB::table('school_years')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            'school_year' => $row->schoolyear,
            'month_start' => (string) ($row->schoolyear_start ?? 1),
            'month_end' => (string) ($row->schoolyear_end ?? 12),
            'active' => $this->coerceActive($row->active),
            'attendance_entries' => 10,   // days_settings not directly mappable
            'card_template_id' => null, // old value was a string path, not an int FK
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 8 columns — keep in sync with columnCount above
    }
}
