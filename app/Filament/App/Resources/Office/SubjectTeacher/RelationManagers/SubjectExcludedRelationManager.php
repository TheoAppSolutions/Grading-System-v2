<?php

namespace App\Filament\Admin\Resources\School\Subjects\RelationManagers;

use App\Filament\Admin\Resources\School\Subjects\SubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\SubjectExcluded;
use Filament\Actions\AssociateAction;
use MyTools;

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
                TextColumn::make('student.full_name')
                    ->label('Name')
            ])
            ->headerActions([
                AssociateAction::make('add_student')
                    ->label('Add Student')
                    ->slideOver()
            ]);
    }
}
