<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\Config\GradeLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Helpers\HCGS;

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
            ->get(['id', 'school_year'])
            ->mapWithKeys(fn($r) => [(string) $r->school_year => (int) $r->id])
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
            ->mapWithKeys(fn($r) => [(int) $r->id => (int) $r->id])
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

        if (HCGS::isHCGS() && HCGS::gradeLevelChecker($key) !== $key) {
             $key = HCGS::gradeLevelChecker($key);
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
            ->get(['id', 'section'])
            ->mapWithKeys(fn($r) => [(string) $r->section => (int) $r->id])
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
    // Section lookup  (section_or_strand varchar → sections.id)
    // -------------------------------------------------------------------------

    protected function buildUserMap(): array
    {
        if ($this->userMap !== null) {
            return $this->userMap;
        }

        $this->userMap = DB::connection('new_db')
            ->table('users')
            ->get(['id', 'old_id'])
            ->mapWithKeys(fn($r) => [(string) $r->old_id => (int) $r->id])
            ->all();

        return $this->userMap;
    }

    protected function resolveUser(?string $old_id): ?int
    {
        if ($old_id === null || trim($old_id) === '') {
            return null;
        }

        $old_id = trim($old_id);
        $userMap = $this->buildUserMap();

        // Direct map hit — return immediately
        if (isset($userMap[$old_id])) {
            return $userMap[$old_id];
        }

        // Fallback: known ID remaps not in the auto-generated map
        $new_id = match (config('app.name')) {
            'BIS'  => match ($old_id) {
                '188' => 114,
                default => null,
            },
            'HCGS' => match ($old_id) {
                '45'  => 6,
                '47'  => 19,
                '53'  => 7,
                default => null,
            },
            default => null,
        };

        if ($new_id === null) {
            Log::warning("User old_id [{$old_id}] not found in new DB user_map");
            return null;
        }

        return $userMap[$new_id] ?? null;
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
            ->mapWithKeys(fn($r) => [(string) $r->subject_code => (int) $r->id])
            ->all();

        return $this->subjectMap;
    }

    protected function resolveSubject(?string $subjectCode): ?int
    {
        if ($subjectCode === null || trim($subjectCode) === '') {
            return null;
        }

        if (! isset($this->buildSubjectMap()[trim($subjectCode)])) {
            if (trim($subjectCode) == 'FILI12') {
                $subjectCode = 'FILI12A'; // special case for old code that was duplicated and had to be changed in new DB
            } else {
                Log::warning("Subject code [{$subjectCode}] not found in new DB subject_map");
            }
        }

        return $this->buildSubjectMap()[trim($subjectCode)] ?? 999;
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
        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => []];
        $batchSize = (int) floor(self::SQLITE_VARS / $columnCount);

        DB::transaction(function () use (
            $newTable,
            $oldTable,
            $orderColumn,
            $mapper,
            $batchSize,
            $dedupeKey,
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
                    &$stats
                ) {
                    $firstMapped = $mapper($rows->first());
                    $newDedupeKey = array_key_first($firstMapped);
                    $oldDedupeKey = $dedupeKey ?? $newDedupeKey;

                    $incomingVals = $rows->pluck($oldDedupeKey)->all();

                    $existingVals = DB::table($newTable)
                        ->whereIn($newDedupeKey, $incomingVals)
                        ->pluck($newDedupeKey)
                        ->flip()
                        ->all();

                    $toInsert = [];

                    foreach ($rows as $row) {
                        $sourceVal = $row->{$oldDedupeKey};

                        if (isset($existingVals[$sourceVal])) {
                            $stats['skipped']++;

                            continue;
                        }

                        try {
                            $toInsert[] = $mapper($row);
                        } catch (Throwable $e) {
                            $stats['errors'][] = [
                                'row' => $sourceVal,
                                'message' => $e->getMessage(),
                            ];
                            Log::channel('migrationlog')->info("Migration mapping error [{$newTable}]", [
                                'value' => $sourceVal,
                                'error' => $e->getMessage(),
                            ]);
                        }
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
                                    $stats['errors'][] = [
                                        'row' => $mapped[$newDedupeKey] ?? null,
                                        'message' => $rowError->getMessage(),
                                    ];
                                    Log::channel('migrationlog')->info("Migration insert error [{$newTable}]", [
                                        'error' => $rowError->getMessage(),
                                    ]);
                                }
                            }
                        }
                    }
                });
        });

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

    protected function ensureSqliteFileExists(): void {}
}
