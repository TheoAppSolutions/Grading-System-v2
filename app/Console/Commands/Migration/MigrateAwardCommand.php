<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\AwardsMigrationController;
use Illuminate\Console\Command;

class MigrateAwardCommand extends Command
{
    protected $signature = 'migrate:Awards
                            {--fresh : Reset Awards table before migrating}';

    protected $description = 'Migrate Awards from old MySQL to new SQLite';

    public function handle(AwardsMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting Awards table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating Awards...');

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
        $this->info('Awards migration completed successfully.');
        return self::SUCCESS;
    }
}
