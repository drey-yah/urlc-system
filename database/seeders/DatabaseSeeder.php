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
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'researcher@example.com'],
            [
                'name' => 'Dr. Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'researcher',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'reviewer@example.com'],
            [
                'name' => 'Prof. Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'rjvtongcua@antiquespride.edu.ph'],
            [
                'name' => 'Recording Staff',
                'password' => Hash::make('marie@2025'),
                'role' => 'recording_staff',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'mixedsignals790@gmail.com'],
            [
                'name' => 'Budget Officer',
                'password' => Hash::make('marie@2025'),
                'role' => 'budget_officer',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'collegecoordinator4@gmail.com'],
            [
                'name' => 'College Coordinator',
                'password' => Hash::make('marie@2025'),
                'role' => 'coordinator',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'collegedean626@gmail.com'],
            [
                'name' => 'College Dean',
                'password' => Hash::make('marie@2025'),
                'role' => 'dean',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'andreana@gmail.com'],
            [
                'name' => 'VPREI',
                'password' => Hash::make('marie@2025'),
                'role' => 'vprei',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'ammjaromayu@gmail.com'],
            [
                'name' => 'Vice President for Academic & Finance (VPAF)',
                'password' => Hash::make('marie@2025'),
                'role' => 'vpaf',
                'is_approved' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'president@antiquespride.edu.ph'],
            [
                'name' => 'Dr. Pablo S. Crespo Jr.',
                'password' => Hash::make('marie@2025'),
                'role' => 'president',
                'is_approved' => true,
            ]
        );

        // Seed Institutional Budget Allocations Matrix
        $this->call(BudgetAllocationSeeder::class);
    }
}
