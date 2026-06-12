<?php

namespace App\Filament\App\Resources\Office\Enrollments\Tables;

use App\Models\Student;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Config\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Enrollment;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Helpers\MyTools;
use Filament\Infolists\Components\TextEntry;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Student::where('active', 1))
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->label('Student ID'),
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record['last_name'] . ', ' . $record['first_name'])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('current_level')
                    ->label('Current Grade')
                    ->state(function ($record, $livewire) {
                        $filters = $livewire->tableFilters['enrollment'] ?? [];
                        $schoolYearId = $filters['school_year_id'] ?? null;
                        $enrollmentStatus = $filters['enrollment_status'] ?? null;

                        if ($enrollmentStatus === 'enrolled' && $schoolYearId) {
                            return $record->enrollments()
                                ->where('school_year_id', $schoolYearId)
                                ->first()?->gradeLevel?->label;
                        }

                        // Get latest enrollment by school year relationship, not created_at
                        $latest = $record->enrollments()
                            ->with(['gradeLevel', 'schoolYear'])
                            ->whereHas('schoolYear')
                            ->join('school_years', 'enrollments.school_year_id', '=', 'school_years.id')
                            ->orderByDesc('school_years.school_year')
                            ->first();

                        if (! $latest) {
                            return '—';
                        }

                        return $latest->gradeLevel?->label
                            . ' (S.Y. ' . $latest->schoolYear?->school_year . ')';
                    })
            ])
            ->filters([
                Filter::make('enrollment')
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('school_year_id')
                            ->label('School Year')
                            ->required()
                            ->searchable()
                            ->options(SchoolYear::orderBy('id', 'desc')->pluck('school_year', 'id'))
                            ->default(SchoolYear::where('active', 1)->first()?->id)
                            ->selectablePlaceholder(false)
                            ->placeholder('Select School Year'),
                        Select::make('enrollment_status')
                            ->label('Enrollment Status')
                            ->required()
                            ->options([
                                'enrolled'     => 'Enrolled',
                                'not_enrolled' => 'Not Enrolled',
                            ])
                            ->afterStateUpdated(function (GET $get, Set $set) {
                                if ($get('enrollment_status') === 'not_enrolled') {
                                    $set('grade_level_id', '');
                                }
                            })
                            ->default('enrolled')
                            ->selectablePlaceholder(false)
                            ->live(),
                        Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->options(GradeLevel::orderBy('sort_order', 'asc')->pluck('label', 'id'))
                            ->searchable()
                            ->visible(fn(Get $get) => $get('enrollment_status') === 'enrolled')
                            ->placeholder('All Grade Level')
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->when(
                            filled($data['school_year_id']),
                            function (Builder $q) use ($data) {
                                $enrolled = ($data['enrollment_status'] ?? 'enrolled') === 'enrolled';

                                if ($enrolled) {
                                    $q->whereHas('enrollments', function (Builder $eq) use ($data) {
                                        $eq->where('school_year_id', $data['school_year_id'])
                                            ->when(
                                                filled($data['grade_level_id']),
                                                fn($q) => $q->where('grade_level_id', $data['grade_level_id'])
                                            );
                                    });
                                } else {
                                    $q->whereDoesntHave(
                                        'enrollments',
                                        fn(Builder $eq) =>
                                        $eq->where('school_year_id', $data['school_year_id'])
                                    );
                                }
                            }
                        );
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];

                        if (filled($data['school_year_id'])) {
                            $year = SchoolYear::find($data['school_year_id'])?->school_year;
                            $status = ($data['enrollment_status'] ?? 'enrolled') === 'enrolled' ? 'Enrolled' : 'Not Enrolled';
                            $indicators[] = "School Year: {$year} ({$status})";
                        }

                        if (filled($data['grade_level_id'])) {
                            $indicators[] = 'Grade Level: ' . GradeLevel::find($data['grade_level_id'])?->label;
                        }

                        return $indicators;
                    }),
            ], FiltersLayout::AboveContent)
            ->toolbarActions([
                BulkAction::make('bulk_enroll')
                    ->label('Enroll Student')
                    ->schema(
                        function (Collection $records) {
                            $gradeLevelOptions = $records
                                ->flatMap(fn($student) => $student->enrollments)
                                ->map(fn($enrollment) => $enrollment->gradeLevel)
                                ->filter()
                                ->unique('id')
                                ->pluck('id')
                                ->toArray();

                            if (count($gradeLevelOptions) > 1) :
                                return [
                                    TextEntry::make('Multiple Gradelevel Selected, do you want to enroll all student with 1 level increase?')
                                ];
                            endif;

                            return [
                                Select::make('grade_level_id')
                                    ->label('Grade Level')
                                    ->searchable()
                                    ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id'))
                                    ->required(),
                            ];
                        }
                    )
                    ->action(fn(Collection $records, array $data) => static::enrollStudents($records, $data))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Bulk Enroll Students')
                    ->modalSubmitActionLabel('Enroll')
                    ->visible(function ($livewire) {
                        if ($livewire->tableFilters['enrollment']['school_year_id'] == SchoolYear::getActive()->id) {
                            return true;
                        }
                        return false;
                    })
            ])
            ->deferFilters(false)
            ->hiddenFilterIndicators(true)
            ->recordUrl('');
    }

    public static function enrollStudents(Collection $records, array $data): void
    {
        $alreadyEnrolled = Enrollment::where('school_year_id', SchoolYear::getActive())
            ->whereIn('student_id', $records->pluck('id'))
            ->pluck('student_id')
            ->toArray();

        $newStudents = $records->reject(fn($student) => in_array($student->id, $alreadyEnrolled));

        $enrollments = $newStudents->map(fn($student) => [
            'student_id'     => $student->id,
            'grade_level_id' => $data['grade_level_id'],
            'school_year_id' => $data['school_year_id'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ])->toArray();

        if (empty($enrollments)) {
            Notification::make()
                ->title('All selected students are already enrolled.')
                ->warning()
                ->send();
            return;
        }

        Enrollment::insert($enrollments);

        Student::whereIn('id', $newStudents->pluck('id'))
            ->update(['current_level' => $data['grade_level_id']]);

        Notification::make()
            ->title('Students enrolled successfully.')
            ->success()
            ->send();
    }
}
