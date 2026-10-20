<?php

namespace App\Support\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait CleansUpPhotoFiles
{
    /**
     * Remove photo files that are no longer referenced by the form.
     *
     * This is intentionally called from an after-save hook. Removing a file
     * in the upload component only changes the Livewire state, so cancelling
     * an edit leaves the existing file untouched.
     */
    protected function cleanupUnreferencedPhotoFiles(string $directory, mixed $state): void
    {
        $directory = trim($directory, '/');
        $referencedPaths = collect(Arr::flatten(Arr::wrap($state)))
            ->filter(fn (mixed $path): bool => is_string($path) && str_starts_with($path, $directory.'/'))
            ->values()
            ->all();

        foreach (Storage::disk('public')->files($directory) as $path) {
            if (! in_array($path, $referencedPaths, true)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
