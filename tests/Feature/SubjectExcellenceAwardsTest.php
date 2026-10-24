<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\SubjectExcellenceAwards;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectExcellenceAwardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_selection_replaces_the_non_pe_award_for_the_subject(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
        ]);
        $subject = Subject::query()->create([
            'subject_code' => 'MATH7',
            'subject_title' => 'Mathematics',
            'grade_level_id' => 7,
            'active' => 1,
        ]);
        $teacher = User::factory()->create();
        $students = collect([
            Student::query()->create([
                'id' => 1001,
                'first_name' => 'First',
                'middle_name' => null,
                'last_name' => 'Winner',
                'suffix' => null,
                'gender' => 'M',
                'active' => 1,
            ]),
            Student::query()->create([
                'id' => 1002,
                'first_name' => 'Second',
                'middle_name' => null,
                'last_name' => 'Winner',
                'suffix' => null,
                'gender' => 'F',
                'active' => 1,
            ]),
        ]);

        foreach ($students as $student) {
            Enrollment::query()->create([
                'student_id' => $student->id,
                'school_year_id' => $schoolYear->id,
                'grade_level_id' => 7,
            ]);

            foreach ([1 => 94, 2 => 96] as $quarter => $final) {
                Grade::query()->create([
                    'student_id' => $student->id,
                    'user_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'grade_level_id' => 7,
                    'school_year_id' => $schoolYear->id,
                    'quarter' => $quarter,
                    'final' => $final,
                ]);
            }
        }

        $service = app(SubjectExcellenceAwards::class);
        $service->selectForStudent($subject, $schoolYear->id, 7, 1001, $schoolYear->label);

        $this->assertDatabaseHas('awards', [
            'student_id' => 1001,
            'subject_id' => $subject->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $service->selectForStudent($subject, $schoolYear->id, 7, 1002, $schoolYear->label);

        $this->assertDatabaseMissing('awards', [
            'student_id' => 1001,
            'subject_id' => $subject->id,
            'school_year_id' => $schoolYear->id,
        ]);
        $this->assertDatabaseHas('awards', [
            'student_id' => 1002,
            'subject_id' => $subject->id,
            'school_year_id' => $schoolYear->id,
        ]);
    }
}
