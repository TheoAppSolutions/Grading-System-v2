<?php

namespace App\Filament\App\Resources\Office\Homerooms\Tables;

use App\Models\Homeroom;
use App\Models\SchoolYear;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use MyTools;

class HomeroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gradeLevel.label'),
                TextColumn::make('section.section'),
                SelectColumn::make('user_id')
                    ->label('Adviser')
                    ->searchableOptions()
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
                SelectColumn::make('user2_id')
                    ->label('Co-Adviser')
                    ->searchableOptions()
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
            ])
            ->paginated(false)
            ->recordUrl('');
    }
}
