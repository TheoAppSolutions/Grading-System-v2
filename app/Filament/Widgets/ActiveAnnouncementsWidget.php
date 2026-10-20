<?php

namespace App\Filament\Widgets;

use Marcelodelgado\Announcements\Filament\Widgets\AnnouncementsWidget;

class ActiveAnnouncementsWidget extends AnnouncementsWidget
{
    public function dismiss(int $announcementId): void
    {
        parent::dismiss($announcementId);

        $this->dispatch('announcement-dismissed');
    }
}
