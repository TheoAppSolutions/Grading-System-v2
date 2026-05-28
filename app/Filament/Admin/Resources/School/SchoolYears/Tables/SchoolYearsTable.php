<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Tables;

use App\Models\SchoolYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use MyTools;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school_year'),
                TextColumn::make('coverage')
                    ->getStateUsing(function ($record) {
                        return MyTools::getMonths()[$record['school_year_start']] . ' - ' . MyTools::getMonths()[$record['school_year_end']];
                    })
                    ->label('Months'),
                ToggleColumn::make('active')
                    ->disabled(fn($record) => $record->active)
                    ->beforeStateUpdated(function ($record, $state) {
                        if ($state) {
                            SchoolYear::query()->update(['active' => false]);
                        }
                    })
                    ->label('Active'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('id', 'DESC')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
