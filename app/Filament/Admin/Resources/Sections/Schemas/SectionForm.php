<?php

namespace App\Filament\Admin\Resources\Sections\Schemas;

use App\Models\GradeLevel;
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
                Select::make('grade_level')
                    ->required()
                    ->searchable()
                    ->options(
                        function () {
                            return GradeLevel::pluck('label', 'id')->sortBy('sort_order', 1)->toArray();;
                        }
                    ),
                Select::make('school_year')
                    ->required()
                    ->searchable()
                    ->options(
                        function () {
                            return SchoolYear::pluck('school_year', 'id')->toArray();;
                        }
                    ),
                TextInput::make('section')
                    ->required()
                    ->maxLength(191)
            ]);
    }
}
