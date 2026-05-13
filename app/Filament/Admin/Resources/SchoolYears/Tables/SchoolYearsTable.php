<?php

namespace App\Filament\Admin\Resources\SchoolYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('active')
                    ->searchable(),
                TextColumn::make('school_year')
                    ->searchable(),
                TextColumn::make('school_year_start')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('school_year_end')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('days_settings')
                    ->searchable(),
                TextColumn::make('card_template')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
