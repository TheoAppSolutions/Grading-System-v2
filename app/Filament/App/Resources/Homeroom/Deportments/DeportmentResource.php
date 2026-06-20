<?php

namespace App\Filament\App\Resources\Homeroom\Deportments;

use App\Filament\App\Resources\Homeroom\Deportments\Pages\CreateDeportment;
use App\Filament\App\Resources\Homeroom\Deportments\Pages\EditDeportment;
use App\Filament\App\Resources\Homeroom\Deportments\Pages\ListDeportments;
use App\Filament\App\Resources\Homeroom\Deportments\Schemas\DeportmentForm;
use App\Filament\App\Resources\Homeroom\Deportments\Tables\DeportmentsTable;
use App\Models\Deportment;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class DeportmentResource extends Resource
{
    protected static ?string $model = Deportment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNumberedList;

    protected static string|UnitEnum|null $navigationGroup = 'Homeroom Menu';

    protected static ?int $navigationSort = 2;

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

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        return config('system-features.homeroom_deportment');
    }

    #[Override]
    public static function canView(Model $record): bool
    {
        return config('system-features.homeroom_deportment');
    }
}
