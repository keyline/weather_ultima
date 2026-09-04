<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'info@weather.com'],
            [
                'name' => 'Weather Ultima Admin',
                'password' => Hash::make('weather@123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );
    }
}
