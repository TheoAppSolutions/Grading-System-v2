<?php

namespace App\Filament\App\Resources\Office\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label('Student ID')
                    ->required()
                    ->numeric(),
                TextInput::make('student_lrn')
                    ->label('Student LRN')
                    ->visible(config('system-features.student_lrn')),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('suffix'),
                Select::make('gender')
                    ->options([
                        'M' => 'Male',
                        'F' => 'Female'
                    ])
                    ->required(),
            ]);
    }
}
