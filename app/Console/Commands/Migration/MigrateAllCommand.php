<?php

namespace App\Console\Commands\Migration;

use Illuminate\Console\Command;

class MigrateAllCommand extends Command
{
    protected $signature = 'migrate:all
                            {--fresh : Reset all tables before migrating}
                            {--skip-grades : Skip grades migration (useful for testing)}';

    protected $description = 'Run all data migrations sequentially: school_years → users → sections → students → subjects → enrollments → grades';

    /**
     * Ordered pipeline — each step depends on the ones before it.
     *
     *  1. school-years   — base FK for almost everything
     *  2. users          — FK for grades.user_id
     *  3. sections       — FK for student_sections + enrollments
     *  4. students       — core student rows + infos + sections
     *  5. subjects       — FK for grades.subject_id
     *  6. enrollments    — depends on school_years + sections + students
     *  7. grades         — depends on everything above
     */
    private array $pipeline = [
        ['command' => 'migrate:users',        'label' => 'Users'],
        ['command' => 'migrate:school-years', 'label' => 'School Years'],
        ['command' => 'migrate:subjects',     'label' => 'Subjects'],
        ['command' => 'migrate:students',     'label' => 'Students'],
        // ['command' => 'migrate:sections',     'label' => 'Sections'],
        ['command' => 'migrate:enrollments',  'label' => 'Enrollments'],
        ['command' => 'migrate:grades',       'label' => 'Grades'],
        ['command' => 'migrate:homeroom',       'label' => 'Homerooms'],
    ];

    public function handle(): int
    {
        $fresh       = $this->option('fresh');
        $skipGrades  = $this->option('skip-grades');
        $startedAt   = now();
        $failed      = [];

        $this->newLine();
        $this->line('╔══════════════════════════════════════╗');
        $this->line('║       Full Database Migration        ║');
        $this->line('╚══════════════════════════════════════╝');
        $this->newLine();

        if ($fresh) {
            $this->warn('⚠  --fresh flag detected. All target tables will be reset before migrating.');
            if (! $this->confirm('Are you sure you want to reset and re-migrate everything?')) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
            $this->newLine();
        }

        foreach ($this->pipeline as $step) {
            if ($skipGrades && $step['command'] === 'migrate:grades') {
                $this->warn("Skipping: {$step['label']}");
                $this->newLine();
                continue;
            }

            $this->line("──────────────────────────────────────");
            $this->info("▶  {$step['label']}");
            $this->line("──────────────────────────────────────");

            $args = $fresh ? ['--fresh' => true] : [];

            // migrate:grades uses --tables instead of --fresh internally
            if ($step['command'] === 'migrate:grades' && $fresh) {
                $args = [];
                $this->callSilently('migrate:grades', ['--fresh' => true]);
                $exitCode = $this->call('migrate:grades');
            } else {
                $exitCode = $this->call($step['command'], $args);
            }

            if ($exitCode !== self::SUCCESS) {
                $failed[] = $step['label'];
                $this->newLine();
                $this->error("✗  {$step['label']} failed. See errors above.");

                if (! $this->confirm('Continue with the next step anyway?', default: false)) {
                    $this->newLine();
                    $this->error('Migration aborted by user.');
                    return self::FAILURE;
                }
            }

            $this->newLine();
        }

        // ── Summary ──────────────────────────────────────────────────────────
        $elapsed = now()->diffInSeconds($startedAt);

        $this->line('╔══════════════════════════════════════╗');
        $this->line('║           Migration Summary          ║');
        $this->line('╚══════════════════════════════════════╝');
        $this->newLine();

        $rows = collect($this->pipeline)
            ->filter(fn ($s) => ! ($skipGrades && $s['command'] === 'migrate:grades'))
            ->map(fn ($s) => [
                $s['label'],
                in_array($s['label'], $failed) ? '✗ Failed' : '✓ Done',
            ])
            ->all();

        $this->table(['Step', 'Result'], $rows);

        $this->newLine();
        $this->line("Total time: {$elapsed}s");

        if (! empty($failed)) {
            $this->newLine();
            $this->error('Migration completed with failures: ' . implode(', ', $failed));
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('✓  All migrations completed successfully.');
        return self::SUCCESS;
    }
}
