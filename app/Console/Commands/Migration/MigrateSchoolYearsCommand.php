<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\SchoolYearMigrationController;
use Illuminate\Console\Command;

class MigrateSchoolYearsCommand extends Command
{
    protected $signature = 'migrate:school-years
                            {--fresh : Reset school_years table before migrating}';

    protected $description = 'Migrate school years from old MySQL to new SQLite';

    public function handle(SchoolYearMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting school_years table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating school years...');

        $response = $controller->migrate();
        $data     = $response->getData(assoc: true);
        $inserted = $data['school_years']['inserted'] + $data['school_year_infos']['inserted'] + $data['school_year_card_templates']['inserted'];
        $skipped = $data['school_years']['skipped'] + $data['school_year_infos']['skipped'] + $data['school_year_card_templates']['skipped'];
        $errors = array_merge($data['school_years']['errors'], $data['school_year_infos']['errors'], $data['school_year_card_templates']['errors']);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rows inserted', $inserted ?? 0],
                ['Rows skipped',  $skipped  ?? 0],
                ['Errors',        count($errors ?? [])],
            ]
        );

        return $this->finish($data);
    }

    private function finish(array $data): int
    {
        if (! empty($errors)) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($errors as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('School years migration completed successfully.');
        return self::SUCCESS;
    }
}