<?php

namespace App\Filament\App\Resources\Office\Homerooms\Tables;

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
        $schoolYear = MyTools::getSchoolYear() ?? '';

        if (!$schoolYear) :
            redirect('/app');
        endif;

        return $table
            ->columns([
                TextColumn::make('schoolYear.school_year'),
                TextColumn::make('gradeLevel.label'),
                TextColumn::make('section.section'),
                SelectColumn::make('user_id')
                    ->label('Adviser')
                    ->searchable()
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
                SelectColumn::make('user2_id')
                    ->label('Co-Adviser')
                    ->searchable()
                    ->options(fn() => User::where('id', '!=', 1)->pluck('name', 'id')->toArray()),
            ])
            ->filters([
                SelectFilter::make('school_year_id')
                    ->default(fn($schoolYear) => $schoolYear->school_year_id ?? $schoolYear)
                    ->options(fn() => SchoolYear::all()->pluck('school_year', 'id')->toArray()),
            ])
            ->recordUrl('');
    }
}
