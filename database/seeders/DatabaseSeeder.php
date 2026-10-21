<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Marcelodelgado\Announcements\Models\Announcement;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(ConfigurationSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Announcement::query()->updateOrCreate(
            ['title' => 'Welcome to the Grading System'],
            [
                'body' => 'Please review the active school year before recording enrollments, grades, or attendance.',
                'type' => 'info',
                'is_active' => true,
                'is_dismissible' => true,
                'starts_at' => null,
                'expires_at' => null,
            ],
        );
    }
}
