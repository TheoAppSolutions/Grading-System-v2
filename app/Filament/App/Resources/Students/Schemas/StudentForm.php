<?php

namespace App\Filament\App\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_id')
                    ->label('Student ID')
                    ->required()
                    ->numeric(),
                TextInput::make('student_lrn')
                    ->label('Student LRN'),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('middlename'),
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('suffix'),
                Select::make('gender')
                    ->options([
                        'm' => 'Male',
                        'f' => 'Female'
                    ])
                    ->required(),
            ]);
    }
}
