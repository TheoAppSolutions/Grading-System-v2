<?php

namespace App\Filament\App\Resources\Subjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject_id')
                    ->required(),
                TextInput::make('subject_label')
                    ->required(),
                TextInput::make('subject_title')
                    ->required()
                    ->default(''),
                TextInput::make('grade_level')
                    ->required(),
            ]);
    }
}
