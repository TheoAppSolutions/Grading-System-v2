<?php

namespace App\Filament\App\Resources\Office\Homerooms;

use App\Filament\App\Resources\Office\Homerooms\Pages\CreateHomeroom;
use App\Filament\App\Resources\Office\Homerooms\Pages\EditHomeroom;
use App\Filament\App\Resources\Office\Homerooms\Pages\ListHomerooms;
use App\Filament\App\Resources\Office\Homerooms\Schemas\HomeroomForm;
use App\Filament\App\Resources\Office\Homerooms\Tables\HomeroomsTable;
use App\Models\Homeroom;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use MyTools;
use Override;

class HomeroomResource extends Resource
{
    protected static ?string $model = Homeroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        $schoolYear = MyTools::getSchoolYear();

        if ($schoolYear): 
            return true;
        endif;

        return false;
    }

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
