<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\MigrationTools;

class SubjectUserMigrationController extends BaseMigrationController
{
    /**
     * Migrates `subjects_user` from old MySQL → `subject_users` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/subject-users/preview  → preview()
     *   POST   /migrate/subject-users          → migrate()
     *   DELETE /migrate/subject-users/reset    → reset()
     *
     * Column mapping (old → new):
     *   subject_id  → subject_id    (preserved as-is)
     *   user_id     → user_id       (preserved, FK to users)
     *   user2_id    → user2_id      (preserved, FK to users)
     *   schoolyear  → school_year_id via lookup on new_db.school_years.school_year
     *   section     → section       (empty string preserved)
     *   semester    → semester      (empty string preserved)
     *   category    → category      (empty string preserved)
     *   (none)      → timestamps    now()
     *
     * ⚠ school_year_id:
     *   Resolved by matching old `schoolyear` varchar against
     *   new_db.school_years.school_year column.
     *   Unmatched rows are inserted with school_year_id = null.
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table' => 'subject_users',
            'old_count' => DB::connection('old_db')->table('subjects_user')->count(),
            'new_count' => DB::connection('new_db')->table('subject_users')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample' => DB::connection('old_db')
                ->table('subjects_user')
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
        $this->buildSchoolYearMap();

        $stats = $this->runMigration(
            newTable: 'subject_users',
            oldTable: 'subjects_user',
            orderColumn: 'id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 9, // subject_id, user_id, user2_id, school_year_id, section, semester, category, created_at, updated_at
            dedupeKey: 'id'
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
        $deleted = DB::connection('new_db')->table('subject_users')->delete();
        DB::connection('new_db')->table('subject_users')->truncate();

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

        if (MigrationTools::skipSubjectId($row->subject_id)) {
            return [];
        }

        return [
            'subject_id' => $this->resolveSubject($row->subject_id) ?? 'x' . $row->subject_id,
            'user_id' => $this->resolveUser($row->user_id) ?? null,
            'user2_id' => $this->resolveUser($row->user2_id) ?? null,
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'section_id' => $this->resolveSection($row->section ?? null),
            'semester' => $row->semester ?? '',
            'category' => $row->category ?? '',
            'created_at' => $row->created_at ?? $now,
            'updated_at' => $row->updated_at ?? $now,
        ];
        // 9 columns — keep in sync with columnCount above
    }
}
