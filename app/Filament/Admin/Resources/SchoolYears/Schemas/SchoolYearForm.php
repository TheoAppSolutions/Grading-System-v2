<?php

namespace App\Filament\Admin\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->required()
                    ->live(),
                Select::make('school_year_end')
                    ->options(MyTools::getMonths())
                    ->required()
                    ->live(),
                TextInput::make('attendance_entries')
                    ->required()
                    ->live(),
                Section::make('Attendance Settings')
                    ->visible(fn(Get $get) => (int) $get('attendance_entries') > 0 && (int) $get('school_year_start') > 0 && (int) $get('school_year_end') > 0)
                    ->schema(
                        function (Get $get) {
                            $start   = (int) $get('school_year_start');
                            $end     = (int) $get('school_year_end');
                            $entries = max(1, (int) $get('attendance_entries'));

                            if (!$start || !$end) {
                                $months = range(1, 12);
                            } elseif ($start <= $end) {
                                $months = range($start, $end);
                            } else {
                                $months = array_merge(range($start, 12), range(1, $end));
                            }

                            // Split months across exactly $entries buckets
                            $totalMonths  = count($months);
                            $chunks       = array_chunk($months, (int) ceil($totalMonths / $entries));

                            $fields = [];
                            foreach ($chunks as $index => $chunk) {
                                $firstMonth = date('F', mktime(0, 0, 0, $chunk[0], 1));
                                $lastMonth  = date('F', mktime(0, 0, 0, end($chunk), 1));

                                $monthRange = $firstMonth === $lastMonth
                                    ? $firstMonth
                                    : "$firstMonth – $lastMonth";

                                $fields[] = TextInput::make("attendance_entry.$index")
                                    ->label("No. of Days: " . $monthRange)
                                    ->numeric();
                            }

                            return $fields;
                        }
                    ),
            ]);
    }
}
