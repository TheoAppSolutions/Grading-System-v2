<?php

namespace App\Support;

use App\Enums\Gender;
use App\Models\Config\GradeLevel;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class StudentImport
{
    /**
     * Parse and validate the legacy paste format without writing any records.
     *
     * @return array{rows: array<int, array<string, mixed>>, errors: array<int, string>}
     */
    public function preview(string $importCode): array
    {
        $rows = collect(preg_split('/[|\r\n]+/', trim($importCode)) ?: [])
            ->map(fn (string $row): array => str_getcsv(trim($row)))
            ->filter(fn (array $row): bool => count(array_filter($row, fn ($value) => trim((string) $value) !== '')) > 0)
            ->values();
        $errors = [];
        $prepared = [];
        $seenIds = [];
        $gradeLevels = GradeLevel::query()->get()->keyBy(fn ($level) => mb_strtolower(trim($level->label)));

        foreach ($rows as $index => $columns) {
            $line = $index + 1;
            if (count($columns) !== 10) {
                $errors[] = "Row {$line}: expected 10 comma-separated fields; found ".count($columns).'.';
                continue;
            }

            [$id, $last, $first, $middle, $suffix, $gender, $grade, $contact, $address, $email] = array_map(
                fn ($value) => trim((string) $value),
                $columns,
            );
            $id = ltrim($id, '0');
            $id = $id === '' ? '0' : $id;
            $gender = strtoupper($gender);
            $gradeKey = mb_strtolower($grade);
            $gradeLevel = $gradeLevels->get($gradeKey);

            if (! ctype_digit($id) || (int) $id < 1) {
                $errors[] = "Row {$line}: student ID must be a positive number.";
            } elseif (isset($seenIds[$id]) || Student::whereKey((int) $id)->exists()) {
                $errors[] = "Row {$line}: student ID {$id} already exists in this import or the system.";
            }
            $seenIds[$id] = true;

            if ($last === '' || $first === '') {
                $errors[] = "Row {$line}: first and last name are required.";
            }
            if (! array_key_exists($gender, Gender::options())) {
                $errors[] = "Row {$line}: gender must be M or F.";
            }
            if (! $gradeLevel) {
                $gradeLevel = $gradeLevels->first(fn ($level) => mb_strtolower((string) $level->id) === $gradeKey);
            }
            if (! $gradeLevel) {
                $errors[] = "Row {$line}: grade '{$grade}' is not configured in v2.";
            }
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$line}: email address is invalid.";
            }

            $prepared[] = [
                'id' => $id,
                'name' => trim("{$first} {$middle} {$last} {$suffix}"),
                'grade_label' => $gradeLevel?->label ?? $grade,
                'grade_level_id' => $gradeLevel?->id,
                'gender' => $gender,
                'contact' => $contact,
                'address' => $address,
                'email' => $email,
                'last' => $last,
                'first' => $first,
                'middle' => $middle,
                'suffix' => $suffix,
                'gradeLevel' => $gradeLevel,
            ];
        }

        return [
            'rows' => $prepared,
            'errors' => $errors,
        ];
    }

    public function import(string $importCode, SchoolYear $schoolYear): int
    {
        $preview = $this->preview($importCode);

        if ($preview['errors'] !== []) {
            throw ValidationException::withMessages(['import_code' => $preview['errors']]);
        }

        DB::transaction(function () use ($preview, $schoolYear): void {
            foreach ($preview['rows'] as $row) {
                Student::create([
                    'id' => (int) $row['id'],
                    'last_name' => $row['last'],
                    'first_name' => $row['first'],
                    'middle_name' => $row['middle'] ?: null,
                    'suffix' => $row['suffix'] ?: null,
                    'gender' => $row['gender'],
                    'active' => true,
                ]);
                StudentInfo::create([
                    'student_id' => (int) $row['id'],
                    'contact' => $row['contact'] ?: null,
                    'address' => $row['address'] ?: null,
                    'email_address' => $row['email'] ?: null,
                ]);
                Enrollment::create([
                    'student_id' => (int) $row['id'],
                    'school_year_id' => $schoolYear->id,
                    'grade_level_id' => $row['gradeLevel']->id,
                ]);
            }
        });

        return count($preview['rows']);
    }
}
