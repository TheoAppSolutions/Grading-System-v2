<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\HomeroomMigrationController;
use Illuminate\Console\Command;

class MigrateHomeroomsCommand extends Command
{
    protected $signature = 'migrate:homeroom
                            {--fresh : Reset homerooms table before migrating}';

    protected $description = 'Migrate sections from old MySQL to new SQLite';

    public function handle(HomeroomMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting homerooms table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating homerooms...');

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
        $this->info('Homerooms migration completed successfully.');
        return self::SUCCESS;
    }
}
