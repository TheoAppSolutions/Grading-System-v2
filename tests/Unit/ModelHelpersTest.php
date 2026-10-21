<?php

namespace Tests\Unit;

use App\Models\Config\CardTemplate;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Configuration;
use App\Models\Student;
use App\Models\StudentField;
use App\Models\SubjectExcluded;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_name_and_status_helpers_use_model_attributes(): void
    {
        $student = Student::query()->create([
            'id' => 2001,
            'first_name' => 'Ada',
            'middle_name' => 'Lovelace',
            'last_name' => 'Byron',
            'suffix' => 'Jr.',
            'gender' => 'F',
            'active' => 1,
        ]);

        $this->assertSame('Byron, Ada Lovelace Jr.', $student->full_name);
        $this->assertSame('Byron, Ada', $student->facehash_avatar_name);
        $this->assertTrue($student->isActive());
    }

    public function test_student_field_get_and_set_are_upsert_helpers(): void
    {
        Student::query()->create([
            'id' => 2002,
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'gender' => 'F',
            'active' => 1,
        ]);

        $this->assertNull(StudentField::getField(2002, 'House'));

        StudentField::setField(2002, 'House', 'Blue');
        StudentField::setField(2002, 'House', 'Green');

        $this->assertSame('Green', StudentField::getField(2002, 'House'));
        $this->assertDatabaseCount('student_fields', 1);
    }

    public function test_configuration_get_set_and_forget_round_trip_values(): void
    {
        $this->assertSame('fallback', Configuration::get('missing', 'fallback'));

        Configuration::set('report.template', 'summary');
        $this->assertSame('summary', Configuration::get('report.template'));

        Configuration::forget('report.template');
        $this->assertNull(Configuration::get('report.template'));
    }

    public function test_school_year_returns_the_first_active_record(): void
    {
        SchoolYear::query()->create(['label' => '2025-2026', 'active' => 0, 'attendance_entries' => 4]);
        $active = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1, 'attendance_entries' => 2]);

        $this->assertTrue($active->is(SchoolYear::getActive()));
    }

    public function test_grade_scopes_filter_records_and_passed_helper_is_case_insensitive(): void
    {
        $user = User::factory()->create();
        Grade::query()->create([
            'student_id' => 3001,
            'user_id' => $user->id,
            'subject_id' => 10,
            'grade_level_id' => 7,
            'school_year_id' => 2026,
            'quarter' => 1,
            'final' => 85,
            'remarks' => 'PASSED',
        ]);
        Grade::query()->create([
            'student_id' => 3002,
            'user_id' => $user->id,
            'subject_id' => 11,
            'grade_level_id' => 8,
            'school_year_id' => 2025,
            'quarter' => 2,
            'final' => 50,
            'remarks' => 'FAILED',
        ]);

        $grade = Grade::query()->first();
        $this->assertTrue($grade->isPassed());
        $this->assertCount(1, Grade::bySchoolYear('2026')->byQuarter(1)->byGradeLevel(7)->bySubject('10')->passed()->get());
        $this->assertCount(1, Grade::failed()->get());
    }

    public function test_subject_exclusion_helper_checks_student_subject_and_year(): void
    {
        SubjectExcluded::query()->create([
            'student_id' => 2001,
            'subject_id' => 10,
            'school_year_id' => 2026,
        ]);

        $this->assertTrue(SubjectExcluded::isExcluded(2001, '10', '2026'));
        $this->assertFalse(SubjectExcluded::isExcluded(2001, '11', '2026'));
    }

    public function test_card_template_options_are_defined_for_supported_periods(): void
    {
        $this->assertSame([1 => '1st Semester', 2 => '2nd Semester'], CardTemplate::getOptions(2));
        $this->assertSame('Quarter', CardTemplate::getOptionsLabel(4));
        $this->assertSame('Month', CardTemplate::getOptionsLabel(12));
        $this->assertSame('October', CardTemplate::getOptions(12)[10]);
        $this->assertSame([], CardTemplate::getOptions(99));
    }
}
