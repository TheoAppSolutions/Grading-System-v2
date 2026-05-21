<?php

namespace App\Filament\App\Resources\Office\Homerooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HomeroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('advisory')
                    ->required()
                    ->default(''),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('user2_id')
                    ->numeric(),
                TextInput::make('section_id')
                    ->required()
                    ->default(''),
                TextInput::make('school_year'),
            ]);
    }
}
