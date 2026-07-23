<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\GraduatesMigrationController;
use Illuminate\Console\Command;

class MigrateGraduateCommand extends Command
{
    protected $signature = 'migrate:graduates
                            {--fresh : Reset Graduates table before migrating}';

    protected $description = 'Migrate Graduates from old MySQL to new SQLite';

    public function handle(GraduatesMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting Graduates table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating Graduates...');

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
        $this->info('Graduates migration completed successfully.');
        return self::SUCCESS;
    }
}
