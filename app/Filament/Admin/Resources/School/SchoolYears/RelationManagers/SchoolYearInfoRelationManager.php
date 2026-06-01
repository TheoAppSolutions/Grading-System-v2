<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use App\Helpers\MyTools;

class SchoolYearInfoRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolYearInfo';

    protected static ?string $relationshipTitle = 'No. Calendar Days';

    public function table(Table $table): Table
    {
        $startMonth = (int) $this->ownerRecord->month_start;
        $endMonth = (int) $this->ownerRecord->month_end;
        $entries = (int) $this->ownerRecord->attendance_entries;

        $months = [];
        $month = $startMonth;
        while (true) {
            $months[] = $month;
            if ($month === $endMonth) break;
            $month = ($month % 12) + 1;
        }

        $totalMonths = count($months);
        $chunkSize = (int) ceil($totalMonths / $entries);

        $chunks = array_chunk($months, $chunkSize);
        $rangeMap = [];
        foreach ($chunks as $index => $chunk) {
            $label = count($chunk) > 1
                ? MyTools::getMonths()[reset($chunk)] . ' - ' . MyTools::getMonths()[end($chunk)]
                : MyTools::getMonths()[reset($chunk)];

            $rangeMap[$index + 1] = $label;
        }
        return $table
            ->heading('')
            ->columns([
                TextColumn::make('coverage')
                    ->label('Coverage')
                    ->formatStateUsing(fn($state) => $rangeMap[$state] ?? '-'),
                TextInputColumn::make('days')
                    ->rules(['required', 'max:255']),
            ])
            ->paginated(false);
    }
}
