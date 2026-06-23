<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\RelationManagers;

use App\Models\Homeroom;
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

    protected static ?string $relationshipTitle = 'Homeroom';

    public function table(Table $table): Table
    {
        $isActive = $this->ownerRecord->active;
        return $table
            ->heading('')
            ->query(fn() => Homeroom::where('school_year_id', $this->ownerRecord->id))
            ->columns([
                TextColumn::make('gradeLevel.label'),
                TextColumn::make('section.label')
                    ->visible(config('system-features.sections')),
                SelectColumn::make('user_id')
                    ->label('Adviser')
                    ->searchableOptions()
                    ->disabled(function () use ($isActive) {
                        return $isActive != 1;
                    })
                    ->options(fn() => User::get()->pluck('name', 'id')),
                SelectColumn::make('user2_id')
                    ->label('Co-Adviser')
                    ->searchableOptions()
                    ->disabled(function () use ($isActive) {
                        return $isActive != 1;
                    })
                    ->options(fn() => User::get()->pluck('name', 'id')),
            ]);
    }
}
