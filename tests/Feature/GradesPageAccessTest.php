<?php

namespace Tests\Feature;

use App\Filament\Pages\GradesPage;
use App\Models\Config\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\SubjectUser;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradesPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_can_access_an_assigned_subject(): void
    {
        [$schoolYear, $subject] = $this->createSubjectContext();
        $user = User::factory()->create();
        UserRole::query()->create(['user_id' => $user->id, 'role_id' => 5]);
        SubjectUser::query()->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->actingAs($user);
        request()->merge(['sid' => $subject->id, 'gid' => $subject->grade_level_id]);

        $this->assertTrue(GradesPage::canAccess());
    }

    public function test_non_admin_cannot_access_an_unassigned_subject(): void
    {
        [$schoolYear, $subject] = $this->createSubjectContext();
        $assignedUser = User::factory()->create();
        $otherUser = User::factory()->create();
        UserRole::query()->insert([
            ['user_id' => $assignedUser->id, 'role_id' => 5],
            ['user_id' => $otherUser->id, 'role_id' => 5],
        ]);
        SubjectUser::query()->create([
            'subject_id' => $subject->id,
            'user_id' => $assignedUser->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->actingAs($otherUser);
        request()->merge(['sid' => $subject->id, 'gid' => $subject->grade_level_id]);

        $this->assertFalse(GradesPage::canAccess());
    }

    public function test_admin_can_access_any_subject(): void
    {
        [, $subject] = $this->createSubjectContext();
        $admin = User::factory()->create();
        UserRole::query()->create(['user_id' => $admin->id, 'role_id' => 1]);

        $this->actingAs($admin);
        request()->merge(['sid' => $subject->id, 'gid' => $subject->grade_level_id]);

        $this->assertTrue(GradesPage::canAccess());
    }

    public function test_non_admin_cannot_access_with_a_mismatched_grade_level(): void
    {
        [$schoolYear, $subject] = $this->createSubjectContext();
        $user = User::factory()->create();
        UserRole::query()->create(['user_id' => $user->id, 'role_id' => 5]);
        SubjectUser::query()->create([
            'subject_id' => $subject->id,
            'user_id' => $user->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->actingAs($user);
        request()->merge(['sid' => $subject->id, 'gid' => $subject->grade_level_id + 1]);

        $this->assertFalse(GradesPage::canAccess());
    }

    private function createSubjectContext(): array
    {
        $schoolYear = SchoolYear::query()->create(['label' => '2026-2027', 'active' => 1]);
        $subject = Subject::query()->create([
            'subject_code' => 'MATH7',
            'subject_title' => 'Mathematics',
            'grade_level_id' => 7,
            'active' => 1,
        ]);

        return [$schoolYear, $subject];
    }
}
