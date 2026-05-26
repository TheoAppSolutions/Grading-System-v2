<?php

namespace App\Filament\App\Resources\Office\Graduates\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GraduatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('graduated_at')
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
