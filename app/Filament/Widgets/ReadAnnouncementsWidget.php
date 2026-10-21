<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class ReadAnnouncementsWidget extends Widget
{
    protected static ?int $sort = -3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.read-announcements-widget';

    public static function canView(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return Announcement::query()
            ->join('announcement_user', 'announcement_user.announcement_id', '=', 'announcements.id')
            ->where('announcement_user.user_id', auth()->id())
            ->whereNotNull('announcement_user.dismissed_at')
            ->where('announcements.is_active', true)
            ->targetedTo(auth()->user())
            ->exists();
    }

    #[On('announcement-dismissed')]
    public function refreshAnnouncements(): void
    {
        // Re-render the widget after an announcement is dismissed.
    }

    /**
     * @return Collection<int, Announcement>
     */
    public function getReadAnnouncements(): Collection
    {
        return Announcement::query()
            ->select('announcements.*')
            ->join('announcement_user', 'announcement_user.announcement_id', '=', 'announcements.id')
            ->where('announcement_user.user_id', auth()->id())
            ->whereNotNull('announcement_user.dismissed_at')
            ->where('announcements.is_active', true)
            ->targetedTo(auth()->user())
            ->orderByDesc('announcement_user.dismissed_at')
            ->get();
    }
}
