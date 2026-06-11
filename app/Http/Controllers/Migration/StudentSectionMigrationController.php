<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentSectionMigrationController extends BaseMigrationController
{
    /**
     * Migrates `students_section` from old MySQL → `student_sections` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/student-sections/preview  → preview()
     *   POST   /migrate/student-sections          → migrate()
     *   DELETE /migrate/student-sections/reset    → reset()
     *
     * Column mapping (old → new):
     *   student_id  → student_id     (int, preserved)
     *   section     → section_id     FK int — looked up via sections.title
     *   schoolyear  → school_year_id FK int — resolveSchoolYear() from base
     *   (none)      → timestamps     now()
     */

    private ?array $sectionMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'           => 'student_sections',
            'old_count'       => DB::connection('old_db')->table('students_section')->count(),
            'new_count'       => DB::connection('new_db')->table('student_sections')->count(),
            'section_map'     => $this->buildSectionMap(),
            'school_year_map' => $this->buildSchoolYearMap(),
            'sample'          => DB::connection('old_db')
                                        ->table('students_section')
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
        $this->buildSectionMap();
        $this->buildSchoolYearMap(); // from BaseMigrationController

        $stats = $this->runMigration(
            newTable:    'student_sections',
            oldTable:    'students_section',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 5, // student_id, section_id, school_year_id, created_at, updated_at
            dedupeKey:   'entry_id'
        );

        $unfilledSections    = DB::connection('new_db')->table('student_sections')->whereNull('section_id')->count();

        return response()->json([
            'status'                  => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled_section_id'     => $unfilledSections,
            'note'                    => ($unfilledSections) > 0
                ? 'Some rows have unresolved FKs. Call GET /migrate/student-sections/unfilled.'
                : 'All section_id and school_year_id values resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // List rows with any unresolved FK
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('student_sections')
            ->where(fn ($q) => $q
                ->whereNull('section_id')
                ->orWhereNull('school_year_id')
            )
            ->select(['id', 'student_id', 'section_id', 'school_year_id'])
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
        $deleted = DB::connection('new_db')->table('student_sections')->delete();

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
            'section_id'     => $this->resolveSection($row->section ?? null),
            'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null), // from base
            'created_at'     => $row->created_at ?? $now,
            'updated_at'     => $row->updated_at ?? $now,
        ];
        // 5 columns — keep in sync with columnCount above
    }
}
