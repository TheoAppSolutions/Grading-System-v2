<?php

namespace App\Filament\Admin\Resources\System\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('honorifics'),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                ToggleColumn::make('active')
                    ->disabled(fn ($record) => Auth::id() === $record->id),
            ])
            ->filters([
                //
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('active', 'desc')
                    ->orderBy('name', 'asc');
            })
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
