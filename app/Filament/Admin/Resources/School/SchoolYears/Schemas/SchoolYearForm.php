<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use MyTools;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_year')
                    ->placeholder('e.g., 2026-2027')
                    ->required(),
                Select::make('school_year_start')
                    ->options(MyTools::getMonths())
                    ->required(),
                Select::make('school_year_end')
                    ->options(MyTools::getMonths())
                    ->required(),
                TextInput::make('attendance_entries')
                    ->integer()
                    ->required(),
            ]);
    }
}
