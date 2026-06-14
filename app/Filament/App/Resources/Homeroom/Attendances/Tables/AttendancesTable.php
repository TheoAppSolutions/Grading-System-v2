<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Tables;

use App\Models\Config\CardTemplate;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\SchoolYearInfo;
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
                
                TextColumn::make('school_day')
                    ->label('School Day'),
                    // ->getStateUsing(fn ($livewire) => SchoolYearInfo::where(['school_year_id' => $schoolYearx->id, 'entry_no' => $livewire->entryNumber])->first()->),

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
                    ->default(1)
                    ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id')),

                Filter::make('attendance_entry')
                    ->schema([
                        Select::make('attendance_entry')
                            ->label('Entry #')
                            ->searchable()
                            ->selectablePlaceholder(false)
                            ->options(function () {
                                $schoolYear = SchoolYear::getActive();

                                return CardTemplate::getOptions($schoolYear->attendance_entries) ?? [];
                            })
                            ->live()
                            ->default(1)
                            ->afterStateUpdated(fn ($state, $livewire) => $livewire->entryNumber = $state),
                    ])
                    // ->query(fn (Builder $query) => $query), // no-op, don't actually filter,
            ], FiltersLayout::AboveContent)
            ->defaultSort('last_name', 'ASC');
    }
}
