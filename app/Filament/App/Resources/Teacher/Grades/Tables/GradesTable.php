<?php

namespace App\Filament\App\Resources\Office\Grades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subject.id')
                    ->searchable(),
                TextColumn::make('grade_level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quarter')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('school_year')
                    ->searchable(),
                TextColumn::make('midquarter')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('effort')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deportment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remarks')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
