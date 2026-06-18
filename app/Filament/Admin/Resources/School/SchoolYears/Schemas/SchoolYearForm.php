<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Schemas;

use App\Helpers\MyTools;
use App\Models\Config\CardTemplate;
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
                TextInput::make('label')
                    ->label('School Year')
                    ->required(),
                Select::make('attendance_entries')
                    ->label('Attendance Entries')
                    ->live()
                    ->options(CardTemplate::getCategoriesOptions())
                    ->native(false)
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
