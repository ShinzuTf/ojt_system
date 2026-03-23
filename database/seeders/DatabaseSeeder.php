<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        User::create([
            'fname' => 'Admin',
            'mname' => null,
            'lname' => 'User',
            'suffix' => null,
            'email' => 'admin@philcst.edu.ph',
            'role' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        // Coordinator Account
        User::create([
            'fname' => 'Maria',
            'mname' => null,
            'lname' => 'Santos',
            'suffix' => null,
            'email' => 'msantos@philcst.edu.ph',
            'role' => 'coordinator',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        // Sample Student Account
        $student = User::create([
            'fname' => 'Juan',
            'mname' => 'Andres',
            'lname' => 'Dela Cruz',
            'suffix' => null,
            'email' => 'jdelacruz@philcst.edu.ph',
            'role' => 'student',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        \App\Models\OjtInfo::create([
            'user_id' => $student->id,
            'student_number' => '00038630',
            'course' => 'BSIT',
            'year_level' => 4,
            'company_name' => 'Accenture Philippines',
            'company_email' => 'hr@accenture.com',
            'ojt_status' => 'ongoing',
            'required_hours' => 486,
            'rendered_hours' => 120,
        ]);
    }
}
