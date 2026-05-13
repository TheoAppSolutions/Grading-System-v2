<?php

namespace App\Filament\App\Resources\GradeCriterias;

use App\Filament\App\Resources\GradeCriterias\Pages\CreateGradeCriteria;
use App\Filament\App\Resources\GradeCriterias\Pages\EditGradeCriteria;
use App\Filament\App\Resources\GradeCriterias\Pages\ListGradeCriterias;
use App\Filament\App\Resources\GradeCriterias\Pages\ViewGradeCriteria;
use App\Filament\App\Resources\GradeCriterias\Schemas\GradeCriteriaForm;
use App\Filament\App\Resources\GradeCriterias\Schemas\GradeCriteriaInfolist;
use App\Filament\App\Resources\GradeCriterias\Tables\GradeCriteriasTable;
use App\Models\GradeCriteria;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeCriteriaResource extends Resource
{
    protected static ?string $model = GradeCriteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GradeCriteriaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GradeCriteriaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradeCriteriasTable::configure($table);
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
            'index' => ListGradeCriterias::route('/'),
            'create' => CreateGradeCriteria::route('/create'),
            'view' => ViewGradeCriteria::route('/{record}'),
            'edit' => EditGradeCriteria::route('/{record}/edit'),
        ];
    }
}
