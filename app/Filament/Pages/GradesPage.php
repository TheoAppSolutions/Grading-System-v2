<?php

namespace App\Filament\Pages;

use App\Helpers\MyTools;
use App\Filament\Imports\GradeImporter;
use App\Models\Config\CardTemplate;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Imports\ImportAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Override;
use UnitEnum;

class GradesPage extends Page implements HasTable
{
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
        $this->pendingGrades = [];
        $this->resetTable();
    }

    public function updatedQuarterType(): void
    {
        $this->pendingGrades = [];
        $this->resetTable();
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

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(GradeImporter::class)
                ->options(fn (): array => [
                    'user_id' => Auth::id(),
                    'subject_id' => $this->subjectId,
                    'grade_level_id' => $this->gradeLevelId,
                    'school_year_id' => SchoolYear::getActive()?->id,
                    'quarter' => $this->quarter_entry,
                ])
                ->visible(fn (): bool => config('system-features.grade_import') && SchoolYear::getActive() !== null),

            Action::make('save')
                ->label('Save Grades')
                ->color('success')
                ->icon('heroicon-o-document-check')
                ->action(function () {
                    $quarter = $this->quarter_entry;

                    foreach ($this->pendingGrades as $studentId => $quarters) {
                        if (! array_key_exists($quarter, $quarters)) {
                            continue; // nothing pending for the active quarter
                        }

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
                                $quarters[$quarter]
                            )
                        );

                        unset($this->pendingGrades[$studentId][$quarter]);

                        if (empty($this->pendingGrades[$studentId])) {
                            unset($this->pendingGrades[$studentId]);
                        }
                    }

                    Notification::make()->title('Grades saved')->success()->send();
                })
                ->visible(fn (): bool => SchoolYear::getActive() !== null && $this->subjectId !== null),
        ];
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
