<?php

namespace App\Filament\Plugins;

use App\Filament\Resources\AnnouncementResource;
use App\Filament\Widgets\ActiveAnnouncementsWidget;
use Filament\Panel;
use Marcelodelgado\Announcements\AnnouncementsPlugin as PackageAnnouncementsPlugin;

class AnnouncementsPlugin extends PackageAnnouncementsPlugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->resources([AnnouncementResource::class])
            ->widgets([ActiveAnnouncementsWidget::class]);
    }
}
