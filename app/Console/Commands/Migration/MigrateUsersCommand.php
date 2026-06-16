<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\UserMigrationController;
use App\Http\Controllers\Migration\UserRoleMigrationController;
use Illuminate\Console\Command;

class MigrateUsersCommand extends Command
{
    protected $signature = 'migrate:users
                            {--fresh : Reset users and user_roles tables before migrating}';

    protected $description = 'Migrate users and user_roles from old MySQL to new SQLite';

    public function handle(
        UserMigrationController      $controller,
        UserRoleMigrationController $rolesController,
    ): int {
        if ($this->option('fresh')) {
            $this->warn('Resetting user_roles table...');
            $rolesController->reset();
            $this->warn('Resetting users table...');
            $controller->reset();
            $this->info('Tables cleared.');
            $this->newLine();
        }

        // ── Users ─────────────────────────────────────────────────────────────
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
                ['Unfilled emails', $data['unfilled'] ?? 0],
            ]
        );

        if (! empty($data['errors'])) {
            $this->newLine();
            $this->error('Errors in users:');
            foreach ($data['errors'] as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
        }

        if (($data['unfilled'] ?? 0) > 0) {
            $this->newLine();
            $this->warn("⚠  {$data['unfilled']} user(s) have placeholder emails ({id}@placeholder.email).");
            $this->warn('   Run GET /migrate/users/unfilled to list them.');
        }

        $usersHadErrors = ! empty($data['errors']);

        // ── User Roles ────────────────────────────────────────────────────────
        $this->newLine();
        $this->info('Migrating user_roles...');

        $rolesResponse = $rolesController->migrate();
        $rolesData     = $rolesResponse->getData(assoc: true);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rows inserted',   $rolesData['inserted']   ?? 0],
                ['Rows skipped',    $rolesData['skipped']    ?? 0],
                ['Errors',          count($rolesData['errors'] ?? [])],
                ['Unresolved user_id', $rolesData['unresolved'] ?? 0],
            ]
        );

        if (! empty($rolesData['errors'])) {
            $this->newLine();
            $this->error('Errors in user_roles:');
            foreach ($rolesData['errors'] as $err) {
                $this->line("  • Row [{$err['row']}]: {$err['message']}");
            }
        }

        if (($rolesData['unresolved'] ?? 0) > 0) {
            $this->newLine();
            $this->warn("⚠  {$rolesData['unresolved']} user_role row(s) could not resolve user_id.");
            $this->warn('   The user may not have been migrated.');
        }

        $rolesHadErrors = ! empty($rolesData['errors']);

        // ── Summary ───────────────────────────────────────────────────────────
        $this->newLine();

        if ($usersHadErrors || $rolesHadErrors) {
            $this->error('Users migration completed with errors.');
            return self::FAILURE;
        }

        $this->info('✓  Users and user_roles migrated successfully.');
        return self::SUCCESS;
    }
}