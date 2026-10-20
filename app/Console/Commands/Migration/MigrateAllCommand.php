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
     */
    private array $pipeline = [
        ['command' => 'migrate:users',        'label' => 'Users'],
        ['command' => 'migrate:school-years', 'label' => 'School Years'],
        ['command' => 'migrate:sections',     'label' => 'Sections'],
        ['command' => 'migrate:subjects',     'label' => 'Subjects'],
        ['command' => 'migrate:students',     'label' => 'Students'],
        ['command' => 'migrate:enrollments',  'label' => 'Enrollments'],
        ['command' => 'migrate:homeroom',     'label' => 'Homerooms'],
        ['command' => 'migrate:comments',     'label' => 'Comments'],
        ['command' => 'migrate:attendance',   'label' => 'Attendance'],
        ['command' => 'migrate:awards',       'label' => 'Award'],
        ['command' => 'migrate:graduates',    'label' => 'Graduate'],
        ['command' => 'migrate:deportment',   'label' => 'Deportment'],
        ['command' => 'migrate:grades',        'label' => 'Grades'],
    ];

    public function handle(): int
    {
        $fresh      = $this->option('fresh');
        $skipGrades = $this->option('skip-grades');
        $startedAt  = now();
        $failed     = [];
        $skipped    = [];

        foreach ($this->pipeline as $step) {
            if (! $this->getApplication()?->has($step['command'])) {
                $this->error("Migration command [{$step['command']}] is not registered.");

                return self::FAILURE;
            }
        }

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
            $this->line("──────────────────────────────────────");

            // --- Skip: sections feature flag ---
            if ($step['label'] === 'Sections' && ! config('system-features.sections')) {
                $this->warn("⊘  Skipping: {$step['label']}");
                $this->line("   Reason: system-features.sections is disabled");
                $skipped[] = ['label' => $step['label'], 'reason' => 'Feature flag disabled'];
                $this->newLine();
                continue;
            }

            // --- Skip: deportment feature flag ---
            if ($step['label'] === 'Deportment' && ! config('system-features.homeroom_deportment')) {
                $this->warn("⊘  Skipping: {$step['label']}");
                $this->line("   Reason: system-features.homeroom_deportment is disabled");
                $skipped[] = ['label' => $step['label'], 'reason' => 'Feature flag disabled'];
                $this->newLine();
                continue;
            }

            // --- Skip: grades flag ---
            if ($skipGrades && $step['command'] === 'migrate:grades') {
                $this->warn("⊘  Skipping: {$step['label']}");
                $this->line("   Reason: --skip-grades flag was passed");
                $skipped[] = ['label' => $step['label'], 'reason' => '--skip-grades flag'];
                $this->newLine();
                continue;
            }

            $this->info("▶  {$step['label']}");
            $this->line("──────────────────────────────────────");

            $args = $fresh ? ['--fresh' => true] : [];

            $exitCode = $this->call($step['command'], $args);

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
            ->map(function ($s) use ($failed, $skipped) {
                $skippedLabels = array_column($skipped, 'label');
                $skippedEntry  = collect($skipped)->firstWhere('label', $s['label']);

                if (in_array($s['label'], $skippedLabels)) {
                    return [$s['label'], '⊘ Skipped', $skippedEntry['reason'] ?? ''];
                }

                return [
                    $s['label'],
                    in_array($s['label'], $failed) ? '✗ Failed' : '✓ Done',
                    '',
                ];
            })
            ->all();

        $this->table(['Step', 'Result', 'Note'], $rows);

        $this->newLine();
        $this->line("Total time: {$elapsed}s");

        if (! empty($skipped)) {
            $this->newLine();
            $this->warn('Skipped steps: ' . implode(', ', array_column($skipped, 'label')));
        }

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
