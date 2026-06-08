<?php

namespace App\Filament\App\Resources\Teacher\Grades\Pages;

use App\Filament\App\Resources\Teacher\Grades\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Helpers\MyTools;

class ListGrades extends ListRecords
{
    protected static string $resource = GradeResource::class;

    public array $pendingChanges = [];
    public int $quarterType = 1;
    public int $activeQuarter = 1;
    public int $subjectId = 0;
    public int $gradeLevelId = 0;
    public string $schoolYearId = '';
    public string $importCode = '';
    public Collection $students;

    public function mount(): void
    {
        parent::mount();

        $this->subjectId = (int) request()->query('sid', 0);
        $this->gradeLevelId = (int) request()->query('gid', 0);
        $this->schoolYearId = MyTools::getSchoolYear()->id;
        $this->students = Student::with('enrollmentLatest')->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importGrades')
                ->label('Import Grades')
                ->button()
                ->color('secondary')
                ->visible(config('app.system_features.grade_import'))
                ->schema([
                    Textarea::make('import_grades')
                        ->label('Paste the import code below'),
                ])
                ->action(function (array $data) {
                    $this->importGrades($data['import_grades']);
                }),

            Action::make('saveGrades')
                ->label('Save Grades')
                ->action(fn($livewire) => $livewire->saveGrades())
                ->button()
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        $quarterQuery = fn(int $quarter) => fn(Builder $query) => $query
            ->addSelect([
                'grade_id' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('id')->limit(1),
                'midquarter' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('midquarter')->limit(1),
                'remarks' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('remarks')->limit(1),
                'final' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('final')->limit(1),
                'effort' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('effort')->limit(1),
                'deportment' => Grade::whereColumn('student_id', 'students.id')
                    ->where('quarter', $quarter)->select('deportment')->limit(1),
            ]);

        return [
            '1st Quarter' => Tab::make()->modifyQueryUsing($quarterQuery(1)),
            '2nd Quarter' => Tab::make()->modifyQueryUsing($quarterQuery(2)),
            '3rd Quarter' => Tab::make()->modifyQueryUsing($quarterQuery(3)),
            '4th Quarter' => Tab::make()->modifyQueryUsing($quarterQuery(4)),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return '1st Quarter';
    }

    public function updatedActiveTab(): void
    {
        $this->activeQuarter = match ($this->activeTab) {
            '1st Quarter' => 1,
            '2nd Quarter' => 2,
            '3rd Quarter' => 3,
            '4th Quarter' => 4,
            default       => 1,
        };
    }

    public function saveGrades(): void
    {
        if (empty($this->pendingChanges)) {
            Notification::make()
                ->title('No changes to save')
                ->warning()
                ->send();
            return;
        }

        foreach ($this->pendingChanges as $studentId => $fields) {
            Grade::updateOrCreate(
                [
                    'student_id'     => $studentId,
                    'quarter'        => $this->activeQuarter,
                    'subject_id'     => $this->subjectId,
                    'grade_level_id' => $this->gradeLevelId,
                    'school_year_id' => $this->schoolYearId,
                ],
                array_merge($fields, ['user_id' => Auth::id()])
            );
        }

        $this->pendingChanges = [];

        Notification::make()
            ->title('Grades saved')
            ->success()
            ->send();
    }

    public function importGrades(string $importCode): void
    {
        $importData = explode(',', preg_replace('/\s+/', '', $importCode));

        foreach (array_chunk($importData, 5) as $chunk) {
            [$studentId, $midquarter, $final, $effort, $deportment] = $chunk;

            $this->pendingChanges[$studentId] = [
                'midquarter' => $this->normalizeGrade($midquarter),
                'final'      => $this->normalizeGrade($final, checkLow: true),
                'effort'     => $effort,
                'deportment' => $this->normalizeGrade($deportment),
            ];
        }
    }

    private function normalizeGrade(string $grade, bool $checkLow = false): string|int
    {
        if ($grade == 16.67) {
            return '';
        }

        if ($checkLow && $grade > 0 && $grade < 70) {
            return '';
        }

        if ($grade >= 100) {
            return 100;
        }

        if (strlen($grade) == 3) {
            return substr($grade, 0, -1);
        }

        return $grade;
    }
}
