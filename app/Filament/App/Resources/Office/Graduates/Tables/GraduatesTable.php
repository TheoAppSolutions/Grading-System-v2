<?php

namespace App\Filament\App\Resources\Office\Graduates\Tables;

use App\Filament\App\Resources\Office\Students\StudentResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GraduatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student')
                    ->getStateUsing(fn($record) => $record->student->fullName)
                    ->searchable(),
                TextColumn::make('schoolYear.school_year')
                    ->label('Graduate School Year')
            ])
            ->defaultSort('school_year', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => StudentResource::getUrl('view', [$record->student_id])),
            ]);
    }
}
