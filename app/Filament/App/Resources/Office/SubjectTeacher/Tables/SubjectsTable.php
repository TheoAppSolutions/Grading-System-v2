<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher\Tables;

use App\Models\Config\GradeLevel;
use App\Models\Config\SubjectCategory;
use App\Models\Subject;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use MyTools;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        $schoolYear = MyTools::getSchoolYear();

        $teachers = User::where([
            // ['id', '!=', 1],
            'active' => 1
        ])->pluck('name', 'id');

        return $table
            ->query(Subject::with('subjectUser'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('subject_title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('subjectCategory.label')
                    ->label('Category'),
                SelectColumn::make('teacher')
                    ->label('Teacher')
                    ->searchableOptions()
                    ->options(fn() => $teachers)
                    // ->default(fn($record) => $record->subjectUser[0]->user_id)
                    ->updateStateUsing(function ($record, $state) use ($schoolYear) {
                        $record->subjectUser()->updateOrCreate(
                            ['subject_id' => $record->id],
                            [
                                'user_id' => $state,
                                'school_year_id' => $schoolYear->id
                            ]
                        );
                    }),
                TextInputColumn::make('units')
                    ->label('Units')
                    // ->default(fn($record) => $record->subjectUnit[0]->units)
                    ->updateStateUsing(function ($record, $state) use ($schoolYear) {
                        $record->subjectUnit()->updateOrCreate(
                            ['subject_id' => $record->id],
                            [
                                'units' => $state,
                                'school_year_id' => $schoolYear->id
                            ]
                        );
                    })
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->options(GradeLevel::all()->sortBy('sort_order')->pluck('label', 'id'))
                    ->searchable(),
                // SelectFilter::make('subject_category_id')
                //     ->label('Category')
                //     ->options(SubjectCategory::all()->sortBy('sort_order')->pluck('label', 'id'))
                //     ->searchable()
            ], layout: FiltersLayout::AboveContent)
            ->recordUrl(false);
    }
}
