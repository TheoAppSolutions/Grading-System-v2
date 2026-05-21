<?php

namespace App\Filament\Admin\Resources\School\Sections\Schemas;

use App\Models\Config\GradeLevel;
use App\Models\SchoolYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_level_id')
                    ->label('Grade Level')
                    ->required()
                    ->searchable()
                    ->options(
                        function () {
                            return GradeLevel::pluck('label', 'id')->toArray();;
                        }
                    ),
                Select::make('school_year_id')
                    ->label('School Year')
                    ->required()
                    ->searchable()
                    ->options(
                        function () {
                            return SchoolYear::pluck('school_year', 'id')->toArray();;
                        }
                    ),
                TextInput::make('section')
                    ->label('Section / Strand')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(191)
            ]);
    }
}
