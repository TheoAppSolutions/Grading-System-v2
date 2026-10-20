<x-filament-panels::page>
    {{ $this->content }}

    @if (! \App\Models\SchoolYear::getActive())
        <div class="rounded-xl border border-warning-300 bg-warning-50 p-4 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-950 dark:text-warning-200">
            <p class="font-semibold">No active school year is configured.</p>
            <p class="mt-1">
                @if (auth()->user()?->isAdmin())
                    Create or activate a school year in the admin panel before using enrollment, grading, homeroom, or attendance features.
                @else
                    Ask an administrator to configure an active school year.
                @endif
            </p>
        </div>
    @endif
</x-filament-panels::page>
