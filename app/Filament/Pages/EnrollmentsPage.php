<?php

namespace App\Filament\Pages;

use App\Filament\App\Resources\Office\Students\StudentResource;
use App\Helpers\MyTools;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

class EnrollmentsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Office Menu';

    protected static ?string $title = 'Enrollments';

    protected static ?string $slug = 'Enrollments';

    protected string $view = 'filament.app.pages.custom-tables-page';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        $schoolYear = MyTools::getSchoolYear();

        if ($schoolYear && Auth::user()->isOffice()) {
            return true;
        }

        return false;
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isOffice();
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Enrollment (S.Y. '.(SchoolYear::getActive()?->label ?? 'Not configured').')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::where('active', 1)
                // ->whereDoesntHave('graduate')
            )
            ->selectable(true)
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->label('Student ID'),
                TextColumn::make('name')
                    ->getStateUsing(fn ($record) => $record['last_name'].', '.$record['first_name'])
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search) {
                            $query->where('last_name', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('last_name', $direction)
                            ->orderBy('first_name', $direction);
                    }),
                TextColumn::make('current_level')
                    ->label('Current Grade')
                    ->state(function ($record, $livewire) {
                        $filters = $livewire->tableFilters['enrollment'] ?? [];
                        $schoolYearId = SchoolYear::getActive()?->id;
                        $enrollmentStatus = $filters['enrollment_status'] ?? null;

                        if ($enrollmentStatus === 'enrolled' && $schoolYearId) {
                            return $record->enrollments()
                                ->where('school_year_id', $schoolYearId)
                                ->first()?->gradeLevel?->label;
                        }

                        $latest = $record->enrollments()
                            ->with(['gradeLevel', 'schoolYear'])
                            ->whereHas('schoolYear')
                            ->join('school_years', 'enrollments.school_year_id', '=', 'school_years.id')
                            ->orderByDesc('school_years.label')
                            ->first();

                        if (! $latest) {
                            return '—';
                        }

                        return $latest->gradeLevel?->label
                            .' (S.Y. '.$latest->schoolYear?->label.')';
                    })
                    ->sortable(query: function (Builder $query, string $direction, $livewire): Builder {
                        $filters = $livewire->tableFilters['enrollment'] ?? [];
                        $schoolYearId = SchoolYear::getActive()?->id;
                        $enrollmentStatus = $filters['enrollment_status'] ?? null;

                        if ($enrollmentStatus === 'enrolled' && $schoolYearId) {
                            return $query->orderBy(
                                Enrollment::query()
                                    ->select('grade_level_id')
                                    ->whereColumn('enrollments.student_id', 'students.id')
                                    ->where('enrollments.school_year_id', $schoolYearId)
                                    ->limit(1),
                                $direction
                            );
                        }

                        return $query->orderBy(
                            Enrollment::query()
                                ->select('enrollments.grade_level_id')
                                ->join('school_years', 'enrollments.school_year_id', '=', 'school_years.id')
                                ->whereColumn('enrollments.student_id', 'students.id')
                                ->orderByDesc('school_years.label')
                                ->limit(1),
                            $direction
                        );
                    }),
            ])
            ->filters([
                Filter::make('enrollment')
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('enrollment_status')
                            ->label('Enrollment Status')
                            ->required()
                            ->options([
                                'enrolled' => 'Enrolled',
                                'not_enrolled' => 'Not Enrolled',
                            ])
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if ($get('enrollment_status') === 'not_enrolled') {
                                    $set('grade_level_id', '');
                                    $set('school_year_id', SchoolYear::getActive()?->id);
                                }
                            })
                            ->default('enrolled')
                            ->live(),
                        Select::make('school_year_id')
                            ->label('School Year')
                            ->required()
                            ->searchable()
                            ->options(SchoolYear::orderBy('id', 'desc')->pluck('label', 'id'))
                            ->default(SchoolYear::where('active', 1)->first()?->id)
                            ->visible(fn (Get $get) => $get('enrollment_status') === 'enrolled')
                            ->placeholder('Select School Year'),
                        Select::make('grade_level_id')
                            ->label('Grade Level')
                            ->options(GradeLevel::orderBy('sort_order', 'asc')->pluck('label', 'id'))
                            ->searchable()
                            // ->visible(fn (Get $get) => $get('enrollment_status') === 'enrolled')
                            ->placeholder('All Grade Level'),
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
                                                fn ($q) => $q->where('grade_level_id', $data['grade_level_id'])
                                            );
                                    });
                                } else {
                                    $q->whereDoesntHave(
                                        'enrollments',
                                        fn (Builder $eq) => $eq->where('school_year_id', $data['school_year_id'])
                                    );
                                }
                            }
                        );
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];

                        if (filled($data['school_year_id'])) {
                            $year = SchoolYear::find($data['school_year_id'])?->label;
                            $status = ($data['enrollment_status'] ?? 'enrolled') === 'enrolled' ? 'Enrolled' : 'Not Enrolled';
                            $indicators[] = "School Year: {$year} ({$status})";
                        }

                        if (filled($data['grade_level_id'])) {
                            $indicators[] = 'Grade Level: '.GradeLevel::find($data['grade_level_id'])?->label;
                        }

                        return $indicators;
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('bulk_enroll')
                    ->label('Enroll Student')
                    ->schema(
                        [
                            Select::make('grade_level_id')
                                ->label('Grade Level')
                                ->searchable()
                                ->options(GradeLevel::orderBy('sort_order')->pluck('label', 'id'))
                                ->required(),
                        ]
                    )
                    ->action(fn (Collection $records, array $data) => static::enrollStudents($records, $data))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->modalHeading('Bulk Enroll Students')
                    ->modalSubmitActionLabel('Enroll')
                    ->visible(function ($livewire) {
                        $activeSchoolYearId = SchoolYear::getActive()?->id;

                        return ($livewire->tableFilters['enrollment']['enrollment_status'] ?? 'enrolled') === 'not_enrolled'
                            && $activeSchoolYearId !== null
                            && (string) ($livewire->tableFilters['enrollment']['school_year_id'] ?? '') === (string) $activeSchoolYearId;
                    }),
                BulkAction::make('bulk_deactivate')
                    ->label('Deactivate Enrollment')
                    ->action(fn (Collection $records) => static::deactivateStudents($records))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->modalHeading('Bulk Deactivate Enrollments')
                    ->modalDescription('This removes the selected students from the active school year. Historical enrollments are preserved.')
                    ->modalSubmitActionLabel('Deactivate')
                    ->visible(function ($livewire) {
                        $activeSchoolYearId = SchoolYear::getActive()?->id;

                        return ($livewire->tableFilters['enrollment']['enrollment_status'] ?? 'enrolled') === 'enrolled'
                            && $activeSchoolYearId !== null
                            && (string) ($livewire->tableFilters['enrollment']['school_year_id'] ?? '') === (string) $activeSchoolYearId;
                    }),
            ])
            ->recordActions([
                ViewAction::make('viewStudent')
                    ->label('View Student')
                    ->button()
                    ->color('success')
                    ->url(fn ($record) => StudentResource::getUrl('edit', [$record->id])),
            ])
            ->paginated(true);
    }

    public static function enrollStudents(Collection $records, array $data): void
    {
        $schoolYear = SchoolYear::getActive();

        if (! $schoolYear) {
            Notification::make()
                ->title('Configure an active school year before enrolling students.')
                ->danger()
                ->send();

            return;
        }

        $gradeLevelId = $data['grade_level_id'] ?? null;

        if (! $gradeLevelId) {
            Notification::make()
                ->title('Select a grade level before enrolling students.')
                ->danger()
                ->send();

            return;
        }

        $alreadyEnrolled = Enrollment::where('school_year_id', $schoolYear->id)
            ->whereIn('student_id', $records->pluck('id'))
            ->pluck('student_id')
            ->toArray();

        $newStudents = $records->reject(fn ($student) => in_array($student->id, $alreadyEnrolled));

        $enrollments = $newStudents->map(fn ($student) => [
            'student_id' => $student->id,
            'grade_level_id' => $gradeLevelId,
            'school_year_id' => $schoolYear->id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        if (empty($enrollments)) {
            Notification::make()
                ->title('All selected students are already enrolled.')
                ->warning()
                ->send();

            return;
        }

        $insertedCount = DB::transaction(fn () => Enrollment::insertOrIgnore($enrollments));

        if ($insertedCount === 0) {
            Notification::make()
                ->title('All selected students are already enrolled.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Students enrolled successfully.')
            ->success()
            ->send();
    }

    public static function deactivateStudents(Collection $records): void
    {
        $schoolYear = SchoolYear::getActive();

        if (! $schoolYear) {
            Notification::make()
                ->title('Configure an active school year before deactivating students.')
                ->danger()
                ->send();

            return;
        }

        $deactivatedCount = DB::transaction(fn () => Enrollment::query()
            ->where('school_year_id', $schoolYear->id)
            ->whereIn('student_id', $records->pluck('id'))
            ->delete());

        if ($deactivatedCount === 0) {
            Notification::make()
                ->title('No active enrollments were found for the selected students.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Students deactivated successfully.')
            ->success()
            ->send();
    }
}
