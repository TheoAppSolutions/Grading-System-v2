<?php

namespace App\Filament\Admin\Resources\System\Settings;

use App\Filament\Admin\Resources\System\Settings\Pages\CreateSetting;
use App\Filament\Admin\Resources\System\Settings\Pages\EditSetting;
use App\Filament\Admin\Resources\System\Settings\Pages\ListSettings;
use App\Filament\Admin\Resources\System\Settings\Schemas\SettingForm;
use App\Filament\Admin\Resources\System\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'setting';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
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
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
