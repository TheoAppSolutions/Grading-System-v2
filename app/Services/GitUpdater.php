<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class GitUpdater
{
    private const CACHE_KEY = 'git.update_available';

    public function hasUpdate(bool $refresh = false): bool
    {
        if (! $refresh) {
            return Cache::get(self::CACHE_KEY, false);
        }

        try {
            $status = $this->status();
            Cache::put(self::CACHE_KEY, $status['available'], now()->addMinutes(15));

            return $status['available'];
        } catch (\Throwable $exception) {
            report($exception);

            return Cache::get(self::CACHE_KEY, false);
        }
    }

    /**
     * @return array{available: bool, branch: string, local: string, remote: string}
     */
    public function status(): array
    {
        return $this->inspect(fetch: true);
    }

    /**
     * Fetch the configured branch and fast-forward the working tree to it.
     *
     * @return array{available: bool, updated: bool, branch: string, message: string}
     */
    public function update(): array
    {
        $status = $this->inspect(fetch: true);

        if (! $status['available']) {
            Cache::forget(self::CACHE_KEY);

            return [
                ...$status,
                'updated' => false,
                'message' => 'The application is already up to date.',
            ];
        }

        $repoPath = base_path();
        $dirty = $this->run('git status --porcelain', $repoPath);

        if ($dirty->output() !== '') {
            throw new RuntimeException('The application has local changes. Commit or stash them before updating.');
        }

        $pull = $this->run(
            'git pull --ff-only origin '.escapeshellarg($status['branch']),
            $repoPath,
            timeout: 60,
        );

        if (! $pull->successful()) {
            throw new RuntimeException(trim($pull->errorOutput()) ?: 'Git could not pull the remote update.');
        }

        Cache::forget(self::CACHE_KEY);

        return [
            ...$status,
            'updated' => true,
            'message' => 'The application was updated successfully.',
        ];
    }

    /**
     * @return array{available: bool, branch: string, local: string, remote: string}
     */
    private function inspect(bool $fetch): array
    {
        $repoPath = base_path();
        $branch = trim($this->run('git branch --show-current', $repoPath)->output());

        if ($branch === '' || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._\/-]*$/', $branch)) {
            throw new RuntimeException('The application is not on a valid local Git branch.');
        }

        if ($fetch) {
            $result = $this->run(
                'git fetch --prune origin '.escapeshellarg($branch),
                $repoPath,
                timeout: 30,
            );

            if (! $result->successful()) {
                throw new RuntimeException(trim($result->errorOutput()) ?: 'Git could not fetch the remote branch.');
            }
        }

        $local = trim($this->run('git rev-parse HEAD', $repoPath)->output());
        $remote = trim($this->run(
            'git rev-parse --verify '.escapeshellarg('refs/remotes/origin/'.$branch),
            $repoPath,
        )->output());

        if ($local === '' || $remote === '') {
            throw new RuntimeException('Could not determine the local and remote Git revisions.');
        }

        return [
            'available' => $local !== $remote,
            'branch' => $branch,
            'local' => $local,
            'remote' => $remote,
        ];
    }

    private function run(string $command, string $path, int $timeout = 10)
    {
        return Process::path($path)->timeout($timeout)->run($command);
    }
}
