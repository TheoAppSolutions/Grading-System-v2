<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Tables;

use App\Models\Attendance;
use App\Models\Config\AttendanceOptions;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
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
                    }
                ])->where([
                    'school_year_id' => $schoolYear
                ]);
            })
            ->columns(self::getColumns())
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
                        // return AttendanceOptions->where('id', $schoolYear->attendance_entries)->options;
                    })
            ], FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->defaultSort('last_name', 'ASC')
            ->hiddenFilterIndicators()
            ->recordUrl('');
    }

    public static function getColumns(): array
    {
        $columns = [
            TextColumn::make('student_name')
                ->label('Name')
                ->getStateUsing(fn($record) => $record->student->first_name . ' ' . $record->student->middle_name . ' ' . $record->student->last_name),
        ];

        $schoolYear = SchoolYear::getActive();
        for ($i = 1; $i <= $schoolYear->attendance_entries; $i++):
            $columns[] = TextInputColumn::make('entry_no' . $i)->label('Entry: ' . $i);
        endfor;

        return $columns;
    }
}
