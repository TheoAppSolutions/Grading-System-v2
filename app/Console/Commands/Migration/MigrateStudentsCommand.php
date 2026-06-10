<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\StudentMigrationController;
use App\Http\Controllers\Migration\StudentInfoMigrationController;
use App\Http\Controllers\Migration\StudentSectionMigrationController;
use Illuminate\Console\Command;

class MigrateStudentsCommand extends Command
{
    protected $signature = 'migrate:students
                            {--fresh : Reset student tables before migrating}';

    protected $description = 'Migrate students, student_infos, and student_sections from old MySQL to new SQLite';

    public function handle(
        StudentMigrationController        $students,
        StudentInfoMigrationController    $infos,
        StudentSectionMigrationController $sections,
    ): int {
        $steps = [
            'students'         => $students,
            'student_infos'    => $infos,
        ];

        // if (config('app.name') === 'BIS') {
        //     $steps['student_sections'] = $sections;
        // }

        if ($this->option('fresh')) {
            $this->warn('Resetting student tables...');
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

            if (isset($data['unfilled_section_id']) && $data['unfilled_section_id'] > 0) {
                $this->warn("  ⚠  {$data['unfilled_section_id']} row(s) with unresolved section_id in {$table}.");
            }

            if (isset($data['unfilled_school_year_id']) && $data['unfilled_school_year_id'] > 0) {
                $this->warn("  ⚠  {$data['unfilled_school_year_id']} row(s) with unresolved school_year_id in {$table}.");
            }

            $this->newLine();
        }

        if ($hasErrors) {
            return self::FAILURE;
        }

        $this->info('Students migration completed successfully.');
        return self::SUCCESS;
    }
}
