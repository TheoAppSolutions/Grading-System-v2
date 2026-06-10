<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\SectionMigrationController;
use Illuminate\Console\Command;

class MigrateSectionsCommand extends Command
{
    protected $signature = 'migrate:sections
                            {--fresh : Reset sections table before migrating}';

    protected $description = 'Migrate sections from old MySQL to new SQLite';

    public function handle(SectionMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting sections table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating sections...');

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

        foreach (['unfilled_grade_level_id', 'unfilled_school_year_id'] as $key) {
            if (! empty($data[$key])) {
                $this->warn("  ⚠  {$data[$key]} row(s) with unresolved {$key}.");
            }
        }

        if (! empty($data['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($data['errors'] as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Sections migration completed successfully.');
        return self::SUCCESS;
    }
}
