<?php

namespace App\Filament\App\Resources\Homeroom\Attendances\Pages;

use App\Filament\App\Resources\Homeroom\Attendances\AttendanceResource;
use App\Filament\Imports\AttendanceImporter;
use App\Models\Attendance;
use App\Models\Config\CardTemplate;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use App\Models\SchoolYearInfo;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use League\Csv\Bom;
use League\Csv\Writer;
use Override;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    public array $pendingChanges = [];

    public array $schoolDaysByEntry = [];

    public int $entryNumber = 1;

    public function getSchoolDays(?int $entry = null): float
    {
        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $schoolYearId) {
            return 0;
        }

        $coverage = $entry ?? $this->entryNumber;

        if (array_key_exists($coverage, $this->schoolDaysByEntry)) {
            return (float) $this->schoolDaysByEntry[$coverage];
        }

        return $this->schoolDaysByEntry[$coverage] = (float) SchoolYearInfo::query()
            ->where('school_year_id', $schoolYearId)
            ->where('coverage', $coverage)
            ->value('days');
    }

    public function getPendingValue(int $studentId, string $field, mixed $fallback = ''): mixed
    {
        return data_get($this->pendingChanges, "{$studentId}.{$this->entryNumber}.{$field}", $fallback);
    }

    public function setPendingValue(int $studentId, string $field, mixed $value): void
    {
        $this->pendingChanges[$studentId][$this->entryNumber][$field] = $value;
    }

    public function getAttendanceGradeLevelId(): ?int
    {
        $schoolYearId = SchoolYear::getActive()?->id;

        if (! $schoolYearId) {
            return null;
        }

        if (Auth::user()?->isAdmin()) {
            $gradeLevelId = data_get($this->tableFilters, 'grade_level_id.value');

            return filled($gradeLevelId) ? (int) $gradeLevelId : null;
        }

        return Homeroom::query()
            ->where('school_year_id', $schoolYearId)
            ->where(function (Builder $query): void {
                $query->where('user_id', Auth::id())
                    ->orWhere('user2_id', Auth::id());
            })
            ->value('grade_level_id');
    }

    public function getAttendanceCsvOptions(): array
    {
        $schoolYear = SchoolYear::getActive();
        $gradeLevelId = $this->getAttendanceGradeLevelId();
        $entry = $this->entryNumber;

        if (! $schoolYear || ! $gradeLevelId) {
            return [];
        }

        $enrollments = Enrollment::query()
            ->where('school_year_id', $schoolYear->id)
            ->where('grade_level_id', $gradeLevelId);

        if (! Auth::user()?->isAdmin()) {
            $homeroom = Homeroom::query()
                ->where('school_year_id', $schoolYear->id)
                ->where('grade_level_id', $gradeLevelId)
                ->where(function (Builder $query): void {
                    $query->where('user_id', Auth::id())
                        ->orWhere('user2_id', Auth::id());
                })
                ->first();

            if (! $homeroom) {
                return [];
            }

            $enrollments->when($homeroom->section_id, fn (Builder $query, $sectionId) => $query->where('section_id', $sectionId));
        }

        return [
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeLevelId,
            'entry' => $entry,
            'school_days' => $this->getSchoolDays($entry),
            'allowed_student_ids' => $enrollments->pluck('student_id')->map(fn ($id) => (int) $id)->all(),
        ];
    }

    public function getAttendanceCsvFileName(): string
    {
        $schoolYear = SchoolYear::getActive();
        $gradeLevel = GradeLevel::find($this->getAttendanceGradeLevelId());
        $entryLabel = CardTemplate::getOptions((int) ($schoolYear?->attendance_entries ?? 0))[$this->entryNumber]
            ?? 'entry-'.$this->entryNumber;

        return str('attendance-'.$gradeLevel?->label.'-'.$entryLabel.'-'.$schoolYear?->label)
            ->slug()
            ->toString();
    }

    public function downloadAttendanceImportTemplate(): StreamedResponse
    {
        $options = $this->getAttendanceCsvOptions();
        $csv = Writer::createFromFileObject(new SplTempFileObject);

        $csv->insertOne(['student_id', 'student_name', 'absent', 'tardy']);
        $csv->insertAll(AttendanceImporter::getExampleRows($options));

        return response()->streamDownload(function () use ($csv): void {
            $csv->setOutputBOM(Bom::Utf8);

            echo $csv->toString();
        }, $this->getAttendanceCsvFileName().'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Attendance (S.Y. '.(SchoolYear::getActive()?->label ?? 'Not configured').')';
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('importAttendance')
                ->label('Import CSV')
                ->importer(AttendanceImporter::class)
                ->options(fn (): array => $this->getAttendanceCsvOptions())
                ->registerModalActions([
                    Action::make('downloadAttendanceTemplate')
                        ->label('Download populated CSV template')
                        ->link()
                        ->action(fn (): StreamedResponse => $this->downloadAttendanceImportTemplate()),
                ])
                ->modalDescription(fn (ImportAction $action) => $action->getModalAction('downloadAttendanceTemplate'))
                ->disabled(fn (): bool => $this->getAttendanceGradeLevelId() === null)
                ->tooltip(fn (): ?string => $this->getAttendanceGradeLevelId() === null
                    ? 'Select a grade level before importing attendance.'
                    : null),
            Action::make('saveAttendance')
                ->label('Save Attendance')
                ->action(fn ($livewire) => $livewire->saveAttendance())
                ->button()
                ->color('primary'),
        ];
    }

    public function saveAttendance(): void
    {
        $schoolYear = SchoolYear::getActive();

        if (! $schoolYear) {
            Notification::make()->title('No active school year is configured.')->danger()->send();

            return;
        }

        $rules = [];
        $user = Auth::user();

        if ($user?->isAdmin()) {
            $allowedStudentIds = Enrollment::query()
                ->where('school_year_id', $schoolYear->id)
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        } else {
            $homeroom = Homeroom::query()
                ->where('school_year_id', $schoolYear->id)
                ->where(function ($query): void {
                    $query->where('user_id', Auth::id())
                        ->orWhere('user2_id', Auth::id());
                })
                ->first();

            if (! $homeroom) {
                Notification::make()->title('No homeroom is assigned to your account.')->danger()->send();

                return;
            }

            $allowedStudentIds = Enrollment::query()
                ->where('school_year_id', $schoolYear->id)
                ->where('grade_level_id', $homeroom->grade_level_id)
                ->when($homeroom->section_id, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        foreach ($this->pendingChanges as $studentId => $entries) {
            if (! in_array((int) $studentId, $allowedStudentIds, true)) {
                Notification::make()->title('Attendance contains a student outside your homeroom.')->danger()->send();

                return;
            }

            foreach ($entries as $entry => $changes) {
                if (array_key_exists('absent', $changes)) {
                    $rules["pendingChanges.{$studentId}.{$entry}.absent"] = ['numeric', 'min:0', 'max:'.$this->getSchoolDays((int) $entry)];
                }
                if (array_key_exists('tardy', $changes)) {
                    $rules["pendingChanges.{$studentId}.{$entry}.tardy"] = ['numeric', 'min:0'];
                }
            }
        }

        try {
            $this->validate($rules);
        } catch (ValidationException $exception) {
            Notification::make()->title('Attendance could not be saved.')->body($exception->validator->errors()->first())->danger()->send();

            return;
        }

        DB::transaction(function () use ($schoolYear): void {
            foreach ($this->pendingChanges as $studentId => $entries) {
                $attendance = Attendance::firstOrNew([
                    'student_id' => $studentId,
                    'school_year_id' => $schoolYear->id,
                ]);
                $attendanceData = $attendance->attendance ?? [];

                foreach ($entries as $entry => $changes) {
                    $attendanceData[$entry] ??= ['A' => 0, 'T' => 0];

                    if (array_key_exists('absent', $changes)) {
                        $attendanceData[$entry]['A'] = (float) $changes['absent'];
                    }
                    if (array_key_exists('tardy', $changes)) {
                        $attendanceData[$entry]['T'] = (float) $changes['tardy'];
                    }
                }

                $attendance->attendance = $attendanceData;
                $attendance->save();
            }
        });

        $this->pendingChanges = [];
        $this->resetTable();

        Notification::make()->title('Attendance saved.')->success()->send();
    }
}
