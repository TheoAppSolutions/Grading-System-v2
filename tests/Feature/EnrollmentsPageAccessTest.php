<?php

namespace Tests\Feature;

use App\Filament\Pages\EnrollmentsPage;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentsPageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_office_and_admin_users_can_access_enrollments(): void
    {
        foreach ([1 => true, 2 => false, 3 => true, 4 => false, 5 => false, 6 => false, 7 => false] as $roleId => $canAccess) {
            $user = User::factory()->create();
            UserRole::query()->create([
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);

            $this->actingAs($user);

            $this->assertSame($canAccess, EnrollmentsPage::canAccess(), "Unexpected enrollment access for role {$roleId}.");
        }
    }

    public function test_guests_cannot_access_enrollments(): void
    {
        $this->assertFalse(EnrollmentsPage::canAccess());
    }
}
