<?php

namespace App\Filament\Imports;

use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;

class GradeImporter extends Importer
{
    protected static ?string $model = Grade::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('student_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'exists:students,id']),
            ImportColumn::make('midquarter')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:100']),
            ImportColumn::make('final')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:100']),
            ImportColumn::make('effort')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:5']),
            ImportColumn::make('deportment')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:100']),
            ImportColumn::make('remarks')
                ->rules(['max:191']),
        ];
    }

    public function resolveRecord(): Grade
    {
        $studentId = (int) $this->data['student_id'];
        $gradeLevelId = (int) $this->options['grade_level_id'];
        $schoolYearId = (int) $this->options['school_year_id'];

        $isEnrolled = Student::query()
                ->whereKey($studentId)
                ->whereHas('enrollments', fn ($query) => $query
                    ->where('grade_level_id', $gradeLevelId)
                    ->where('school_year_id', $schoolYearId))
                ->exists();

        if (! $isEnrolled) {
            throw ValidationException::withMessages([
                'student_id' => 'The student is not enrolled in the selected grade level and school year.',
            ]);
        }

        return Grade::firstOrNew([
            'student_id' => $studentId,
            'subject_id' => (int) $this->options['subject_id'],
            'school_year_id' => $schoolYearId,
            'quarter' => (int) $this->options['quarter'],
        ]);
    }

    public static function getExampleRows(array $options): array
    {
        $schoolYearId = (int) ($options['school_year_id'] ?? 0);
        $gradeLevelId = (int) ($options['grade_level_id'] ?? 0);
        $subjectId = (int) ($options['subject_id'] ?? 0);
        $quarter = (int) ($options['quarter'] ?? 1);

        return Enrollment::query()
            ->with([
                'student.grades' => fn ($query) => $query
                    ->where('school_year_id', $schoolYearId)
                    ->where('subject_id', $subjectId)
                    ->where('quarter', $quarter),
            ])
            ->where('school_year_id', $schoolYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->whereHas('student', fn ($query) => $query->where('active', 1))
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->orderBy('students.gender', 'desc')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->orderBy('students.id')
            ->select('enrollments.*')
            ->get()
            ->map(function (Enrollment $enrollment): array {
                $student = $enrollment->student;
                $name = trim(implode(' ', array_filter([
                    $student?->first_name,
                    $student?->middle_name,
                    $student?->last_name,
                    $student?->suffix,
                ])));
                $grade = $student?->grades?->first();

                return [
                    $enrollment->student_id,
                    preg_match('/^[=+\-@]/', $name) ? "'{$name}" : $name,
                    $grade?->midquarter,
                    $grade?->final,
                    $grade?->effort,
                    $grade?->deportment,
                    $grade?->remarks,
                ];
            })
            ->all();
    }

    protected function beforeFill(): void
    {
        $this->record->forceFill([
            'user_id' => (int) $this->options['user_id'],
            'grade_level_id' => (int) $this->options['grade_level_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your grade import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
