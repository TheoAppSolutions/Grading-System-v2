<?php

namespace App\Filament\App\Resources\Office\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('student_lrn')
                    ->visible(config('app.system_features.student_lrn'))
                    ->searchable(),
                TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('gender')
                    ->sortable()
                    ->formatStateUsing(fn($state) => match($state) {
                        "M" => "Male",
                        "F" => "Female",
                        default => ""
                    }),
                ToggleColumn::make('active')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('last_name')
            ->defaultPaginationPageOption(25)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
