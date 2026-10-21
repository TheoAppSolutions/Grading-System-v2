<?php

namespace App\Filament\Admin\Resources\School\Sections\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentSectionRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollment';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.fullName')
                    ->label('Student')
                    ->searchable(),
                TextColumn::make('gradeLevel.label')
                    ->label('Grade Level'),
                TextColumn::make('schoolYear.label')
                    ->label('School Year'),
            ])
            ->headerActions([
                AssociateAction::make('add_enrollment')
                    ->label('Add Student')
                    ->slideOver(),
            ]);
    }
}
