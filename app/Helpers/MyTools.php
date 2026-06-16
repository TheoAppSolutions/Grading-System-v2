<?php

namespace App\Helpers;

use App\Models;
use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\School\SchoolYears\SchoolYearResource;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

class MyTools
{
    public static function systemLogo(): string
    {
        return url('images/logo.png?' . time());
    }

    public static function getMonths(): array
    {
        return [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    public static function getSchoolYear()
    {
        if (isset($_GET['school_year'])) {
            return (string) $_GET['school_year'];
        }

        $schoolYear = Models\SchoolYear::where('active', 1)
            ->select(['id', 'school_year'])
            ->first();

        if ($schoolYear === null) {
            Notification::make()
                ->title('No active school year found.')
                ->warning()
                ->send();

            return false;
        }

        return $schoolYear;
    }

    public static function ordinal(int $number): string
    {
        $abs = abs($number);
        $mod100 = $abs % 100;
        $mod10  = $abs % 10;

        $suffix = match (true) {
            $mod100 >= 11 && $mod100 <= 13 => 'th',
            $mod10 === 1                   => 'st',
            $mod10 === 2                   => 'nd',
            $mod10 === 3                   => 'rd',
            default                        => 'th',
        };

        return $number . $suffix;
    }

    public static function getImage(int|string $id = '', string $type = 'users'): string
    {
        $relativePath = match ($type) {
            'users'    => 'images/users/' . ($id !== '' ? $id : Auth::id()) . '.jpg',
            'students' => 'images/students/' . $id . '.jpg',
            default    => '',
        };

        if ($relativePath === '' || !file_exists(public_path($relativePath))) {
            return self::systemLogo();
        }

        return url($relativePath . '?' . time());
    }

    public static function getStudentFullname(int|string $studentId): string
    {
        $student = Models\Student::findOrFail($studentId);

        return trim("{$student->firstname} {$student->lastname}");
    }

    public static function checkMark(): string
    {
        $src = e(url('checkmark.png'));
        return "<img src=\"{$src}\" style=\"height:20px;width:20px;\" alt=\"✓\">";
    }

    public static function cleanString(string $string): string
    {
        return trim(preg_replace('/[^A-Za-z0-9\-]/', '', $string) ?? '');
    }

public static function fetchGit(bool $forceFetch = false): bool
{
    $cacheKey = 'git.update_available';

    // Non-login pages: just return whatever was last computed, no git calls at all
    if (! $forceFetch) {
        return Cache::get($cacheKey, false);
    }

    try {
        $repoPath = base_path();

        $branch = trim(
            Process::path($repoPath)->run('git rev-parse --abbrev-ref HEAD')->output()
        );

        if ($branch === '' || $branch === 'HEAD') {
            // detached HEAD or not a git repo, just fall back to cache
            return Cache::get($cacheKey, false);
        }

        // The only actual network call in this whole method
        Process::path($repoPath)
            ->timeout(10)
            ->run('git fetch origin ' . escapeshellarg($branch));

        $localHash  = trim(Process::path($repoPath)->run('git rev-parse HEAD')->output());
        $remoteHash = trim(Process::path($repoPath)->run('git rev-parse origin/' . escapeshellarg($branch))->output());

        $hasUpdate = $localHash !== '' && $remoteHash !== '' && $localHash !== $remoteHash;

        // Cache it so the next N pages don't trigger fetchGit() with forceFetch again
        Cache::put($cacheKey, $hasUpdate, now()->addMinutes(15));

        return $hasUpdate;
    } catch (\Throwable $e) {
        report($e);

        return Cache::get($cacheKey, false);
    }
}
}
