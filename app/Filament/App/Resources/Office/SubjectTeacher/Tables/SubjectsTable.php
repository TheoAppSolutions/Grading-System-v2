<?php

namespace App\Filament\App\Resources\Office\SubjectTeacher\Tables;

use App\Models\Config\GradeLevel;
use App\Models\Subject;
use App\Models\User;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Builder;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        $teachers = User::getActiveUsers()->pluck('name', 'id');

        return $table
            ->query(
                Subject::with(['subjectUser' => function ($query) {
                    $query->where('school_year_id', SchoolYear::getActive()->id);
                }])
            )
            ->columns([
                TextColumn::make('subject_title')
                    ->label('Title')
                    ->searchable(),

                SelectColumn::make('teacher')
                    ->label('Teacher')
                    ->searchableOptions()
                    ->options(fn() => $teachers)
                    ->default(function ($record, $livewire) {
                        if (isset($livewire->pendingChanges[$record->id]['teacher'])):
                            return $livewire->pendingChanges[$record->id]['teacher'];
                        elseif (isset($record->subjectUser->first()->user_id)):
                            return $record->subjectUser->first()->user_id;
                        endif;
                        return "";
                    })
                    ->updateStateUsing(function ($record, $state, $livewire) {
                        $state = $state ?? '';
                        $livewire->pendingChanges[$record->id]['teacher'] = $state;
                    }),

                TextInputColumn::make('units')
                    ->label('Units')
                    ->default(fn($record, $livewire) => $livewire->pendingChanges[$record->id]['units'] ?? "")
                    ->default(function ($record, $livewire) {
                        if (isset($livewire->pendingChanges[$record->id]['units'])):
                            return $livewire->pendingChanges[$record->id]['units'];
                        elseif (isset($record->subjectUnit->first()->units)):
                            return $record->subjectUnit->first()->units;
                        endif;
                        return "";
                    })
                    ->updateStateUsing(function ($record, $state, $livewire) {
                        $livewire->pendingChanges[$record->id]['units'] = $state;
                    }),
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->options(GradeLevel::all()->sortBy('sort_order')->pluck('label', 'id'))
                    ->selectablePlaceholder(false)
                    ->default(1)
                    ->searchable(),
                // SelectFilter::make('subject_category_id')
                //     ->label('Category')
                //     ->options(SubjectCategory::all()->sortBy('sort_order')->pluck('label', 'id'))
                //     ->searchable()
            ], layout: FiltersLayout::AboveContent)
            ->paginated(false);
    }
}
