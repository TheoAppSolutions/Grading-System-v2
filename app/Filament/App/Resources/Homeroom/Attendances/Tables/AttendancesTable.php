<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Tables;

use App\Models\Config\CardTemplate;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        $schoolYearx = SchoolYear::getActive();

        return $table
            ->query(function () {
                $schoolYear = SchoolYear::getActive()->id;

                return Enrollment::with([
                    'schoolYear' => function ($query) use ($schoolYear) {
                        $query->where('id', $schoolYear);
                    },
                ])->where([
                    'school_year_id' => $schoolYear,
                ]);
            })
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
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
                    ->default(2)
                    ->visible(false)
                    ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id')),

                Filter::make('attendance_entry')
                    ->schema([
                        Select::make('attendance_entry')
                            ->label('Entry #')
                            ->searchable()
                            ->options(function () {
                                $schoolYear = SchoolYear::getActive();

                                return CardTemplate::getOptions($schoolYear->attendance_entries) ?? [];
                            })
                            ->live()
                            ->default(1)
                            ->afterStateUpdated(fn ($state, $livewire) => $livewire->entryNumber = $state),
                    ]),
                // ->query(fn (Builder $query) => $query), // no-op, don't actually filter,
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->join('students', 'enrollments.student_id', '=', 'students.id')
                    ->orderBy('students.gender', 'desc')
                    ->orderBy('students.last_name', 'asc')
                    ->select('enrollments.*');
            });
    }
}
