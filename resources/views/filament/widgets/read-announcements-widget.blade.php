@php
    use Marcelodelgado\Announcements\Enums\AnnouncementType;

    $announcements = $this->getReadAnnouncements();
@endphp

<x-filament-widgets::widget class="fi-wi-read-announcements">
    @if ($announcements->isNotEmpty())
        <x-filament::section>
            <details>
                <summary class="cursor-pointer select-none text-sm font-semibold text-gray-950 dark:text-white">
                    Previous announcements
                    <span class="ms-2 text-xs font-normal text-gray-500 dark:text-gray-400">
                        ({{ $announcements->count() }})
                    </span>
                </summary>

                <div class="mt-4 space-y-3">
                    @foreach ($announcements as $announcement)
                        @php
                            $borderClass = match ($announcement->type) {
                                AnnouncementType::Danger => 'border-danger-600 dark:border-danger-500',
                                AnnouncementType::Warning => 'border-warning-600 dark:border-warning-500',
                                AnnouncementType::Info => 'border-info-600 dark:border-info-500',
                                AnnouncementType::Success => 'border-success-600 dark:border-success-500',
                            };

                            $icon = match ($announcement->type) {
                                AnnouncementType::Danger => 'heroicon-o-exclamation-circle',
                                AnnouncementType::Warning => 'heroicon-o-exclamation-triangle',
                                AnnouncementType::Info => 'heroicon-o-information-circle',
                                AnnouncementType::Success => 'heroicon-o-check-circle',
                            };

                            $iconColorCss = match ($announcement->type) {
                                AnnouncementType::Danger => 'light-dark(#dc2626, #f87171)',
                                AnnouncementType::Warning => 'light-dark(#d97706, #fbbf24)',
                                AnnouncementType::Info => 'light-dark(#0369a1, #38bdf8)',
                                AnnouncementType::Success => 'light-dark(#15803d, #4ade80)',
                            };
                        @endphp

                        <article class="overflow-hidden rounded-xl bg-gray-50 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                            <div class="border-s-4 {{ $borderClass }}" style="display: flex; align-items: flex-start; gap: 0.75rem 1rem; padding-block: 0.75rem; padding-inline: 1rem;">
                                <div style="flex-shrink: 0; line-height: 0;">
                                    <x-filament::icon
                                        :icon="$icon"
                                        style="width: 2rem; height: 2rem; color: {{ $iconColorCss }};"
                                    />
                                </div>

                                <div style="min-width: 0; flex: 1 1 0%;" class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <h1
                                            class="text-gray-950 dark:text-white"
                                            style="margin: 0; font-size: 1.125rem; font-weight: 800 !important; line-height: 1.35; color: light-dark(#030712, #ffffff);"
                                        >
                                            {{ $announcement->title }}
                                        </h1>
                                    </div>
                                    <p class="whitespace-pre-wrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $announcement->body }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </details>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
