<?php

namespace App\Filament\App\Resources\Office\Enrollments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id')
                    ->relationship('student', 'name')
                    ->required(),
                TextInput::make('school_year')
                    ->required()
                    ->default('2018'),
                TextInput::make('grade_level'),
                TextInput::make('section_id'),
            ]);
    }
}
