<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\GradeMigrationController;
use Illuminate\Console\Command;

class MigrateGradesCommand extends Command
{
    protected $signature = 'migrate:grades
                            {--tables=1,2,3,4,5,6,7,8,9,10,11,12 : Comma-separated table numbers to migrate}
                            {--fresh : Reset grades table before migrating}';

    protected $description = 'Migrate grades_1 through grades_12 from old MySQL to new SQLite';

    public function handle(GradeMigrationController $controller): int
    {
        $tables = array_map(
            'intval',
            explode(',', $this->option('tables'))
        );

        if ($this->option('fresh')) {
            $this->warn('Resetting grades table...');
            $controller->reset();
            $this->info('Grades table cleared.');
        }

        $this->info('Starting grades migration...');
        $this->newLine();

        $totalInserted = 0;
        $totalSkipped  = 0;
        $totalErrors   = [];

        $progress = $this->output->createProgressBar(count($tables));
        $progress->setFormat(
            " %current%/%max% [%bar%] %percent:3s%%\n  Table: %message%\n"
        );
        $progress->start();

        foreach ($tables as $num) {
            $progress->setMessage("grades_{$num}");

            $response = $controller->migrateTable($num);
            $data     = $response->getData(assoc: true);

            $totalInserted += $data['inserted'] ?? 0;
            $totalSkipped  += $data['skipped']  ?? 0;
            $totalErrors    = array_merge($totalErrors, $data['errors'] ?? []);

            $progress->advance();
        }

        $progress->finish();
        $this->newLine(2);

        // Summary table
        $this->table(
            ['Metric', 'Count'],
            [
                ['Tables processed', count($tables)],
                ['Rows inserted',    $totalInserted],
                ['Rows skipped',     $totalSkipped],
                ['Errors',           count($totalErrors)],
            ]
        );

        if (! empty($totalErrors)) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($totalErrors as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Grades migration completed successfully.');

        return self::SUCCESS;
    }
}