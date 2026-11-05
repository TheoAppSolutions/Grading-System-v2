<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Support\StudentImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_reports_invalid_rows_without_writing_records(): void
    {
        $preview = app(StudentImport::class)->preview('x,Lee,Sam,,,,7,nope,Address,bad-email');

        $this->assertNotEmpty($preview['errors']);
        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_import_saves_student_details_and_enrollment(): void
    {
        $year = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        $count = app(StudentImport::class)->import(
            '12001,Lee,Sam,Jordan,Jr,M,7,555-0100,Main Street,sam@example.org',
            $year,
        );

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('students', ['id' => 12001, 'first_name' => 'Sam', 'last_name' => 'Lee']);
        $this->assertDatabaseHas('student_infos', ['student_id' => 12001, 'email_address' => 'sam@example.org']);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => 12001,
            'school_year_id' => $year->id,
            'grade_level_id' => 7,
        ]);
    }

    public function test_import_rejects_duplicate_ids_atomically(): void
    {
        $year = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        Student::query()->create([
            'id' => 12001,
            'first_name' => 'Existing',
            'last_name' => 'Student',
            'gender' => 'F',
            'active' => true,
        ]);

        try {
            app(StudentImport::class)->import(
                "12002,Lee,Sam,,,M,7,,,,\n12001,Other,Student,,,F,7,,,,",
                $year,
            );
            $this->fail('Expected duplicate ID validation to fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('import_code', $exception->errors());
        }

        $this->assertDatabaseMissing('students', ['id' => 12002]);
        $this->assertDatabaseCount('enrollments', 0);
        $this->assertDatabaseCount('student_infos', 0);
    }
}
