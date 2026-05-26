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
use MyTools;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Student::where('active', 1))
            ->columns([
                TextColumn::make('student_id')
                    ->label('ID'),
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record['last_name'] . ', ' . $record['first_name'])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('current_level')
                    ->formatStateUsing(fn($state) => $state ? MyTools::ordinal($state) . ' Grade' : 'No Records'),
            ])
            ->filters([
                Filter::make('enrollment')
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('school_year_id')
                            ->label('School Year')
                            ->required()
                            ->options(SchoolYear::pluck('school_year', 'id'))
                            ->default(SchoolYear::where('active', 1)->first()?->id)
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
                            ->live(),
                        Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->options(GradeLevel::pluck('label', 'id'))
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
                    ->label('Bulk Enroll')
                    ->schema([
                        Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->options(GradeLevel::pluck('label', 'id'))
                            ->required(),
                        Select::make('school_year_id')
                            ->label('School Year')
                            ->options(SchoolYear::pluck('school_year', 'id'))
                            ->default(SchoolYear::where('active', 1)->first()?->id)
                            ->required(),
                    ])
                    ->action(fn(Collection $records, array $data) => static::enrollStudents($records, $data))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Bulk Enroll Students')
                    ->modalSubmitActionLabel('Enroll')
                    ->visible(function () {return true;})
            ])
            ->recordUrl('');
    }

    public static function enrollStudents(Collection $records, array $data): void
    {
        $alreadyEnrolled = Enrollment::where('school_year_id', $data['school_year_id'])
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
