<?php

namespace App\Http\Controllers\Migration;

use App\Helpers\MigrationTools;
use App\Models\CommentCriteria;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommentsMigrationController extends BaseMigrationController
{
    /**
     * Migrates comments_quarter + comments_midquarter → single `comments` table.
     *
     * One row per student per school year.
     * comments_quarter  → comments_quarter  JSON column  (flat flags)
     * comments_midquarter rows → comments_midquarter JSON column
     *   (merged into one object keyed by quarter: {"1": {...}, "2": {...}})
     *
     * ⚠ Tradeoff vs separate tables:
     *   - quarter is no longer a queryable column
     *   - updating one quarter requires reading + rewriting the full JSON blob
     *   - unique constraint on the schema should be ['student_id', 'school_year_id']
     *     not ['student_id', 'label', 'quarter'] — fix that first
     *
     * Routes:
     *   GET    /migrate/comments/preview  → preview()
     *   POST   /migrate/comments          → migrate()
     *   GET    /migrate/comments/unfilled → unfilled()
     *   DELETE /migrate/comments/reset    → reset()
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        return response()->json([
            'table'                    => 'comments',
            'old_quarter_count'        => DB::connection('old_db')->table('comments_quarter')->count(),
            'old_midquarter_count'     => DB::connection('old_db')->table('comments_midquarter')->count(),
            'new_count'                => DB::connection('new_db')->table('comments')->count(),
            'school_year_map'          => $this->buildSchoolYearMap(),
            'sample'                   => $this->buildSampleRow(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $this->buildSchoolYearMap();

        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];

        // Pre-load all midquarter rows into memory grouped by student+schoolyear
        // so we don't query per row during chunking
        $midquarterIndex = $this->buildMidquarterIndex();

        DB::connection('new_db')->statement('PRAGMA foreign_keys = OFF');

        try {
            DB::connection('new_db')->transaction(function () use (&$stats, $midquarterIndex) {

                // Drive iteration from comments_quarter (one row per student per year)
                DB::connection('old_db')
                    ->table('comments_quarter')
                    ->orderBy('entry_id')
                    ->chunk(self::CHUNK_SIZE, function ($rows) use (&$stats, $midquarterIndex) {

                        $toInsert = [];

                        foreach ($rows as $row) {
                            $schoolYearId = $this->resolveSchoolYear($row->schoolyear ?? null);
                            $key          = $row->student_id . '_' . ($row->schoolyear ?? '');

                            // Dedupe
                            $exists = DB::connection('new_db')
                                ->table('comments')
                                ->where('student_id',     $row->student_id)
                                ->where('school_year_id', $schoolYearId)
                                ->exists();

                            if ($exists) {
                                $stats['skipped']++;
                                continue;
                            }

                            $now = now()->toDateTimeString();

                            // Merge midquarter rows for this student+year into one object
                            // keyed by quarter: {"1": {subject data}, "2": {subject data}}
                            $midquarterByQuarter = $midquarterIndex[$key] ?? [];

                            $toInsert[] = [
                                'student_id'           => (int) $row->student_id,
                                'school_year_id'       => $schoolYearId,
                                'comments_quarter'     => $row->comments,
                                'comments_midquarter'  => json_encode($midquarterByQuarter),
                                'created_at'           => $row->created_at ?? $now,
                                'updated_at'           => $row->updated_at ?? $now,
                            ];
                        }

                        if (empty($toInsert)) {
                            return;
                        }

                        // 6 columns: student_id, school_year_id, comments_quarter,
                        // comments_midquarter, created_at, updated_at
                        $batchSize = (int) floor(999 / 6);

                        foreach (array_chunk($toInsert, $batchSize) as $batch) {
                            try {
                                DB::connection('new_db')->table('comments')->insert($batch);
                                $stats['inserted'] += count($batch);
                            } catch (\Throwable $e) {
                                foreach ($batch as $mapped) {
                                    try {
                                        DB::connection('new_db')->table('comments')->insert($mapped);
                                        $stats['inserted']++;
                                    } catch (\Throwable $rowError) {
                                        $stats['errors'][] = [
                                            'row'     => $mapped['student_id'],
                                            'message' => $rowError->getMessage(),
                                        ];
                                    }
                                }
                            }
                        }
                    });
            });
        } finally {
            DB::connection('new_db')->statement('PRAGMA foreign_keys = ON');
        }

        $this->insertCommentsCriteria();

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Unfilled
    // -------------------------------------------------------------------------

    public function unfilled(): JsonResponse
    {
        $rows = DB::connection('new_db')
            ->table('comments')
            ->whereNull('school_year_id')
            ->select(['id', 'student_id', 'school_year_id'])
            ->get();

        return response()->json(['count' => $rows->count(), 'rows' => $rows]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::connection('new_db')->table('comments')->delete();
        DB::connection('new_db')->table('comments')->truncate();
        DB::connection('new_db')->table('comments_criteria')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Pre-loads all comments_midquarter rows into an in-memory index:
     *   "{student_id}_{schoolyear}" => [quarter => decoded_comments_json]
     *
     * e.g. "2014016_2016-2017" => [1 => {...}, 2 => {...}}]
     *
     * Avoids N+1 queries during chunking.
     */
    private function buildMidquarterIndex(): array
    {
        $index = [];

        DB::connection('old_db')
            ->table('comments_midquarter')
            ->orderBy('entry_id')
            ->chunk(500, function ($rows) use (&$index) {
                foreach ($rows as $row) {
                    $key     = $row->student_id . '_' . ($row->schoolyear ?? '');
                    // $quarter = (int) $row->quarter;
                    // $index[$key][$quarter] = json_decode($row->comments ?? '{}', associative: true);
                    $index[$key] = json_decode($row->comments ?? '{}', associative: true);
                }
            });

        return $index;
    }

    private function buildSampleRow(): array
    {
        $quarterRow = DB::connection('old_db')->table('comments_quarter')->first();

        if (! $quarterRow) {
            return [];
        }

        $key = $quarterRow->student_id . '_' . ($quarterRow->schoolyear ?? '');
        $midquarterIndex = $this->buildMidquarterIndex();

        return [
            'student_id'          => $quarterRow->student_id,
            'school_year_id'      => $this->resolveSchoolYear($quarterRow->schoolyear ?? null),
            'comments_quarter'    => json_decode($quarterRow->comments, associative: true),
            'comments_midquarter' => $midquarterIndex[$key] ?? null,
        ];
    }

    private function insertCommentsCriteria(): void
    {
        $criterias = MigrationTools::default_criteria();
        foreach ($criterias as $criteria) {
            CommentCriteria::updateOrCreate(
                ['code' => $criteria['code']],
                [
                    'comment_category_id' => $criteria['comment_category_id'],
                    'description' => $criteria['description'],
                    'type' => $criteria['type'],
                    'active' => 1,
                ]
            );
        }
    }
}
