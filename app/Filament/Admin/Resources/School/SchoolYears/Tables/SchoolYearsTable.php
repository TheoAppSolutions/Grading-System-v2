<?php

namespace App\Filament\Admin\Resources\School\SchoolYears\Tables;

use App\Models\Config\CardTemplate;
use App\Models\SchoolYear;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('attendance_entries')
                    ->formatStateUsing(fn ($state) => CardTemplate::getCategoriesOptions()[$state]),
                ToggleColumn::make('active')
                    ->disabled(fn ($record) => $record->active)
                    ->beforeStateUpdated(function ($state) {
                        if ($state) {
                            SchoolYear::query()->update(['active' => false]);
                        }
                    })
                    ->label('Active'),
            ])
            ->defaultSort('id', 'DESC')
            ->recordActions([
                EditAction::make()
                    ->label('View')
                    ->button()
                    ->icon(Heroicon::Eye),
            ]);
    }
}
