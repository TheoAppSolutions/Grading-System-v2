<?php

namespace App\Filament\Pages;

use App\Helpers\MyTools;
use App\Models\Config\CardTemplate;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use BackedEnum;
use Filament\Tables\Filters\SelectFilter;
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

    protected string $view = 'filament.app.pages.custom-tables-page';

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
        return $subject->subject_code . ' (' . SchoolYear::getActive()->label . ')';
    }

    public function table(Table $table): Table
    {
        $schoolYear = SchoolYear::getActive();
        $quarter = (int) data_get(
            $this->tableFilters,
            'quarter.value',
            $this->quarter_entry
        );

        $this->quarter_entry = $quarter ?: 1;
        return $table
            // ->recordKey(fn (Student $record): string => "{$record->id}-q{$this->quarter_entry}")
            ->query(
                Student::query()
                    ->where('active', 1)
                    ->whereHas('enrollments', fn($q) => $q
                        ->where('school_year_id', $schoolYear->id)
                        ->where('grade_level_id', $this->gradeLevelId))
                    ->leftJoin('grades', function ($join) use ($schoolYear) {
                        $join->on('grades.student_id', '=', 'students.id')
                            ->where('grades.subject_id', $this->subjectId)
                            ->where('grades.school_year_id', $schoolYear->id)
                            ->where('grades.quarter', $this->quarter_entry);
                    })
                    ->select(
                        'students.*',
                        'grades.midquarter as midquarter',
                        'grades.final as final',
                        'grades.effort as effort',
                        'grades.deportment as deportment',
                    )
                    ->orderBy('gender', 'desc')
                    ->orderBy('last_name', 'asc')
            )
            ->columns([
                TextColumn::make('#')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('id')
                    ->label('Student ID'),

                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record['last_name'] . ', ' . $record['first_name'] . ' (' . $record['gender'] . ')'),

                TextInputColumn::make('midquarter')
                    ->label('MidQuarter')
                    ->visible(fn() => $this->quarter_type === 'Midquarter')
                    ->getStateUsing(fn($record) => data_get($this->pendingGrades, "{$record->id}.{$this->quarter_entry}.midquarter", $record->midquarter))
                    ->updateStateUsing(function ($record, $state) {
                        $this->pendingGrades[$record->id][$this->quarter_entry]['midquarter'] = $state;
                        return $state;
                    }),

                TextInputColumn::make('final')
                    ->label('Final')
                    ->visible(fn() => $this->quarter_type === 'Quarter')
                    ->getStateUsing(fn($record) => data_get($this->pendingGrades, "{$record->id}.{$this->quarter_entry}.final", $record->final))
                    ->updateStateUsing(function ($record, $state) {
                        $this->pendingGrades[$record->id][$this->quarter_entry]['final'] = $state;
                        return $state;
                    }),

                TextInputColumn::make('effort')
                    ->label('Effort')
                    ->visible(fn() => $this->quarter_type === 'Quarter')
                    ->getStateUsing(fn($record) => data_get($this->pendingGrades, "{$record->id}.{$this->quarter_entry}.effort", $record->effort))
                    ->updateStateUsing(function ($record, $state) {
                        $this->pendingGrades[$record->id][$this->quarter_entry]['effort'] = $state;
                        return $state;
                    }),

                TextInputColumn::make('deportment')
                    ->label('Deportment')
                    ->visible(fn() => $this->quarter_type === 'Quarter')
                    ->getStateUsing(fn($record) => data_get($this->pendingGrades, "{$record->id}.{$this->quarter_entry}.deportment", $record->deportment))
                    ->updateStateUsing(function ($record, $state) {
                        $this->pendingGrades[$record->id][$this->quarter_entry]['deportment'] = $state;
                        return $state;
                    }),
            ])
            ->filters([
                SelectFilter::make('quarter')
                    ->label(fn() => CardTemplate::getOptionsLabel($schoolYear->attendance_entries))
                    ->options(fn() => CardTemplate::getOptions($schoolYear->attendance_entries))
                    ->native(false)
                    ->default(1)
                    ->selectablePlaceholder(false)
                    ->query(fn(Builder $query): Builder => $query),
                Filter::make('filters')
                    ->query(fn(Builder $query) => $query)
                    ->schema([
                        Select::make('quarter_type')
                            ->label('Type')
                            ->options([
                                'Quarter' => 'Quarter',
                                'Midquarter' => 'Midquarter',
                            ])
                            ->native(false)
                            ->visible(config('app.system_features.midquarter'))
                            ->default('Quarter')
                            ->afterStateUpdated(fn($state) => $this->quarter_type = (string) $state),
                        // Select::make('quarter_set')
                        //     ->label(function () use ($schoolYear) {
                        //         return CardTemplate::getOptionsLabel($schoolYear->attendance_entries);
                        //     })
                        //     ->options(function () use ($schoolYear) {
                        //         return CardTemplate::getOptions($schoolYear->attendance_entries);
                        //     })
                        //     ->native(false)
                        //     ->default(1)
                        //     ->afterStateUpdated(fn($state) => $this->quarter_entry = (int) $state),
                    ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Grades')
                ->action(function () {
                    dd($this->pendingGrades);
                    foreach ($this->pendingGrades as $studentId => $fields) {
                        Grade::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'subject_id' => $this->subjectId,
                                'school_year_id' => SchoolYear::getActive()->id,
                            ],
                            array_merge(
                                [
                                    'user_id' => Auth::id(),
                                    'grade_level_id' => $this->gradeLevelId,
                                    'quarter' => 1
                                ],
                                $fields[$this->quarter_entry]
                            )
                        );
                    }

                    $this->pendingGrades = [];
                    Notification::make()->title('Grades saved')->success()->send();
                }),
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
