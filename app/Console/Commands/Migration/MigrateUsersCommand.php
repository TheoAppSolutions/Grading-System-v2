<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\UserMigrationController;
use Illuminate\Console\Command;

class MigrateUsersCommand extends Command
{
    protected $signature = 'migrate:users
                            {--fresh : Reset users table before migrating}';

    protected $description = 'Migrate users from old MySQL to new SQLite';

    public function handle(UserMigrationController $controller): int
    {
        if ($this->option('fresh')) {
            $this->warn('Resetting users table...');
            $controller->reset();
            $this->info('Users table cleared.');
            $this->newLine();
        }

        $this->info('Migrating users...');

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
        }

        // Remind about placeholder emails
        $this->newLine();
        $this->warn('⚠  Emails were auto-generated as placeholders (user.{id}@placeholder.local).');
        $this->warn('   Run GET /migrate/users/unfilled or check the DB to update them.');

        if (! empty($data['errors'])) {
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Users migration completed successfully.');

        return self::SUCCESS;
    }
}