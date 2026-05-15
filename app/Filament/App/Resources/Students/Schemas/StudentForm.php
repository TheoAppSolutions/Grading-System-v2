<?php

namespace App\Filament\App\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_lrn'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('middlename'),
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('suffix')
                    ->required()
                    ->default(''),
                TextInput::make('section_id')
                    ->required()
                    ->default(''),
                TextInput::make('gender')
                    ->required(),
                TextInput::make('active')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
