<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\Config\GradeLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Helpers\MigrationTools;

abstract class BaseMigrationController extends Controller
{
    protected const CHUNK_SIZE = 200;

    protected const SQLITE_VARS = 999;

    // -------------------------------------------------------------------------
    // Shared lookup caches
    // -------------------------------------------------------------------------

    private ?array $schoolYearMap = null;

    private ?array $gradeLevelMap = null;

    private ?array $sectionMap = null;

    private ?array $userMap = null;

    private ?array $subjectMap = null;

    // -------------------------------------------------------------------------
    // School year lookup — usable by any child controller
    // -------------------------------------------------------------------------

    /**
     * Builds a map of school_year string → id integer from the new DB.
     * e.g. ['2023-2024' => 3, '2024-2025' => 4]
     *
     * Cached after first call — only one query per migration run.
     */
    protected function buildSchoolYearMap(): array
    {
        if ($this->schoolYearMap !== null) {
            return $this->schoolYearMap;
        }

        $this->schoolYearMap = DB::connection('new_db')
            ->table('school_years')
            ->get(['id', 'label'])
            ->mapWithKeys(fn ($r) => [(string) $r->label => (int) $r->id])
            ->all();

        return $this->schoolYearMap;
    }

    /**
     * Resolves an old `schoolyear` varchar → new `school_year_id` integer.
     *
     * Usage in any child controller mapRow():
     *   'school_year_id' => $this->resolveSchoolYear($row->schoolyear ?? null),
     *
     * Returns null if the value is empty or no matching school_year is found.
     * Pre-load the map before chunking for best performance:
     *   $this->buildSchoolYearMap();  ← call this in migrate() before runMigration()
     */
    protected function resolveSchoolYear(?string $schoolyear): ?int
    {
        if ($schoolyear === null || trim($schoolyear) === '') {
            return null;
        }

        return $this->buildSchoolYearMap()[trim($schoolyear)] ?? null;
    }

    // -------------------------------------------------------------------------
    // Grade level lookup  (gradelevel varchar → grade_levels.id)
    // -------------------------------------------------------------------------

    protected function buildGradeLevelMap(): array
    {
        if ($this->gradeLevelMap !== null) {
            return $this->gradeLevelMap;
        }

        $this->gradeLevelMap = GradeLevel::get('id')
            ->mapWithKeys(fn ($r) => [(int) $r->id => (int) $r->id])
            ->all();

        return $this->gradeLevelMap;
    }

