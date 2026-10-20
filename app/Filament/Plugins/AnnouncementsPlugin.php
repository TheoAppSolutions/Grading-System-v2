<?php

namespace App\Filament\Plugins;

use App\Filament\Resources\AnnouncementResource;
use Filament\Panel;
use Marcelodelgado\Announcements\AnnouncementsPlugin as PackageAnnouncementsPlugin;
use Marcelodelgado\Announcements\Filament\Widgets\AnnouncementsWidget;

class AnnouncementsPlugin extends PackageAnnouncementsPlugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->resources([AnnouncementResource::class])
            ->widgets([AnnouncementsWidget::class]);
    }
}
