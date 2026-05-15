<?php

namespace App\Filament\Admin\Resources\SchoolYears\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use MyTools;

class SchoolYearInfoRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolYearInfo';

    protected static ?string $relationshipTitle = 'No. Calendar Days';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('month')
                    ->formatStateUsing(fn($state) => MyTools::getMonths()[$state]),
                TextInputColumn::make('days')
                    ->rules(['required', 'max:255']),
            ])
            ->paginated(false);
    }
}
