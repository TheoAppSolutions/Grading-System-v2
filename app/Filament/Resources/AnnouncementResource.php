<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Marcelodelgado\Announcements\Filament\Resources\AnnouncementResource as PackageAnnouncementResource;

class AnnouncementResource extends PackageAnnouncementResource
{
    public static function table(Table $table): Table
    {
        return parent::table($table)->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
