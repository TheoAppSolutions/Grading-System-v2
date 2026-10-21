<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Seed the application's configurations.
     */
    public function run(): void
    {
        Configuration::updateOrCreate(
            ['field_name' => 'default_password'],
            ['field_value' => '654321'],
        );
    }
}
