<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\AttendanceMigrationController;
use Illuminate\Console\Command;

class MigrateAttendanceCommand extends Command
{
    protected $signature = 'migrate:attendance
                            {--fresh : Reset attendance table before migrating}';

    protected $description = 'Migrate attendance from old MySQL to new SQLite';

    public function handle(AttendanceMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting attendance table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating attendance...');

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
        $this->info('Attendance migration completed successfully.');
        return self::SUCCESS;
    }
}
