<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use App\Filament\App\Resources\Office\Homerooms\HomeroomResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;
use App\Models\SchoolYear;

class HomeroomRelationManager extends RelationManager
{
    protected static string $relationship = 'homeroom';

    protected static ?string $relatedResource = HomeroomResource::class;

    protected static ?string $relationshipTitle = 'Homeroom';

    public function table(Table $table): Table
    {
        $schoolYears = $this->ownerRecord->active;
        return $table
            ->heading('')
            ->columns([
                TextColumn::make('gradeLevel.label'),
                TextColumn::make('section.section')
                    ->visible(config('app.system_features.sections')),
                SelectColumn::make('user_id')
                    ->label('Adviser')
                    ->searchableOptions()
                    ->disabled(function () use ($schoolYears) { return $schoolYears != 1; })
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
                SelectColumn::make('user2_id')
                    ->label('Co-Adviser')
                    ->searchableOptions()
                    ->disabled(function () use ($schoolYears) { return $schoolYears != 1; })
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
            ])
            ->paginated(false)
            ->recordUrl('');
    }
}
