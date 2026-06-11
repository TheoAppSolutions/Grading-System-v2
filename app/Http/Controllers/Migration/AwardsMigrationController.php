<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AwardsMigrationController extends BaseMigrationController
{
    /**
     * Migrates `awards` from old MySQL → `awards` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/awards/preview  → preview()
     *   POST   /migrate/awards          → migrate()
     *   GET    /migrate/awards/unfilled → unfilled()
     *   DELETE /migrate/awards/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id     (int, preserved)
     *   subject_id  → subject_id     FK int — looked up via subjects.subject_code
     *   schoolyear  → school_year_id FK int — resolveSchoolYear() from base
     *   gender      → gender         (string, preserved)
     *   section     → (dropped)      not in new schema
     *   created_at  → created_at     original timestamp preserved
     *   updated_at  → updated_at     original timestamp preserved
     */

    private ?array $subjectMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'awards',
            'old_count'       => DB::connection('old_db')->table('awards')->count(),
            'new_count'       => DB::connection('new_db')->table('awards')->count(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'subject_map'     => $this->buildSubjectMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('awards')
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
        $this->buildSubjectMap();

        $stats = $this->runMigration(
            newTable:    'awards',
            oldTable:    'awards',
            orderColumn: 'id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 6, // student_id, subject_id, school_year_id, gender, created_at, updated_at
            dedupeKey:   'id'
        );

        $unfilled = DB::connection('new_db')
            ->table('awards')
            ->where(fn ($q) => $q
                ->whereNull('subject_id')
                ->orWhereNull('school_year_id')
            )
            ->count();

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled' => $unfilled,
            'note'     => $unfilled > 0
                ? "{$unfilled} row(s) have unresolved FKs. Call GET /migrate/awards/unfilled."
                : 'All FKs resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // List rows with unresolved FKs
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('awards')
            ->where(fn ($q) => $q
                ->whereNull('subject_id')
                ->orWhereNull('school_year_id')
            )
            ->select(['id', 'student_id', 'subject_id', 'school_year_id'])
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
        $deleted = DB::connection('new_db')->table('awards')->delete();

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
            'subject_id'     => $this->resolveSubject($row->subject_id ?? null),
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
            'gender'         => $row->gender,
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 6 columns — keep in sync with columnCount above
        // section dropped — not in new schema
    }
}