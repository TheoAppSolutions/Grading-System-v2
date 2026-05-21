<?php

namespace App\Filament\Admin\Resources\School\Sections;

use App\Filament\Admin\Resources\School\Sections\Pages\CreateSection;
use App\Filament\Admin\Resources\School\Sections\Pages\EditSection;
use App\Filament\Admin\Resources\School\Sections\Pages\ListSections;
use App\Filament\Admin\Resources\School\Sections\RelationManagers;
use App\Filament\Admin\Resources\School\Sections\Schemas\SectionForm;
use App\Filament\Admin\Resources\School\Sections\Schemas\SectionInfolist;
use App\Filament\Admin\Resources\School\Sections\Tables\SectionsTable;
use App\Models\Section;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string | UnitEnum | null $navigationGroup = 'School Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Section / Strand';

    public static function form(Schema $schema): Schema
    {
        return SectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentSectionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSections::route('/'),
            'create' => CreateSection::route('/create'),
            'edit' => EditSection::route('/{record}/edit'),
        ];
    }
}
