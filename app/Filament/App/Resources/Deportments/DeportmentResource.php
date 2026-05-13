<?php

namespace App\Filament\App\Resources\Deportments;

use App\Filament\App\Resources\Deportments\Pages\CreateDeportment;
use App\Filament\App\Resources\Deportments\Pages\EditDeportment;
use App\Filament\App\Resources\Deportments\Pages\ListDeportments;
use App\Filament\App\Resources\Deportments\Schemas\DeportmentForm;
use App\Filament\App\Resources\Deportments\Tables\DeportmentsTable;
use App\Models\Deportment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeportmentResource extends Resource
{
    protected static ?string $model = Deportment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeportmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeportmentsTable::configure($table);
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
            'index' => ListDeportments::route('/'),
            'create' => CreateDeportment::route('/create'),
            'edit' => EditDeportment::route('/{record}/edit'),
        ];
    }
}
