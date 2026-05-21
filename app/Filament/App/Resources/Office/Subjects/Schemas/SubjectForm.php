<?php

namespace App\Filament\App\Resources\Office\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Config\SubjectCategory;
use App\Models\Config\GradeLevel;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_level_id')
                    ->required()
                    ->searchable()
                    ->label('Grade Level')
                    ->options(GradeLevel::all()->sortBy('sort_order')->pluck('label', 'id')),
                Select::make('subject_category_id')
                    ->required()
                    ->searchable()
                    ->label('Subject Category')
                    ->options(SubjectCategory::all()->pluck('label', 'id')),
                TextInput::make('subject_title')
                    ->label('Subject Title')
                    ->columnSpanFull()
                    ->required()
                    ->default(''),
            ]);
    }
}
