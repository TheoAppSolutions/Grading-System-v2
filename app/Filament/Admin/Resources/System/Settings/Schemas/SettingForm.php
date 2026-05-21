<?php

namespace App\Filament\Admin\Resources\System\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('field_name')
                    ->required(),
                TextInput::make('field_value'),
            ]);
    }
}