    /**
     * Strategy:
     *   1. Exact match     '7'       → label '7'
     *   2. Numeric extract 'Grade 7' → label '7'
     *   3. Null fallback
     */
    protected function resolveGradeLevel(?string $gradelevel): ?int
    {
        if ($gradelevel === null || trim($gradelevel) === '') {
            return null;
        }

        $map = $this->buildGradeLevelMap();
        $key = trim($gradelevel);

        if (MigrationTools::gradeLevelChecker($key) !== $key) {
            $key = MigrationTools::gradeLevelChecker($key);
        }

        if (isset($map[$key])) {
            return $map[$key];
        }

        if (preg_match('/\d+/', $key, $matches) && isset($map[$matches[0]])) {
            return $map[$matches[0]];
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Section lookup  (section_or_strand varchar → sections.id)
    // -------------------------------------------------------------------------

    protected function buildSectionMap(): array
    {
        if ($this->sectionMap !== null) {
            return $this->sectionMap;
        }

        $this->sectionMap = DB::connection('new_db')
            ->table('sections')
            ->get(['id', 'label'])
            ->mapWithKeys(fn ($r) => [(string) $r->label => (int) $r->id])
            ->all();

        return $this->sectionMap;
    }

    protected function resolveSection(?string $section): ?int
    {
        if ($section === null || trim($section) === '') {
            return null;
        }

        return $this->buildSectionMap()[trim($section)] ?? null;
    }

    // -------------------------------------------------------------------------
    // User lookup  (old users.id → new users.id via old_id column)
    // -------------------------------------------------------------------------

    protected function buildUserMap(): array
    {
        if ($this->userMap !== null) {
            return $this->userMap;
        }

        // Map: old_id (string key) → new id (int)
        $this->userMap = DB::connection('new_db')
            ->table('users')
            ->get(['id', 'old_id'])
            ->mapWithKeys(fn ($r) => [(string) $r->old_id => (int) $r->id])
            ->all();

        return $this->userMap;
    }

    /**
     * Resolves an old users.id → new users.id.
     *
     * Fixed: the previous version called MigrationTools::replaceUserId($old_id)
     * and then looked up $userMap[$new_id] — but $userMap is keyed by OLD ids
     * (old_id column), so that lookup against a NEW id always missed.
     *
     * replaceUserId() is a remap table for old_id → old_id corrections
     * (e.g. known duplicate/merged old user records), so its result must be
     * looked up against $userMap the same way the original $old_id would be.
     */
    protected function resolveUser(?string $old_id): ?int
    {
        if ($old_id === null || trim($old_id) === '') {
            return null;
        }

        $old_id  = trim($old_id);
        $userMap = $this->buildUserMap();

        // Direct map hit — return immediately
        if (isset($userMap[$old_id])) {
            return $userMap[$old_id];
        }

        // Fallback: known old_id remaps not present in the auto-generated map
        // (e.g. duplicate/merged user records in the old DB)
        $remappedOldId = MigrationTools::replaceUserId($old_id);

        if ($remappedOldId === null) {
            Log::channel('migrationlog')->warning("User old_id [{$old_id}] not found in new DB user_map");
            return null;
        }

        // Look up the remapped OLD id against the same userMap (keyed by old_id)
        if (isset($userMap[(string) $remappedOldId])) {
            return $userMap[(string) $remappedOldId];
        }

        Log::channel('migrationlog')->warning(
            "User old_id [{$old_id}] remapped to [{$remappedOldId}] but still not found in user_map"
        );

        return null;
    }

    // -------------------------------------------------------------------------
    // Subject lookup  (varchar code → subjects.id via subject_code)
    // -------------------------------------------------------------------------

    protected function buildSubjectMap(): array
    {
        if ($this->subjectMap !== null) {
            return $this->subjectMap;
        }

        $this->subjectMap = DB::connection('new_db')
            ->table('subjects')
            ->whereNotNull('subject_code')
            ->get(['id', 'subject_code'])
            ->mapWithKeys(fn ($r) => [
                (string) MigrationTools::replaceSubjectId($r->subject_code) => (int) $r->id,
            ])
            ->all();

        return $this->subjectMap;
    }

    /**
     * Resolves an old subject_code varchar → new subjects.id.
     *
     * Fixed: previously returned the magic number 999 as a silent fallback
     * when no match was found. That masks unmapped subjects instead of
     * surfacing them, and risks colliding with a real subjects.id = 999.
     * Returns null instead, consistent with every other resolver, so
     * unmapped rows are visible via whereNull('subject_id') / unfilled checks.
     */
    protected function resolveSubject(?string $subjectCode): ?int
    {
        if ($subjectCode === null || trim($subjectCode) === '') {
            return null;
        }

        $subjectMap  = $this->buildSubjectMap();
        $subjectCode = MigrationTools::replaceSubjectId($subjectCode);

        return $subjectMap[trim($subjectCode)] ?? null;
    }

    // -------------------------------------------------------------------------
    // Generic migration runner
    // -------------------------------------------------------------------------

    protected function runMigration(
        string $newTable,
        string $oldTable,
        string $orderColumn,
        callable $mapper,
        int $columnCount,
        ?string $dedupeKey = null
    ): array {
        $stats          = ['inserted' => 0, 'skipped' => 0, 'errors' => []];
        $skippedRecords = [];
        $batchSize      = (int) floor(self::SQLITE_VARS / $columnCount);

        DB::transaction(function () use (
            $newTable,
            $oldTable,
            $orderColumn,
            $mapper,
            $batchSize,
            $dedupeKey,
            &$skippedRecords,
            &$stats
        ) {
            DB::connection('old_db')
                ->table($oldTable)
                ->orderBy($orderColumn)
                ->chunk(self::CHUNK_SIZE, function ($rows) use (
                    $newTable,
                    $mapper,
                    $batchSize,
                    $dedupeKey,
                    $oldTable,
                    $orderColumn,
                    &$skippedRecords,
                    &$stats
                ) {
                    $mappedPairs = [];

                    foreach ($rows as $row) {
                        try {
                            $mapped = $mapper($row);

                            if (! empty($mapped)) {
                                $mappedPairs[] = [
                                    'old' => $row,
                                    'new' => $mapped,
                                ];
                            } else {
                                $skippedRecords[$oldTable][] = $row;
                                $stats['skipped']++;
                            }
                        } catch (Throwable $e) {
                            $stats['errors'][] = [
                                'row'     => $row->{$orderColumn} ?? 'unknown',
                                'message' => $e->getMessage(),
                            ];
                            Log::channel('migrationlog')->error("Migration mapping error [{$newTable}]", [
                                'old_table' => $oldTable,
                                'old_row'   => (array) $row,
                                'error'     => $e->getMessage(),
                            ]);
                        }
                    }

                    if (empty($mappedPairs)) {
                        return;
                    }

                    // Define target database unique/composite keys
                    $newDedupeKeys = $this->getDedupeKeysForTable($newTable, $dedupeKey);
                    $existingKeys  = [];

                    if (! empty($newDedupeKeys)) {
                        $query = DB::table($newTable);

                        foreach ($newDedupeKeys as $col) {
                            $colVals = array_unique(array_column(array_column($mappedPairs, 'new'), $col));
                            $nonNullVals = array_filter($colVals, fn($v) => $v !== null);
                            $hasNull = count($colVals) > count($nonNullVals);

                            $query->where(function ($q) use ($col, $nonNullVals, $hasNull) {
                                if (! empty($nonNullVals)) {
                                    $q->whereIn($col, $nonNullVals);
                                }
                                if ($hasNull) {
                                    $q->orWhereNull($col);
                                }
                                if (empty($nonNullVals) && ! $hasNull) {
                                    $q->whereRaw('1 = 0');
                                }
                            });
                        }

                        $existingRows = $query->get($newDedupeKeys)->all();

                        foreach ($existingRows as $existingRow) {
                            $existingRow = (array) $existingRow;
                            $keyParts    = [];
                            foreach ($newDedupeKeys as $col) {
                                $keyParts[] = (string) ($existingRow[$col] ?? '');
                            }
                            $existingKeys[implode('|', $keyParts)] = true;
                        }
                    }

                    // For backward-compatible log variables:
                    $firstMapped = $mappedPairs[0]['new'];
                    $newDedupeKey = array_key_first($firstMapped);

                    $toInsert    = [];
                    $sourceByKey = []; // newDedupeKey value → original old row, for error logging
                    $seenInChunk = [];

                    foreach ($mappedPairs as $pair) {
                        $mapped = $pair['new'];
                        $row    = $pair['old'];

                        if (! empty($newDedupeKeys)) {
                            $keyParts = [];
                            foreach ($newDedupeKeys as $col) {
                                $keyParts[] = (string) ($mapped[$col] ?? '');
                            }
                            $lookupKey = implode('|', $keyParts);

                            if (isset($existingKeys[$lookupKey]) || isset($seenInChunk[$lookupKey])) {
                                $skippedRecords[$oldTable][] = $row;
                                $stats['skipped']++;
                                continue;
                            }
                            $seenInChunk[$lookupKey] = true;
                        }

                        $toInsert[] = $mapped;
                        $sourceByKey[$mapped[$newDedupeKey] ?? null] = $row;
                    }

                    if (empty($toInsert)) {
                        return;
                    }

                    foreach (array_chunk($toInsert, $batchSize) as $batch) {
                        try {
                            DB::table($newTable)->insert($batch);
                            $stats['inserted'] += count($batch);
                        } catch (Throwable $e) {
                            foreach ($batch as $mapped) {
                                try {
                                    DB::table($newTable)->insert($mapped);
                                    $stats['inserted']++;
                                } catch (Throwable $rowError) {
                                    $dedupeVal = $mapped[$newDedupeKey] ?? null;
                                    $originalRow = $sourceByKey[$dedupeVal] ?? null;

                                    $stats['errors'][] = [
                                        'row'     => $dedupeVal,
                                        'message' => $rowError->getMessage(),
                                    ];

                                    Log::channel('migrationlog')->error("Migration insert error [{$newTable}]", [
                                        'old_table'   => $oldTable,
                                        'old_row'     => $originalRow ? (array) $originalRow : null,
                                        'mapped_row'  => $mapped,
                                        'error'       => $rowError->getMessage(),
                                    ]);
                                }
                            }
                        }
                    }
                });
        });

        if ($skippedRecords) {
            Log::channel('migrationlog')->info("Duplicate record in [{$newTable}]", [
                'value' => $skippedRecords,
            ]);
        }

        return $stats;
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    protected function coerceActive(mixed $value): int
    {
        if (is_null($value)) {
            return 0;
        }

        $lower = strtolower(trim((string) $value));

        return in_array($lower, ['1', 'yes', 'true', 'active'], strict: true) ? 1 : 0;
    }

    protected function getDedupeKeysForTable(string $table, ?string $dedupeKey): array
    {
        if (is_array($dedupeKey)) {
            return $dedupeKey;
        }

        $compositeMaps = [
            'grades'           => ['student_id', 'subject_id', 'quarter', 'school_year_id', 'grade_level_id'],
            'deportments'      => ['student_id', 'school_year_id', 'quarter'],
            'attendances'      => ['student_id', 'school_year_id'],
            'enrollments'      => ['student_id', 'school_year_id'],
            'subject_units'    => ['subject_id', 'school_year_id'],
            'subject_excluded' => ['student_id', 'subject_id', 'school_year_id'],
            'student_sections' => ['student_id', 'section_id', 'school_year_id'],
            'graduates'        => ['student_id', 'school_year_id'],
            'awards'           => ['student_id', 'subject_id', 'school_year_id'],
            'sections'         => ['label', 'grade_level_id', 'school_year_id'],
            'subject_users'    => ['subject_id', 'school_year_id', 'section_id'],
            'user_roles'       => ['user_id', 'role_id'],
            'homerooms'        => ['section_id', 'school_year_id', 'grade_level_id'],
            'users'            => ['old_id'],
            'students'         => ['id'],
            'student_infos'    => ['student_id'],
            'subjects'         => ['subject_code'],
            'school_years'     => ['label'],
        ];

        if (isset($compositeMaps[$table])) {
            return $compositeMaps[$table];
        }

        if ($dedupeKey !== null) {
            return [$dedupeKey];
        }

        return [];
    }
}
