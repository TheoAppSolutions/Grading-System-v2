<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Tables;

use App\Helpers\MyTools;
use App\Models\Config\CardTemplate;
use App\Models\SchoolYear;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school_year'),
                TextColumn::make('attendance_entries'),
                ToggleColumn::make('active')
                    ->disabled(fn ($record) => $record->active)
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
