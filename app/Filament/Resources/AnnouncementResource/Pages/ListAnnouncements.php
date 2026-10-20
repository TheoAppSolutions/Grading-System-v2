<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions\CreateAction;
use Marcelodelgado\Announcements\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements as PackageListAnnouncements;

class ListAnnouncements extends PackageListAnnouncements
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
