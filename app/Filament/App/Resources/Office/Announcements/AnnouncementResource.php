<?php

namespace App\Filament\App\Resources\Office\Announcements;

use App\Filament\App\Resources\Office\Announcements\Pages\CreateAnnouncement;
use App\Filament\App\Resources\Office\Announcements\Pages\EditAnnouncement;
use App\Filament\App\Resources\Office\Announcements\Pages\ListAnnouncements;
use App\Filament\App\Resources\Office\Announcements\Schemas\AnnouncementForm;
use App\Filament\App\Resources\Office\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static string | UnitEnum | null $navigationGroup = 'Office Menu';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
