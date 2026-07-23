<?php

namespace App\Console\Commands\Migration;

use App\Http\Controllers\Migration\SubjectCodeCleanupController;
use Illuminate\Console\Command;

/**
 * Console wrapper around SubjectCodeCleanupController.
 *
 * Usage:
 *   php artisan migrate:clean-subjects            → preview only, asks before applying
 *   php artisan migrate:clean-subjects --dry-run   → preview only, never applies
 *   php artisan migrate:clean-subjects --force     → applies without confirmation
 */
class MigrateCleanSubjectCodes extends Command
{
    protected $signature = 'migrate:clean-subjects
        {--dry-run : Show the plan only, never write to the database}
        {--force : Apply the plan without the confirmation prompt}';

    protected $description = 'Strip grade suffixes from subjects.subject_code, dedupe, and remap child tables';

    public function handle(SubjectCodeCleanupController $controller): int
    {
        $this->info('Building cleanup plan...');
        $plan = $controller->preview()->getData(true);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            collect($plan['summary'])->map(fn($count, $label) => [str_replace('_', ' ', $label), $count])->toArray()
        );

        if (!empty($plan['irregular_suffix'])) {
            $this->warn('Irregular suffixes skipped (review manually):');
            $this->table(
                ['id', 'subject_code', 'reason'],
                collect($plan['irregular_suffix'])->map(fn($r) => [$r['id'], $r['subject_code'], $r['reason']])->toArray()
            );
        }

        if (!empty($plan['code_collisions'])) {
            $this->warn('Code collisions resolved with a fallback code:');
            $this->table(
                ['id', 'wanted_code', 'assigned_code', 'reason'],
                collect($plan['code_collisions'])->map(fn($r) => [$r['id'], $r['wanted_code'], $r['assigned_code'], $r['reason']])->toArray()
            );
        }

        if (!empty($plan['conflicts_resolved'])) {
            $this->warn('Code collisions resolved via title-based disambiguation:');
            foreach ($plan['conflicts_resolved'] as $conflict) {
                $this->line("  {$conflict['stripped_code']} kept for \"{$conflict['kept_for']}\"; other titles reassigned:");
                foreach ($conflict['rows'] as $row) {
                    $this->line("    id {$row['id']} ({$row['old_code']} → {$row['new_code']}) — \"{$row['subject_title']}\"");
                }
            }
        }

        if ($plan['summary']['renames'] === 0 && $plan['summary']['duplicates_to_delete'] === 0) {
            $this->info('Nothing to clean up.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run only — no changes written.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Apply this plan to the database?', false)) {
            $this->info('Aborted — no changes written.');
            return self::SUCCESS;
        }

        $this->info('Applying...');
        $result = $controller->migrate()->getData(true);

        $this->newLine();
        $this->info('Done.');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Backup file', $result['backup']],
                ['Renamed', $result['renamed']],
                ['Duplicates deleted', $result['duplicates_deleted']],
                ['Conflicts resolved', $result['conflicts_resolved']],
                ['Irregular suffix skipped', $result['irregular_suffix_skipped']],
                ['Code collisions deduped', $result['code_collisions_deduped']],
                ['Child tables id-remapped', implode(', ', $result['child_tables_id_remapped'])],
            ]
        );

        return self::SUCCESS;
    }
}