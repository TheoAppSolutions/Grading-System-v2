<?php

namespace App\Http\Controllers\Migration;

use App\Models\Config\GradeLevel;
use App\Models\Config\SubjectCategory;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectMigrationController extends BaseMigrationController
{
    /**
     * Migrates `subjects` from old MySQL → `subjects` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/subjects/preview  → preview()
     *   POST   /migrate/subjects          → migrate()
     *   GET    /migrate/subjects/unfilled → unfilledGradeLevels()
     *   DELETE /migrate/subjects/reset    → reset()
     *
     * Column mapping (old → new):
     *   subject_id    → subject_code     preserved varchar code (e.g. 'MATH-01')
     *                                    used by subject_users and subject_units
     *                                    to cross-reference this table
     *   subjecttitle  → subject_title    falls back to subjectname if empty
     *   gradelevel    → grade_level_id   looked up via new_db.grade_levels.label
     *   subjectname   → (dropped)        only used as fallback for subject_title
     *
     * Generated / defaulted:
     *   subject_category_id → null  (no equivalent in old schema)
     *   timestamps          → now()
     *
     * ⚠ subject_code:
     *   This is the critical link between subjects ↔ subject_users ↔ subject_units
     *   ↔ subject_excluded. All those tables store subject_id as a varchar code.
     *   Preserving it here as subject_code keeps that relationship intact.
     *
     * ⚠ grade_level_id:
     *   Resolved by matching old `gradelevel` varchar against grade_levels.label.
     *   Rows with no match get null — call GET /migrate/subjects/unfilled to list them.
     */
    private ?array $subjectCategoryMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table' => 'subjects',
            'old_count' => DB::connection('old_db')->table('subjects')->count(),
            'new_count' => DB::connection('new_db')->table('subjects')->count(),
            'grade_level_map' => $this->buildGradeLevelMap(),
            'sample' => DB::connection('old_db')
                ->table('subjects')
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
        $this->buildGradeLevelMap();

        $stats = $this->runMigration(
            newTable: 'subjects',
            oldTable: 'subjects',
            orderColumn: 'entry_id',
            mapper: fn ($r) => $this->mapRow($r),
            columnCount: 6,         // subject_code, grade_level_id, subject_category_id, subject_title, created_at, updated_at
            dedupeKey: 'subject_id' // old varchar code — unique per subject
        );

        $unfilled = DB::connection('new_db')
            ->table('subjects')
            ->whereNull('grade_level_id')
            ->count();

        // $this->insertChineseSubject();

        return response()->json([
            'status' => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled_grade_level_id' => $unfilled,
            'note' => $unfilled > 0
                ? "{$unfilled} subject(s) have null grade_level_id. Call GET /migrate/subjects/unfilled."
                : 'All grade_level_id values resolved successfully.',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('subjects')->delete();
        DB::connection('new_db')->table('subjects')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            // Preserved varchar code — cross-reference key for subject_users,
            // subject_units, and subject_excluded
            'subject_code' => $row->subject_id,
            'grade_level_id' => $this->resolveGradeLevel($row->gradelevel ?? null),
            'subject_category_id' => $this->resolveSubjectCategory($row->subjecttitle ?? null),
            'subject_title' => Str::title($row->subjectname) ?: (Str::title($row->subjectname) ?? 0),
            'created_at' => $now,
            'updated_at' => $now,
        ];
        // 6 columns — keep in sync with columnCount above
    }

    // -------------------------------------------------------------------------
    // Subject Category lookup helpers
    // -------------------------------------------------------------------------

    private function buildSubjectCategoryMap(): array
    {
        if ($this->subjectCategoryMap !== null) {
            return $this->subjectCategoryMap;
        }

        $this->subjectCategoryMap = SubjectCategory::get(['id', 'label'])
            ->mapWithKeys(fn ($r) => [Str::upper($r->label) => (int) $r->id])
            ->all();

        return $this->subjectCategoryMap;
    }

    private function resolveSubjectCategory(?string $subjectName): ?int
    {
        if ($subjectName === null || trim($subjectName) === '') {
            return null;
        }

        $map = $this->buildSubjectCategoryMap();
        $key = trim($subjectName);

        if (isset($map[$key])) {
            return $map[$key];
        }

        if (preg_match('/\d+/', $key, $matches) && isset($map[$matches[0]])) {
            return $map[$matches[0]];
        }

        return 0;
    }

    public function insertChineseSubject(): void
    {
        $subjects = [
            ['subject_code' => 'CHIN01', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 1],
            ['subject_code' => 'CHIN02', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 2],
            ['subject_code' => 'CHIN03', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 3],
            ['subject_code' => 'CHIN04', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 4],
            ['subject_code' => 'CHIN05', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 5],
            ['subject_code' => 'CHIN06', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 6],
            ['subject_code' => 'CHIN07', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 7],
            ['subject_code' => 'CHIN08', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 8],
            ['subject_code' => 'CHIN09', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 9],
            ['subject_code' => 'CHIN10', 'subject_title' => 'Chinese', 'subject_category_id' => 15, 'grade_level_id' => 10],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['subject_code' => $subject['subject_code']],
                [
                    'subject_title' => $subject['subject_title'],
                    'subject_category_id' => $subject['subject_category_id'],
                    'grade_level_id' => $subject['grade_level_id'],
                ]
            );
        }
    }
}
