<?php

namespace App\Filament\App\Resources\Teacher\Grades\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('subject_id')
                    ->relationship('subject', 'id')
                    ->required(),
                TextInput::make('grade_level')
                    ->required()
                    ->numeric(),
                TextInput::make('quarter')
                    ->required()
                    ->numeric(),
                TextInput::make('school_year')
                    ->required()
                    ->default('0'),
                TextInput::make('midquarter')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('final')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('effort')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('deportment')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('remarks')
                    ->required()
                    ->default('PASSED'),
            ]);
    }
}
