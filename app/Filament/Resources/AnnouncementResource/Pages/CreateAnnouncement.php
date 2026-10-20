<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Marcelodelgado\Announcements\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement as PackageCreateAnnouncement;

class CreateAnnouncement extends PackageCreateAnnouncement
{
    protected static string $resource = AnnouncementResource::class;
}
