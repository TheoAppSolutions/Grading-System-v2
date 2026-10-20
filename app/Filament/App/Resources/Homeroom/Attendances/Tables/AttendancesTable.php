<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Tables;

use App\Models\Config\CardTemplate;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () {
                $schoolYear = SchoolYear::getActive()?->id;

                if (! $schoolYear) {
                    return Enrollment::query()->whereRaw('1 = 0');
                }

                if (Auth::user()?->isAdmin()) {
                    return Enrollment::query()
                        ->with([
                            'student.attendance' => fn ($query) => $query->where('school_year_id', $schoolYear),
                        ])
                        ->where('school_year_id', $schoolYear);
                }

                $homeroom = Homeroom::query()
                    ->where('school_year_id', $schoolYear)
                    ->where(function (Builder $query): void {
                        $query->where('user_id', Auth::id())
                            ->orWhere('user2_id', Auth::id());
                    })
                    ->first();

                if (! $homeroom) {
                    return Enrollment::query()->whereRaw('1 = 0');
                }

                return Enrollment::query()
                    ->with([
                        'student.attendance' => fn ($query) => $query->where('school_year_id', $schoolYear),
                    ])
                    ->where([
                        'school_year_id' => $schoolYear,
                        'grade_level_id' => $homeroom->grade_level_id,
                    ])
                    ->when($homeroom->section_id, fn (Builder $query, $sectionId) => $query->where('section_id', $sectionId));
            })
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('student_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => trim(implode(' ', array_filter([
                        $record->student?->first_name,
                        $record->student?->middle_name,
                        $record->student?->last_name,
                        $record->student?->suffix,
                    ])))),

                TextInputColumn::make('school_days')
                    ->label('School Days')
                    ->type('number')
                    ->extraInputAttributes([
                        'readonly' => true,
                    ])
                    ->getStateUsing(
                        fn ($livewire) => $livewire->getSchoolDays()
                    ),

                TextInputColumn::make('absent')
                    ->label('Absent')
                    ->type('number')
                    ->step(0.5)
                    ->getStateUsing(
                        fn ($record, $livewire) => $livewire->getPendingValue(
                            $record->student_id,
                            'absent',
                            data_get($record->student?->attendance?->first()?->attendance, $livewire->entryNumber.'.A', 0),
                        )
                    )
                    ->updateStateUsing(
                        function ($record, $state, $livewire) {
                            $livewire->setPendingValue($record->student_id, 'absent', $state);
                        }
                    ),

                TextInputColumn::make('tardy')
                    ->label('Tardy')
                    ->type('number')
                    ->step(0.5)
                    ->getStateUsing(
                        fn ($record, $livewire) => $livewire->getPendingValue(
                            $record->student_id,
                            'tardy',
                            data_get($record->student?->attendance?->first()?->attendance, $livewire->entryNumber.'.T', 0),
                        )
                    )
                    ->updateStateUsing(
                        fn ($record, $state, $livewire) => $livewire->setPendingValue($record->student_id, 'tardy', $state)
                    ),

                // TextInputColumn::make('present')
                //     ->label('Present')
                //     ->type('number')
                //     ->extraInputAttributes([
                //         'readonly' => true,
                //     ])
                //     ->getStateUsing(
                //         function ($record, $livewire): float {
                //             $absent = (float) $livewire->getPendingValue(
                //                 $record->student_id,
                //                 'absent',
                //                 data_get($record->student?->attendance?->first()?->attendance, $livewire->entryNumber.'.A', 0),
                //             );

                //             return max(0, $livewire->getSchoolDays() - $absent);
                //         }
                //     ),
            ])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Grade Level')
                    ->searchable()
                    ->native(false)
                    ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false)
                    ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id')),

                Filter::make('attendance_entry')
                    ->schema([
                        Select::make('attendance_entry')
                            ->label(function (): string {
                                $attendanceEntries = SchoolYear::getActive()?->attendance_entries ?? 0;

                                return CardTemplate::getOptionsLabel($attendanceEntries);
                            })
                            ->searchable()
                            ->options(function () {
                                $schoolYear = SchoolYear::getActive();

                                return $schoolYear
                                    ? (CardTemplate::getOptions($schoolYear->attendance_entries) ?? [])
                                    : [];
                            })
                            ->live()
                            ->default(function (): int {
                                $attendanceEntries = SchoolYear::getActive()?->attendance_entries ?? 0;

                                return $attendanceEntries >= 10 ? now()->month : 1;
                            })
                            ->afterStateHydrated(function ($state, $livewire): void {
                                $livewire->entryNumber = (int) ($state ?: 1);
                            })
                            ->afterStateUpdated(function ($state, $livewire): void {
                                $livewire->entryNumber = (int) $state;
                            }),
                    ]),
                // ->query(fn (Builder $query) => $query), // no-op, don't actually filter,
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->join('students', 'enrollments.student_id', '=', 'students.id')
                    ->orderBy('students.gender', 'desc')
                    ->orderBy('students.last_name', 'asc')
                    ->orderBy('students.first_name', 'asc')
                    ->orderBy('students.id', 'asc')
                    ->select('enrollments.*');
            })
            ->emptyStateHeading('No students found for this homeroom.');
    }
}
