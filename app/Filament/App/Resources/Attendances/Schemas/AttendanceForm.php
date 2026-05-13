<?php

namespace App\Filament\App\Resources\Attendances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                TextInput::make('schoolyear')
                    ->required(),
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('days_present')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('days_tardiness')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
