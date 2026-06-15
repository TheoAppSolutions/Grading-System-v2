<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use App\Models\Config\CardTemplate;

class SchoolYearInfoRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolYearInfo';

    protected static ?string $relationshipTitle = 'Calendar Days';

    public function table(Table $table): Table
    {
        $entries = (int) $this->ownerRecord->attendance_entries;
        $isActive = $this->ownerRecord->active;
        $coverageOptions = CardTemplate::getOptions($entries);

        return $table
            ->heading('')
            ->columns([
                TextColumn::make('coverage')
                    ->label('Coverage')
                    ->formatStateUsing(fn($state) => $coverageOptions[$state] ?? '-'),
                TextInputColumn::make('days')
                    ->type('number')
                    ->disabled(function () use ($isActive) { return $isActive != 1; })
                    ->rules(['required', 'max:255']),
            ])
            ->paginated(false);
    }
}