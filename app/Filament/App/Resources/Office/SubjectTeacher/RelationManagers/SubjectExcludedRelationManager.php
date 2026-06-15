<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\SubjectExcluded;
use Filament\Actions\AssociateAction;
use App\Helpers\MyTools;

class SubjectExcludedRelationManager extends RelationManager
{
    protected static string $relationship = 'subjectExcluded';

    public function table(Table $table): Table
    {
        $schoolYear = MyTools::getSchoolYear();
        $subject_id = $this->ownerRecord->id;

        return $table
            ->query(
                SubjectExcluded::where([
                    'school_year_id' => $schoolYear->id,
                    'subject_id' => $subject_id
                ])
            )
            ->columns([
                TextColumn::make('student.fullName')
                    ->label('Name')
            ])
            ->headerActions([
                AssociateAction::make('add_student')
                    ->label('Add Student')
                    ->slideOver()
            ]);
    }
}
