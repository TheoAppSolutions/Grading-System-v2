<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\CommentsMigrationController;
use Illuminate\Console\Command;

class MigrateCommentsCommand extends Command
{
    protected $signature = 'migrate:comments
                            {--fresh : Reset comments table before migrating}';

    protected $description = 'Migrate comments from old MySQL to new SQLite';

    public function handle(CommentsMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting comments table...');
            $controller->reset();
            $this->newLine();
        }

        $this->info('Migrating comments...');

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
        $this->info('Comment migration completed successfully.');
        return self::SUCCESS;
    }
}
