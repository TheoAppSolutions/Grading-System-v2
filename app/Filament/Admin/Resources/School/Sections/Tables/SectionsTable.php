<?php

namespace App\Filament\Admin\Resources\School\Sections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\SchoolYear;
use Filament\Tables\Enums\FiltersLayout;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('section')
                    ->label('Section / Strand')
                    ->searchable(),
                TextColumn::make('gradeLevel.label'),
            ])
            ->filters([
                SelectFilter::make('school_year_id')
                    ->label('School Year')
                    ->default(fn() => $schoolYears->label ?? '')
                    ->options(fn() => SchoolYear::all()->pluck('label', 'id')->toArray()),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
