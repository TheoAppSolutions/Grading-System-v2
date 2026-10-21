<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Homeroom\Attendances\AttendanceResource;
use App\Filament\Imports\AttendanceImporter;
use App\Helpers\Formulas;
use App\Models\Attendance;
use App\Models\CommentCriteria;
use App\Models\Enrollment;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use App\Models\SchoolYearInfo;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_navigation_requires_a_homeroom_assignment_for_the_active_school_year(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 4,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(AttendanceResource::shouldRegisterNavigation());

        Homeroom::query()->create([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
            'user_id' => $user->id,
        ]);

        $this->assertTrue(AttendanceResource::shouldRegisterNavigation());
    }

    public function test_attendance_navigation_accepts_a_co_adviser_assignment(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 4,
        ]);
        $user = User::factory()->create();

        Homeroom::query()->create([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
            'user2_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->assertTrue(AttendanceResource::shouldRegisterNavigation());
    }

    public function test_attendance_is_cast_and_present_is_calculated_from_school_days(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 4,
        ]);
        SchoolYearInfo::query()->create([
            'school_year_id' => $schoolYear->id,
            'coverage' => 1,
            'days' => 50,
        ]);
        Student::query()->create([
            'id' => 1001,
            'first_name' => 'Sample',
            'last_name' => 'Student',
            'gender' => 1,
            'active' => 1,
        ]);
        Attendance::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'attendance' => ['1' => ['A' => 2.5, 'T' => 1]],
        ]);

        $attendance = Formulas::getAttendance(1001, $schoolYear->id);

        $this->assertSame(47.5, $attendance[1]['present']);
        $this->assertSame(2.5, $attendance[1]['absent']);
        $this->assertSame(50.0, $attendance[1]['schooldays']);
    }

    public function test_present_is_bounded_at_zero_when_legacy_absence_exceeds_school_days(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 4,
        ]);
        SchoolYearInfo::query()->create([
            'school_year_id' => $schoolYear->id,
            'coverage' => 1,
            'days' => 10,
        ]);
        Student::query()->create([
            'id' => 1001,
            'first_name' => 'Sample',
            'last_name' => 'Student',
            'gender' => 1,
            'active' => 1,
        ]);
        Attendance::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'attendance' => ['1' => ['A' => 11, 'T' => 0]],
        ]);

        $this->assertSame(0.0, Formulas::getAttendance(1001, $schoolYear->id)[1]['present']);
    }

    public function test_attendance_csv_import_only_updates_its_selected_month(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 10,
        ]);
        DB::table('students')->insert([
            'id' => 1001,
            'first_name' => 'Sample',
            'last_name' => 'Student',
            'gender' => 1,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Enrollment::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
        ]);
        Attendance::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'attendance' => ['9' => ['A' => 1, 'T' => 0]],
        ]);

        AttendanceImporter::test(options: [
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
            'entry' => 10,
            'school_days' => 20,
            'allowed_student_ids' => [1001],
        ])->import([
            'student_id' => 1001,
            'absent' => 2.5,
            'tardy' => 1,
        ])->assertImported();

        $attendance = Attendance::query()->where('student_id', 1001)->firstOrFail()->attendance;

        $this->assertSame(['A' => 1, 'T' => 0], $attendance[9]);
        $this->assertSame(['A' => 2.5, 'T' => 1], $attendance[10]);
    }

    public function test_attendance_csv_example_contains_students_and_selected_month_values(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
            'attendance_entries' => 10,
        ]);
        DB::table('students')->insert([
            'id' => 1001,
            'first_name' => 'Sample',
            'last_name' => 'Student',
            'gender' => 1,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Enrollment::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
        ]);
        Attendance::query()->create([
            'student_id' => 1001,
            'school_year_id' => $schoolYear->id,
            'attendance' => [
                '9' => ['A' => 1, 'T' => 0],
                '10' => ['A' => 2.5, 'T' => 1],
            ],
        ]);
        $rows = AttendanceImporter::getExampleRows([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 5,
            'entry' => 10,
            'school_days' => 20,
            'allowed_student_ids' => [1001],
        ]);

        $this->assertSame([[1001, 'Sample Student', 2.5, 1.0]], $rows);
    }

    public function test_comment_criteria_scopes_use_the_real_schema_columns(): void
    {
        CommentCriteria::query()->create([
            'comment_category_id' => 1,
            'code' => 'M1',
            'description' => 'Midquarter comment',
            'type' => 1,
            'active' => true,
        ]);
        CommentCriteria::query()->create([
            'comment_category_id' => 2,
            'code' => 'Q1',
            'description' => 'Quarter comment',
            'type' => 2,
            'active' => true,
        ]);

        $this->assertSame(['M1'], CommentCriteria::getMidquarterCriteria()->pluck('code')->all());
        $this->assertSame(['Q1'], CommentCriteria::getQuarterCriteria()->pluck('code')->all());
        $this->assertSame(['Q1'], CommentCriteria::query()->byCategory(2)->pluck('code')->all());
    }

    public function test_student_without_enrollments_has_an_empty_relationship(): void
    {
        DB::table('students')->insert([
            'id' => 1001,
            'first_name' => 'Blank',
            'last_name' => 'Student',
            'gender' => 1,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = Student::query()->findOrFail(1001);

        $this->assertCount(0, $student->enrollments);
    }
}
