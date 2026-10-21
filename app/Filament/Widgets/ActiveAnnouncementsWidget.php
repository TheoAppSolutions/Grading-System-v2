<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Marcelodelgado\Announcements\Filament\Widgets\AnnouncementsWidget;

class ActiveAnnouncementsWidget extends AnnouncementsWidget
{
    public function getAnnouncements(): Collection
    {
        return Announcement::query()
            ->visibleForDashboard()
            ->targetedTo(auth()->user())
            ->orderedForDisplay()
            ->whereDoesntHave('users', function ($query): void {
                $query->where('users.id', auth()->id())
                    ->whereNotNull('announcement_user.dismissed_at');
            })
            ->get();
    }

    public function dismiss(int $announcementId): void
    {
        $announcement = Announcement::query()->find($announcementId);

        if ($announcement?->is_dismissible) {
            DB::table('announcement_user')->updateOrInsert(
                ['announcement_id' => $announcementId, 'user_id' => auth()->id()],
                ['dismissed_at' => now()],
            );
        }

        $this->dispatch('announcement-dismissed');
    }
}
