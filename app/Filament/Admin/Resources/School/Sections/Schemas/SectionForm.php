<?php

namespace App\Filament\Admin\Resources\School\Sections\Schemas;

use App\Models\Config\GradeLevel;
use App\Models\SchoolYear;
use Filament\Forms\Components\Hidden;
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
                            return GradeLevel::pluck('label', 'id')->toArray();
                        }
                    ),
                Hidden::make('school_year_id')
                    ->default(fn (): ?int => SchoolYear::getActive()?->id)
                    ->required(),
                TextInput::make('label')
                    ->label('Section / Strand')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(191)
            ]);
    }
}
