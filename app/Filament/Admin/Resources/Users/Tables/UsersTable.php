<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
<<<<<<< Updated upstream
                TextColumn::make('honorifics')
                    ->searchable(),
=======
                TextColumn::make('honorifics'),
>>>>>>> Stashed changes
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
<<<<<<< Updated upstream
                ToggleColumn::make('active')
                    ->label('Active')
                    ->disabled(fn($record) => $record->id === 1)
                    ->sortable()
                    ->toggleable(),
=======
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('active')
                    ->icon('heroicon-s-check')
                    
                    ->sortable(),
>>>>>>> Stashed changes
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
