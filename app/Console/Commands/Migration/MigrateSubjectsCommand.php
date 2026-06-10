<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\SubjectMigrationController;
use App\Http\Controllers\Migration\SubjectUserMigrationController;
use App\Http\Controllers\Migration\SubjectUnitMigrationController;
use App\Http\Controllers\Migration\SubjectExcludedMigrationController;
use Illuminate\Console\Command;

class MigrateSubjectsCommand extends Command
{
    protected $signature = 'migrate:subjects
                            {--fresh : Reset subject tables before migrating}';

    protected $description = 'Migrate subjects, subject_users, subject_units, and subject_excluded from old MySQL to new SQLite';

    public function handle(
        SubjectMigrationController         $subjects,
        SubjectUserMigrationController     $users,
        SubjectUnitMigrationController     $units,
        SubjectExcludedMigrationController $excluded,
    ): int {
        $steps = [
            'subjects'          => $subjects,
            'subject_users'     => $users,
            'subject_units'     => $units,
            'subject_excluded'  => $excluded,
        ];

        if ($this->option('fresh')) {
            $this->warn('Resetting subject tables...');
            foreach (array_reverse($steps) as $controller) {
                $controller->reset();
            }
            $this->newLine();
        }

        $hasErrors = false;

        foreach ($steps as $table => $controller) {
            $this->info("Migrating {$table}...");

            $response = $controller->migrate();
            $data     = $response->getData(assoc: true);

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Rows inserted', $data['inserted'] ?? 0],
                    ['Rows skipped',  $data['skipped']  ?? 0],
                    ['Errors',        count($data['errors'] ?? [])],
                ]
            );

            if (! empty($data['errors'])) {
                $hasErrors = true;
                $this->error("Errors in {$table}:");
                foreach ($data['errors'] as $err) {
                    $this->line("  • Row [{$err['row']}]: {$err['message']}");
                }
            }

            // Warn about any unresolved FKs
            foreach (['unfilled_grade_level_id', 'unfilled_school_year_id'] as $key) {
                if (! empty($data[$key])) {
                    $this->warn("  ⚠  {$data[$key]} row(s) with unresolved {$key} in {$table}.");
                }
            }

            $this->newLine();
        }

        if ($hasErrors) {
            return self::FAILURE;
        }

        $this->info('Subjects migration completed successfully.');
        return self::SUCCESS;
    }
}
