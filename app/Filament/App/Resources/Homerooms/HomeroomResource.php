<?php

namespace App\Filament\App\Resources\Homerooms;

use App\Filament\App\Resources\Homerooms\Pages\CreateHomeroom;
use App\Filament\App\Resources\Homerooms\Pages\EditHomeroom;
use App\Filament\App\Resources\Homerooms\Pages\ListHomerooms;
use App\Filament\App\Resources\Homerooms\Schemas\HomeroomForm;
use App\Filament\App\Resources\Homerooms\Tables\HomeroomsTable;
use App\Models\Homeroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeroomResource extends Resource
{
    protected static ?string $model = Homeroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HomeroomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeroomsTable::configure($table);
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
            'index' => ListHomerooms::route('/'),
            'create' => CreateHomeroom::route('/create'),
            'edit' => EditHomeroom::route('/{record}/edit'),
        ];
    }
}
