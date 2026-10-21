<x-filament-panels::page>
    <style>
        .updater-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(18rem, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        .updater-version-card {
            border: 1px solid rgb(255 255 255 / 0.1);
            border-radius: 0.75rem;
            background: rgb(255 255 255 / 0.03);
            padding: 1.25rem;
        }

        .updater-version-layout {
            display: grid;
            grid-template-columns: 2.75rem minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }

        .updater-version-icon {
            display: flex;
            width: 2.75rem;
            height: 2.75rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background: rgb(245 158 11 / 0.12);
            color: rgb(251 191 36);
        }

        .updater-version-details {
            min-width: 0;
        }

        .updater-status {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        @media (max-width: 1023px) {
            .updater-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="updater-grid">
        <x-filament::section
            class="updater-panel"
            heading="Installed version"
            description="The commit currently running on this application."
            icon="heroicon-o-cube-transparent"
        >
            @if (filled($status['local'] ?? null))
                <div class="updater-version-card">
                    <div class="updater-version-layout">
                        <div class="updater-version-icon">
                            <x-filament::icon icon="heroicon-o-code-bracket" class="size-6" />
                        </div>
                        <div class="updater-version-details">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Version</p>
                            <p class="mt-1 break-all font-mono text-lg font-semibold tracking-tight text-gray-950 dark:text-white">{{ $status['local'] }}</p>
                            <p class="mt-3 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <x-filament::icon icon="heroicon-o-clock" class="size-4" />
                                Committed {{ \Illuminate\Support\Carbon::parse($status['local_date'])->format('M j, Y · g:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">The installed version could not be determined.</p>
            @endif
        </x-filament::section>

        <x-filament::section
            heading="Update status"
            description="Check the remote repository when you are ready."
            icon="heroicon-o-arrow-path"
        >
            @if (isset($status['error']))
                <div class="updater-status border border-danger-200 bg-danger-50 text-danger-700 dark:border-danger-400/30 dark:bg-danger-400/10 dark:text-danger-400">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="mt-0.5 size-5 shrink-0" />
                    <p class="text-sm leading-6">{{ $status['error'] }}</p>
                </div>
            @elseif ($hasCheckedForUpdates && ($status['available'] ?? false))
                <div class="updater-status border border-warning-200 bg-warning-50 text-warning-700 dark:border-warning-400/30 dark:bg-warning-400/10 dark:text-warning-400">
                    <x-filament::icon icon="heroicon-o-arrow-up-circle" class="mt-0.5 size-5 shrink-0" />
                    <div>
                        <p class="font-medium">Update available</p>
                        <p class="mt-1 text-sm opacity-80">A newer commit is ready to install.</p>
                    </div>
                </div>
            @elseif ($hasCheckedForUpdates)
                <div class="updater-status border border-success-200 bg-success-50 text-success-700 dark:border-success-400/30 dark:bg-success-400/10 dark:text-success-400">
                    <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 size-5 shrink-0" />
                    <div>
                        <p class="font-medium">You’re up to date</p>
                        <p class="mt-1 text-sm opacity-80">No newer commit was found.</p>
                    </div>
                </div>
            @else
                <div class="updater-status border border-info-200 bg-info-50 text-info-700 dark:border-info-400/30 dark:bg-info-400/10 dark:text-info-400">
                    <x-filament::icon icon="heroicon-o-information-circle" class="mt-0.5 size-5 shrink-0" />
                    <p class="text-sm leading-6">Run a check to see whether a newer version is available.</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
