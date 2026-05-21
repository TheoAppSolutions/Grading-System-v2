<?php

namespace App\Filament\Admin\Resources\System\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('honorifics')
                    ->options([
                        'Mr.' => 'Mr.',
                        'Ms.' => 'Ms.',
                        'Mrs.' => 'Mrs.',
                    ])
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('suffix'),
                TextInput::make('email')
                    ->email()
                    ->required(),
            ]);
    }
}
