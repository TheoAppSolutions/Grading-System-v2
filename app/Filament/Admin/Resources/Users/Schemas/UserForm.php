<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('suffix'),
                TextInput::make('honorifics')
                    ->required(),
                TextInput::make('active')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
