<?php

namespace App\Filament\Admin\Resources\System\Configurations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConfigurationForm
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
