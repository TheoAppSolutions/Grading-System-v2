<?php

namespace App\Filament\App\Resources\Graduates;

use App\Filament\App\Resources\Graduates\Pages\CreateGraduate;
use App\Filament\App\Resources\Graduates\Pages\EditGraduate;
use App\Filament\App\Resources\Graduates\Pages\ListGraduates;
use App\Filament\App\Resources\Graduates\Pages\ViewGraduate;
use App\Filament\App\Resources\Graduates\Schemas\GraduateForm;
use App\Filament\App\Resources\Graduates\Schemas\GraduateInfolist;
use App\Filament\App\Resources\Graduates\Tables\GraduatesTable;
use App\Models\Graduate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GraduateResource extends Resource
{
    protected static ?string $model = Graduate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GraduateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GraduateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GraduatesTable::configure($table);
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
            'index' => ListGraduates::route('/'),
            'create' => CreateGraduate::route('/create'),
            'view' => ViewGraduate::route('/{record}'),
            'edit' => EditGraduate::route('/{record}/edit'),
        ];
    }
}
