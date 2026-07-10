<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'ammjaromay@antiquespride.edu.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('marie@2025'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'researcher@example.com'],
            [
                'name' => 'Dr. Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'researcher',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'reviewer@example.com'],
            [
                'name' => 'Prof. Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
            ]
        );
    }
}
