<?php

namespace App\Filament\Imports;

use App\Models\Attendance;
use App\Models\Enrollment;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

class AttendanceImporter extends Importer
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('student_id')
                ->label('Student ID')
                ->requiredMapping()
                ->integer()
                ->rules(['required', 'integer', 'exists:students,id']),
            ImportColumn::make('absent')
                ->label('Absent')
                ->requiredMapping()
                ->numeric(decimalPlaces: 1)
                ->rules(fn (array $options): array => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:'.(float) $options['school_days'],
                ])
                ->fillRecordUsing(function (Attendance $record, float|int $state, array $options): void {
                    $attendance = $record->attendance ?? [];
                    $entry = (int) $options['entry'];
                    $attendance[$entry] ??= ['A' => 0, 'T' => 0];
                    $attendance[$entry]['A'] = (float) $state;
                    $record->attendance = $attendance;
                }),
            ImportColumn::make('tardy')
                ->label('Tardy')
                ->requiredMapping()
                ->numeric(decimalPlaces: 1)
                ->rules(['required', 'numeric', 'min:0'])
                ->fillRecordUsing(function (Attendance $record, float|int $state, array $options): void {
                    $attendance = $record->attendance ?? [];
                    $entry = (int) $options['entry'];
                    $attendance[$entry] ??= ['A' => 0, 'T' => 0];
                    $attendance[$entry]['T'] = (float) $state;
                    $record->attendance = $attendance;
                }),
        ];
    }

    public function resolveRecord(): Attendance
    {
        $studentId = (int) $this->data['student_id'];
        $schoolYearId = (int) $this->options['school_year_id'];
        $gradeLevelId = (int) $this->options['grade_level_id'];
        $allowedStudentIds = array_map('intval', $this->options['allowed_student_ids'] ?? []);

        $isAllowed = in_array($studentId, $allowedStudentIds, true)
            && Enrollment::query()
                ->where('student_id', $studentId)
                ->where('school_year_id', $schoolYearId)
                ->where('grade_level_id', $gradeLevelId)
                ->exists();

        if (! $isAllowed) {
            throw ValidationException::withMessages([
                'student_id' => 'The student is not in the selected grade level or homeroom.',
            ]);
        }

        return Attendance::firstOrNew([
            'student_id' => $studentId,
            'school_year_id' => $schoolYearId,
        ]);
    }

    public static function getExampleRows(array $options): array
    {
        $schoolYearId = (int) $options['school_year_id'];
        $gradeLevelId = (int) $options['grade_level_id'];
        $entry = (int) $options['entry'];
        $allowedStudentIds = array_map('intval', $options['allowed_student_ids'] ?? []);

        return Enrollment::query()
            ->with([
                'student.attendance' => fn ($query) => $query->where('school_year_id', $schoolYearId),
            ])
            ->where('school_year_id', $schoolYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->whereIn('student_id', $allowedStudentIds)
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->orderBy('students.gender', 'desc')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->orderBy('students.id')
            ->select('enrollments.*')
            ->get()
            ->map(function (Enrollment $enrollment) use ($entry): array {
                $student = $enrollment->student;
                $name = trim(implode(' ', array_filter([
                    $student?->first_name,
                    $student?->middle_name,
                    $student?->last_name,
                    $student?->suffix,
                ])));
                $attendance = $student?->attendance?->first()?->attendance;

                return [
                    $enrollment->student_id,
                    preg_match('/^[=+\-@]/', $name) ? "'{$name}" : $name,
                    (float) data_get($attendance, $entry.'.A', 0),
                    (float) data_get($attendance, $entry.'.T', 0),
                ];
            })
            ->all();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' '.str('attendance row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
