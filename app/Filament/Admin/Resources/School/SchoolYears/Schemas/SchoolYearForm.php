<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Schemas;

use App\Helpers\MyTools;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_year')
                    ->placeholder('e.g., 2026-2027')
                    ->required(),
                Select::make('attendance_entries')
                    ->label('Attendance Entries')
                    ->live()
                    ->options([
                        2 => 'By Semester (2 entries)',
                        4 => 'By Quarter (4 entries)',
                        10 => 'By Month (10 entries)',
                    ])
                    ->required(),
                Select::make('month_start')
                    ->label('Start Month')
                    ->searchable()
                    ->visible(fn(Get $get) => $get('attendance_entries') == 10)
                    ->options(MyTools::getMonths())
                    ->required(),
                Select::make('month_end')
                    ->label('End Month')
                    ->searchable()
                    ->visible(fn(Get $get) => $get('attendance_entries') == 10)
                    ->options(MyTools::getMonths())
                    ->required(),
            ]);
    }
}
