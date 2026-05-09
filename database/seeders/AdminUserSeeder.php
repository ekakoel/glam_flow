<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@domain.com'],
            [
                'name' => 'Admin',
                'password' => '1234567890',
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
    }
}

