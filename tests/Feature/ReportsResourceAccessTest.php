<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Office\Reports\ReportsResource;
use App\Models\Enrollment;
use App\Models\Homeroom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use App\Support\ReportAccess;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_office_users_or_assigned_homeroom_teachers_can_access_reports(): void
    {
        $schoolYear = SchoolYear::query()->create([
            'label' => '2026-2027',
            'active' => 1,
        ]);

        foreach ([1 => true, 2 => false, 3 => true, 4 => false, 5 => false, 6 => false, 7 => false] as $roleId => $canAccess) {
            $user = $this->userWithRole($roleId);

            $this->actingAs($user);

            $this->assertSame($canAccess, ReportsResource::canAccess(), "Unexpected report access for role {$roleId}.");
        }

        $homeroom = $this->userWithRole(4);
        Homeroom::query()->create([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 7,
            'user_id' => $homeroom->id,
        ]);

        $this->actingAs($homeroom);

        $this->assertTrue(ReportsResource::canAccess());

        $teacher = $this->userWithRole(5);
        Homeroom::query()->create([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 8,
            'user_id' => $teacher->id,
        ]);

        $this->actingAs($teacher);

        $this->assertTrue(ReportsResource::canAccess());
    }

    public function test_homeroom_access_is_limited_to_assigned_grade_and_school_year(): void
    {
        $activeYear = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        $previousYear = SchoolYear::query()->create(['label' => '2025-2026', 'active' => 0]);
        $homeroomTeacher = $this->userWithRole(4);

        Homeroom::query()->create([
            'school_year_id' => $activeYear->id,
            'grade_level_id' => 7,
            'user_id' => $homeroomTeacher->id,
        ]);

        $this->student(1001);
        $this->student(1002);
        $this->student(1003);

        $assignedEnrollment = Enrollment::query()->create([
            'student_id' => 1001,
            'school_year_id' => $activeYear->id,
            'grade_level_id' => 7,
        ]);
        $otherGradeEnrollment = Enrollment::query()->create([
            'student_id' => 1002,
            'school_year_id' => $activeYear->id,
            'grade_level_id' => 8,
        ]);
        $previousYearEnrollment = Enrollment::query()->create([
            'student_id' => 1003,
            'school_year_id' => $previousYear->id,
            'grade_level_id' => 7,
        ]);

        $this->assertTrue(ReportAccess::canViewEnrollment($homeroomTeacher, $assignedEnrollment));
        $this->assertFalse(ReportAccess::canViewEnrollment($homeroomTeacher, $otherGradeEnrollment));
        $this->assertFalse(ReportAccess::canViewEnrollment($homeroomTeacher, $previousYearEnrollment));
        $this->assertSame([7], ReportAccess::accessibleGradeLevelIds($homeroomTeacher, $activeYear->id));
    }

    public function test_report_scope_returns_only_active_students_in_allowed_grade_levels(): void
    {
        $activeYear = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        $previousYear = SchoolYear::query()->create(['label' => '2025-2026', 'active' => 0]);
        $homeroomTeacher = $this->userWithRole(4);

        Homeroom::query()->create([
            'school_year_id' => $activeYear->id,
            'grade_level_id' => 7,
            'user_id' => $homeroomTeacher->id,
        ]);

        $this->student(1001);
        $this->student(1002);
        $this->student(1003);

        Enrollment::query()->create(['student_id' => 1001, 'school_year_id' => $activeYear->id, 'grade_level_id' => 7]);
        Enrollment::query()->create(['student_id' => 1002, 'school_year_id' => $activeYear->id, 'grade_level_id' => 8]);
        Enrollment::query()->create(['student_id' => 1003, 'school_year_id' => $previousYear->id, 'grade_level_id' => 7]);

        $visibleStudentIds = ReportAccess::scopeStudents(
            Student::query(),
            $homeroomTeacher,
            $activeYear->id,
        )->pluck('id')->all();

        $this->assertSame([1001], $visibleStudentIds);
    }

    public function test_assigned_teacher_sees_reports_in_the_homeroom_menu(): void
    {
        $schoolYear = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        $teacher = $this->userWithRole(5);

        Homeroom::query()->create([
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => 7,
            'user_id' => $teacher->id,
        ]);

        $this->actingAs($teacher);

        $reportGroups = collect(Filament::getPanel('app')->buildNavigation())
            ->filter(fn ($group) => collect($group->getItems())
                ->contains(fn ($item) => $item->getKey() === ReportsResource::class));

        $this->assertCount(1, $reportGroups);
        $this->assertSame('Homeroom Menu', $reportGroups->first()->getLabel());
    }

    private function userWithRole(int $roleId): User
    {
        $user = User::factory()->create();
        UserRole::query()->create(['user_id' => $user->id, 'role_id' => $roleId]);

        return $user;
    }

    private function student(int $id): Student
    {
        return Student::query()->create([
            'id' => $id,
            'first_name' => 'Student',
            'last_name' => (string) $id,
            'gender' => 'M',
            'active' => true,
        ]);
    }
}
