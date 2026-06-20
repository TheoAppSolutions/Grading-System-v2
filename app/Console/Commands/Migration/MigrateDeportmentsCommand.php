<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\DeportmentMigrationController;
use Illuminate\Console\Command;

class MigrateDeportmentsCommand extends Command
{
    protected $signature = 'migrate:deportment
                            {--fresh : Reset Deportments table before migrating}';

    protected $description = 'Migrate sections from old MySQL to new SQLite';

    public function handle(DeportmentMigrationController $controller): int
    {
        if (!config('system-features.homeroom_deportment')) {
            $this->info('Homeroom Deportment Feature is disabled');
            $this->warn('Skipping Deportments Migration...');
            $this->newLine();
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Resetting Deportments table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating Deportments...');

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

        if (! empty($data['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($data['errors'] as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Deportments migration completed successfully.');
        return self::SUCCESS;
    }
}
