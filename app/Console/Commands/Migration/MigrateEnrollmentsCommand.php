<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\EnrollmentMigrationController;
use Illuminate\Console\Command;

class MigrateEnrollmentsCommand extends Command
{
    protected $signature = 'migrate:enrollments
                            {--fresh : Reset enrollments table before migrating}
                            {--sync-levels : Run sync-current-level after migrating}';

    protected $description = 'Migrate enrollments from old MySQL to new SQLite';

    public function handle(EnrollmentMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting enrollments table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating enrollments...');

        $response = $controller->migrate();
        $data     = $response->getData(assoc: true);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rows inserted', $data['inserted'] ?? 0],
                ['Rows skipped',  $data['skipped']  ?? 0],
                ['Errors',        count($data['errors'] ?? [])],
            ]
        );

        if (! empty($data['unfilled'])) {
            $this->warn("  ⚠  {$data['unfilled']} row(s) with unresolved FKs.");
        }

        if (! empty($data['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($data['errors'] as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
            return self::FAILURE;
        }

        // Optionally sync students.current_level after enrollments are in
        if ($this->option('sync-levels')) {
            $this->newLine();
            $this->info('Syncing students.current_level from latest enrollments...');

            $syncResponse = $controller->syncCurrentLevels();
            $syncData     = $syncResponse->getData(assoc: true);

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Students updated', $syncData['updated'] ?? 0],
                    ['Students skipped', $syncData['skipped'] ?? 0],
                    ['Errors',           count($syncData['errors'] ?? [])],
                ]
            );
        }

        $this->newLine();
        $this->info('Enrollments migration completed successfully.');
        return self::SUCCESS;
    }
}
