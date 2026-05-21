<?php

namespace App\Filament\Admin\Resources\School\Sections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('schoolYear.school_year')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('section')
                    ->label('Section / Strand')
                    ->searchable(),
                TextColumn::make('gradeLevel.label'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
