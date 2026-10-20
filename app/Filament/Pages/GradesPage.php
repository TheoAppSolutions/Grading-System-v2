<?php

namespace App\Filament\Pages;

use App\Filament\Imports\GradeImporter;
use App\Helpers\MyTools;
use App\Models\Config\CardTemplate;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Support\ImportCodeWorkbook;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use League\Csv\Bom;
use League\Csv\Writer;
use Override;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class GradesPage extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Teacher Menu';

    protected static ?string $title = 'Grades';

    protected static ?string $slug = 'Grades';

    protected string $view = 'filament.app.pages.grades-table-page';

    protected static ?int $navigationSort = 1;

    public string $quarter_type = 'Quarter';

    public int $quarter_entry = 1;

    public ?int $subjectId = null;

    public ?int $gradeLevelId = null;

    public array $pendingGrades = [];

    /**
     * Keep the subject assignment check on the page itself so a user cannot
     * bypass the subject navigation by changing sid/gid in the URL.
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Administrators may review any subject, including subjects that are
        // not assigned to a teacher in the active school year.
        if ($user->isAdmin()) {
            return true;
        }

        $subjectId = request()->integer('sid');
        $gradeLevelId = request()->integer('gid');

        // Filament calls canAccess() again while Livewire hydrates the page.
        // The /livewire-*/update request does not carry the original page's
        // sid/gid query parameters, so defer subject authorization to the
        // page instance in mount() and canManageCurrentSubject().
        if (! $subjectId && ! $gradeLevelId) {
            return true;
        }

        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $subjectId || ! $gradeLevelId || ! $schoolYearId) {
            return false;
        }

        return Subject::query()
            ->whereKey($subjectId)
            ->where('grade_level_id', $gradeLevelId)
            ->whereHas('subjectUser', function ($query) use ($schoolYearId, $user): void {
                $query
                    ->where('school_year_id', $schoolYearId)
                    ->where(function ($query) use ($user): void {
                        $query
                            ->where('user_id', $user->id)
                            ->orWhere('user2_id', $user->id);
                    });
            })
            ->exists();
    }

    public function mount(): void
    {
        $this->subjectId = (int) request('sid');
        $this->gradeLevelId = (int) request('gid');
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        $this->subjectId ??= (int) request('sid');
        $subject = Subject::find($this->subjectId);
        $schoolYear = SchoolYear::getActive();

        return ($subject?->subject_code ?? 'Grades').' ('.($schoolYear?->label ?? 'School year not configured').')';
    }

    public function updatedQuarterEntry(): void
    {
        // Keep edits for the other quarters in Livewire state while the table
        // is rebuilt for the newly selected quarter.
        $this->resetTable();
    }

    public function updatedQuarterType(): void
    {
        // Midquarter and quarter edits can both be pending for the same
        // student/quarter, so changing the displayed type must not clear them.
        $this->resetTable();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        ToggleButtons::make('quarter_entry')
                                            ->label('Quarter')
                                            ->options([
                                                1 => '1st',
                                                2 => '2nd',
                                            ])
                                            ->inline()
                                            ->grouped()
                                            ->live()
                                            ->required()
                                            ->colors([
                                                1 => 'warning',
                                                2 => 'warning',
                                            ]),

                                        ToggleButtons::make('quarter_type')
                                            ->label('Type')
                                            ->options([
                                                'Quarter' => 'Quarter',
                                                'Midquarter' => 'Midquarter',
                                            ])
                                            ->inline()
                                            ->grouped()
                                            ->live()
                                            ->required()
                                            ->visible(fn (): bool => (bool) config('system-features.midquarter')),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        $schoolYear = SchoolYear::getActive();
        $schoolYearId = $schoolYear?->id;
        $quarter = $this->quarter_entry;
        $isQuarter = $this->quarter_type === 'Quarter';

        return $table
            ->query(
                Student::query()
                    ->where('active', 1)
                    ->when(! $schoolYearId, fn ($query) => $query->whereRaw('1 = 0'))
                    ->whereHas('enrollments', fn ($q) => $q
                        ->where('school_year_id', $schoolYearId)
                        ->where('grade_level_id', $this->gradeLevelId))
                    ->leftJoin('grades', function ($join) use ($schoolYearId, $quarter) {
                        $join->on('grades.student_id', '=', 'students.id')
                            ->where('grades.subject_id', $this->subjectId)
                            ->where('grades.school_year_id', $schoolYearId)
                            ->where('grades.quarter', $quarter);
                    })
                    ->select(
                        'students.*',
                        'grades.midquarter as midquarter',
                        'grades.final as final',
                        'grades.effort as effort',
                        'grades.deportment as deportment',
                    )
                    ->orderBy('students.gender', 'desc')
                    ->orderBy('students.last_name', 'asc')
            )
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('id')->label('Student ID'),
                TextColumn::make('name')
                    ->getStateUsing(fn ($record) => "{$record->last_name}, {$record->first_name} ({$record->gender})"),

                TextInputColumn::make('final')
                    ->type('number')
                    ->visible($isQuarter)
                    ->getStateUsing(fn ($record) => data_get($this->pendingGrades, "{$record->id}.{$quarter}.final", $record->final))
                    ->updateStateUsing(fn ($record, $state) => $this->pendingGrades[$record->id][$quarter]['final'] = $state),

                TextInputColumn::make('effort')
                    ->type('number')
                    ->visible($isQuarter)
                    ->getStateUsing(fn ($record) => data_get($this->pendingGrades, "{$record->id}.{$quarter}.effort", $record->effort))
                    ->updateStateUsing(fn ($record, $state) => $this->pendingGrades[$record->id][$quarter]['effort'] = $state),

                TextInputColumn::make('deportment')
                    ->type('number')
                    ->visible($isQuarter)
                    ->getStateUsing(fn ($record) => data_get($this->pendingGrades, "{$record->id}.{$quarter}.deportment", $record->deportment))
                    ->updateStateUsing(fn ($record, $state) => $this->pendingGrades[$record->id][$quarter]['deportment'] = $state),

                TextInputColumn::make('midquarter')
                    ->type('number')
                    ->visible(! $isQuarter)
                    ->getStateUsing(fn ($record) => data_get($this->pendingGrades, "{$record->id}.{$quarter}.midquarter", $record->midquarter))
                    ->updateStateUsing(fn ($record, $state) => $this->pendingGrades[$record->id][$quarter]['midquarter'] = $state),
            ])
            ->filters([
                // SelectFilter::make('quarter')
                //     ->label(fn() => CardTemplate::getOptionsLabel($schoolYear->attendance_entries))
                //     ->options([1 => '1st', 2 => '2nd'])
                //     ->native(false)
                //     ->default(1)
                //     ->selectablePlaceholder(false)
                //     ->query(function (Builder $query, array $data) use ($schoolYear): Builder {
                //         $quarter = (int) ($data['value'] ?? 1);
                //         $this->quarter_entry = $quarter;

                //         return $query
                //             ->leftJoin('grades', function ($join) use ($schoolYear, $quarter) {
                //                 $join->on('grades.student_id', '=', 'students.id')
                //                     ->where('grades.subject_id', $this->subjectId)
                //                     ->where('grades.school_year_id', $schoolYear->id)
                //                     ->where('grades.quarter', $quarter);
                //             })
                //             ->addSelect(
                //                 'students.*',
                //                 'grades.midquarter as midquarter',
                //                 'grades.final as final',
                //                 'grades.effort as effort',
                //                 'grades.deportment as deportment',
                //             );
                //     }),
            ]);
    }

    public function getGradeCsvOptions(): array
    {
        $schoolYear = SchoolYear::getActive();

        if (! $schoolYear || ! $this->subjectId || ! $this->gradeLevelId) {
            return [];
        }

        return [
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $this->gradeLevelId,
            'subject_id' => $this->subjectId,
            'quarter' => $this->quarter_entry,
        ];
    }

    public function downloadGradeImportTemplate(): StreamedResponse
    {
        $options = $this->getGradeCsvOptions();
        $csv = Writer::createFromFileObject(new SplTempFileObject);

        $csv->insertOne(['student_id', 'student_name', 'midquarter', 'final', 'effort', 'deportment', 'remarks']);
        $csv->insertAll(GradeImporter::getExampleRows($options));

        return response()->streamDownload(function () use ($csv): void {
            $csv->setOutputBOM(Bom::Utf8);

            echo $csv->toString();
        }, 'grades-quarter-'.$this->quarter_entry.'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import CSV')
                ->importer(GradeImporter::class)
                ->options(fn (): array => [
                    'user_id' => Auth::id(),
                    'subject_id' => $this->subjectId,
                    'grade_level_id' => $this->gradeLevelId,
                    'school_year_id' => SchoolYear::getActive()?->id,
                    'quarter' => $this->quarter_entry,
                ])
                ->registerModalActions([
                    Action::make('downloadGradeTemplate')
                        ->label('Download populated CSV template')
                        ->link()
                        ->action(fn (): StreamedResponse => $this->downloadGradeImportTemplate()),
                ])
                ->modalDescription(fn (ImportAction $action) => $action->getModalAction('downloadGradeTemplate'))
                ->visible(fn (): bool => config('system-features.grade_import') && SchoolYear::getActive() !== null),

            Action::make('importGrades')
                ->label('Import Grades')
                ->outlined()
                ->color('warning')
                ->schema([
                    FileUpload::make('workbook')
                        ->label('Excel workbook')
                        ->helperText('The workbook must contain an IMPORT CODE column.')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    abort_unless($this->canManageCurrentSubject(), 403);

                    try {
                        $records = ImportCodeWorkbook::read($this->uploadedFilePath($data['workbook'] ?? null));
                        $this->stageImportedGrades($records);
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title('Grades were not imported')
                            ->body(collect($exception->errors())->flatten()->implode(' '))
                            ->danger()
                            ->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Grades were not imported')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => config('system-features.grade_import') && SchoolYear::getActive() !== null),

            Action::make('save')
                ->label('Save Grades')
                ->color('success')
                ->icon('heroicon-o-document-check')
                ->action(function () {
                    abort_unless($this->canManageCurrentSubject(), 403);

                    foreach ($this->pendingGrades as $studentId => $quarters) {
                        foreach ($quarters as $quarter => $values) {
                            Grade::updateOrCreate(
                                [
                                    'student_id' => $studentId,
                                    'subject_id' => $this->subjectId,
                                    'school_year_id' => SchoolYear::getActive()?->id,
                                    'quarter' => $quarter,
                                ],
                                array_merge(
                                    [
                                        'user_id' => Auth::id(),
                                        'grade_level_id' => $this->gradeLevelId,
                                    ],
                                    $values
                                )
                            );
                        }
                    }

                    $this->pendingGrades = [];
                    Notification::make()->title('Grades saved')->success()->send();
                })
                ->visible(fn (): bool => SchoolYear::getActive() !== null && $this->subjectId !== null),
        ];
    }

    private function canManageCurrentSubject(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $this->subjectId || ! $this->gradeLevelId || ! $schoolYearId) {
            return false;
        }

        return Subject::query()
            ->whereKey($this->subjectId)
            ->where('grade_level_id', $this->gradeLevelId)
            ->whereHas('subjectUser', function ($query) use ($schoolYearId, $user): void {
                $query
                    ->where('school_year_id', $schoolYearId)
                    ->where(function ($query) use ($user): void {
                        $query
                            ->where('user_id', $user->id)
                            ->orWhere('user2_id', $user->id);
                    });
            })
            ->exists();
    }

    private function uploadedFilePath(mixed $file): string
    {
        if ($file instanceof UploadedFile && $file->getRealPath()) {
            return $file->getRealPath();
        }

        throw ValidationException::withMessages([
            'workbook' => 'Please upload a readable Excel workbook.',
        ]);
    }

    private function stageImportedGrades(array $records): void
    {
        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $schoolYearId || ! $this->subjectId || ! $this->gradeLevelId) {
            throw ValidationException::withMessages([
                'workbook' => 'The current subject, grade level, or school year is unavailable.',
            ]);
        }

        $studentIds = collect($records)->pluck('student_id')->unique()->values();
        $validStudentIds = Student::query()
            ->whereIn('id', $studentIds)
            ->where('active', 1)
            ->whereHas('enrollments', fn ($query) => $query
                ->where('school_year_id', $schoolYearId)
                ->where('grade_level_id', $this->gradeLevelId))
            ->pluck('id');

        $validStudentIdLookup = $validStudentIds
            ->mapWithKeys(fn ($studentId): array => [(string) $studentId => true]);

        $staged = [];
        $ignoredCount = 0;

        foreach ($records as $record) {
            $studentId = (string) $record['student_id'];

            if (! preg_match('/^\d+$/', $studentId)) {
                $ignoredCount++;

                continue;
            }

            // Use the exact student ID as the key. Codes for students outside
            // the current grade level are ignored and can never overwrite a row.
            if (! isset($validStudentIdLookup[$studentId])) {
                $ignoredCount++;

                continue;
            }

            // If a workbook contains the same student on more than one sheet,
            // the last exact student-ID match is the value staged for review.
            $staged[$studentId] = [
                'midquarter' => $this->validateImportedGrade($record['midquarter'], 'midquarter', 100),
                'final' => $this->validateImportedGrade($record['final'], 'final', 100),
                'effort' => $this->validateImportedGrade($record['effort'], 'effort', 5),
                'deportment' => $this->validateImportedGrade($record['deportment'], 'deportment', 100),
            ];
        }

        if ($staged === []) {
            throw ValidationException::withMessages([
                'workbook' => 'No IMPORT CODE student IDs exactly matched an active student in the selected grade level and school year.',
            ]);
        }

        foreach ($staged as $studentId => $values) {
            // Keep the import in Livewire state. The existing Save Grades action
            // is the only path that writes these values to the database.
            $this->pendingGrades[$studentId][$this->quarter_entry] = $values;
        }

        $this->resetTable();

        Notification::make()
            ->title('Grades imported for review')
            ->body(count($staged).' '.str('student')->plural(count($staged)).' staged for quarter '.$this->quarter_entry.'.'
                .($ignoredCount > 0 ? " {$ignoredCount} unmatched code(s) ignored." : ''))
            ->success()
            ->send();
    }

    private function validateImportedGrade(?string $value, string $field, int $max): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value) || (float) $value < 0 || (float) $value > $max) {
            throw ValidationException::withMessages([
                'workbook' => "Invalid {$field} grade '{$value}'. Expected a number from 0 to {$max}.",
            ]);
        }

        return (float) $value;
    }

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        $schoolYear = MyTools::getSchoolYear();

        if ($schoolYear && Auth::user()->isOffice()) {
            return true;
        }

        return false;
    }
}
