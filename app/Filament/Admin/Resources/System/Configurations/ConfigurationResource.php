<?php

namespace App\Filament\Admin\Resources\System\Configurations;

use App\Filament\Admin\Resources\System\Configurations\Pages\CreateConfiguration;
use App\Filament\Admin\Resources\System\Configurations\Pages\EditConfiguration;
use App\Filament\Admin\Resources\System\Configurations\Pages\ListConfigurations;
use App\Filament\Admin\Resources\System\Configurations\Schemas\ConfigurationForm;
use App\Filament\Admin\Resources\System\Configurations\Tables\ConfigurationsTable;
use App\Models\Configuration;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConfigurationResource extends Resource
{
    protected static ?string $model = Configuration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'configurations';

    public static function form(Schema $schema): Schema
    {
        return ConfigurationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConfigurationsTable::configure($table);
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
            'index' => ListConfigurations::route('/'),
            'create' => CreateConfiguration::route('/create'),
            'edit' => EditConfiguration::route('/{record}/edit'),
        ];
    }
}
