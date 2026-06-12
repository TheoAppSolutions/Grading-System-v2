<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Tables;

use App\Models\Config\AttendanceOptions;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () {
                $schoolYear = SchoolYear::getActive()->id;

                return Enrollment::with([
                    'schoolYear' => function ($query) use ($schoolYear) {
                        $query->where('school_year_id', $schoolYear);
                    },
                ])->where([
                    'school_year_id' => $schoolYear,
                ]);
            })
            ->columns([
                TextColumn::make('student_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->student->first_name.' '.$record->student->middle_name.' '.$record->student->last_name),

                TextInputColumn::make('absent')
                    ->label('Absent')
                    ->type('number')
                    ->step(0.5)
                    ->getStateUsing(
                        fn ($record, $livewire) => $livewire->pendingChanges[$record->student_id]['absent']
                            ?? data_get($record->attendance_data, $record->student_id.'.A', '')
                    )
                    ->updateStateUsing(
                        fn ($record, $state, $livewire) => $livewire->pendingChanges[$record->student_id]['absent'] = $state
                    ),

                TextInputColumn::make('tardy')
                    ->label('Tardy')
                    ->type('number')
                    ->step(0.5)
                    ->getStateUsing(
                        fn ($record, $livewire) => $livewire->pendingChanges[$record->student_id]['tardy']
                            ?? data_get($record->attendance_data, $record->student_id.'.T', '')
                    )
                    ->updateStateUsing(
                        fn ($record, $state, $livewire) => $livewire->pendingChanges[$record->student_id]['tardy'] = $state
                    ),
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->searchable()
                    ->selectablePlaceholder(false)
                    ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id')),
                SelectFilter::make('attendance_entry')
                    ->label('Entry #')
                    ->searchable()
                    ->selectablePlaceholder(false)
                    ->options(function () {
                        $schoolYear = SchoolYear::getActive();
// TODO: FIX THIS
                        return AttendanceOptions::find($schoolYear->attendance_entries)?->options ?? [];
                    }),
            ], FiltersLayout::AboveContent)
            ->defaultSort('last_name', 'ASC');
    }

    public static function getColumns(): array
    {
        $columns = [
            TextColumn::make('student_name')
                ->label('Name')
                ->getStateUsing(fn ($record) => $record->student->first_name.' '.$record->student->middle_name.' '.$record->student->last_name),
        ];

        $schoolYear = SchoolYear::getActive();

        return $columns;
    }
}
