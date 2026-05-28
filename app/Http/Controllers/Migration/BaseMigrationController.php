<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseMigrationController extends Controller
{
    protected const CHUNK_SIZE  = 200;
    protected const SQLITE_VARS = 999; // SQLite max bound variables per statement

    // -------------------------------------------------------------------------
    // Generic migration runner — shared by all migration controllers
    // -------------------------------------------------------------------------

    /**
     * @param string   $newTable     Target table in new_db
     * @param string   $oldTable     Source table in old_db
     * @param string   $orderColumn  Column to order source rows by
     * @param callable $mapper       Transforms an old row → new row array
     * @param int      $columnCount  Number of columns in mapped row (for SQLite batch limit)
     * @param string|null $dedupeKey Old column name used for duplicate detection.
     *                               Defaults to the first key of the mapped row.
     */
    protected function runMigration(
        string   $newTable,
        string   $oldTable,
        string   $orderColumn,
        callable $mapper,
        int      $columnCount,
        ?string  $dedupeKey = null
    ): array {
        $stats     = ['inserted' => 0, 'skipped' => 0, 'errors' => []];
        $batchSize = (int) floor(self::SQLITE_VARS / $columnCount);

        DB::transaction(function () use (
            $newTable, $oldTable, $orderColumn, $mapper, $batchSize, $dedupeKey, &$stats
        ) {
            DB::connection('old_db')
                ->table($oldTable)
                ->orderBy($orderColumn)
                ->chunk(self::CHUNK_SIZE, function ($rows) use (
                    $newTable, $mapper, $batchSize, $dedupeKey, &$stats
                ) {
                    $firstMapped  = $mapper($rows->first());
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
                                'row'     => $sourceVal,
                                'message' => $e->getMessage(),
                            ];
                            Log::error("Migration mapping error [{$newTable}]", [
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
                            // Fallback: row-by-row so one bad record doesn't discard the batch
                            foreach ($batch as $mapped) {
                                try {
                                    DB::table($newTable)->insert($mapped);
                                    $stats['inserted']++;
                                } catch (Throwable $rowError) {
                                    $stats['errors'][] = [
                                        'row'     => $mapped[$newDedupeKey] ?? null,
                                        'message' => $rowError->getMessage(),
                                    ];
                                    Log::error("Migration insert error [{$newTable}]", [
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

    /**
     * Old `active` columns are sometimes varchar ('1', '0', 'yes', etc.).
     * New schema uses tinyInteger — coerce to 0 or 1.
     */
    protected function coerceActive(mixed $value): int
    {
        if (is_null($value)) {
            return 0;
        }

        $lower = strtolower(trim((string) $value));

        return in_array($lower, ['1', 'yes', 'true', 'active'], strict: true) ? 1 : 0;
    }

    /**
     * SQLite requires the .sqlite file to physically exist before connecting.
     */
    protected function ensureSqliteFileExists(): void
    {
        // $path = config('database.connections.new_db.database');

        // if ($path !== ':memory:' && ! file_exists($path)) {
        //     abort(500, "SQLite file not found at [{$path}]. Run: touch {$path}");
        // }
    }
}