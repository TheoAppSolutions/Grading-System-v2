<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Marcelodelgado\Announcements\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement as PackageEditAnnouncement;

class EditAnnouncement extends PackageEditAnnouncement
{
    protected static string $resource = AnnouncementResource::class;
}
