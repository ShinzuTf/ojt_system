<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OjtInfo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompletedOjtSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== STUDENT 1: Maria Garcia Santos =====
        $student1 = User::create([
            'fname' => 'Maria',
            'mname' => 'Garcia',
            'lname' => 'Santos',
            'email' => 'maria.santos@ojt.local',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_number' => 'CS-2025-001',
            'course' => 'BS CS',
            'year_level' => 4,
            'status' => 'active',
        ]);

        // Supervisor 1: Mr. Robert Cruz
        User::create([
            'fname' => 'Robert',
            'mname' => 'Emmanuel',
            'lname' => 'Cruz',
            'email' => 'robert.cruz@acmetech.com',
            'password' => Hash::make('password123'),
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        // OJT Info for Student 1
        OjtInfo::create([
            'user_id' => $student1->id,
            'student_number' => 'CS-2025-001',
            'course' => 'BS CS',
            'year_level' => 4,
            'company_name' => 'AcmeTech Solutions Inc',
            'company_email' => 'hr@acmetech.com',
            'company_address' => '123 Tech Avenue, Manila',
            'supervisor_name' => 'Robert Emmanuel Cruz',
            'supervisor_contact' => '09123456789',
            'ojt_start' => Carbon::parse('2025-06-01'),
            'ojt_end' => Carbon::parse('2025-09-28'),
            'required_hours' => 720,
            'rendered_hours' => 720,
            'ojt_status' => 'completed',
        ]);

        // ===== STUDENT 2: Juan Carlos Dela Cruz =====
        $student2 = User::create([
            'fname' => 'Juan',
            'mname' => 'Carlos',
            'lname' => 'Dela Cruz',
            'email' => 'juan.delacruz@ojt.local',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_number' => 'CS-2025-002',
            'course' => 'BS IS',
            'year_level' => 4,
            'status' => 'active',
        ]);

        // Supervisor 2: Ms. Patricia Reyes
        User::create([
            'fname' => 'Patricia',
            'mname' => 'Nicole',
            'lname' => 'Reyes',
            'email' => 'patricia.reyes@innovatesoft.com',
            'password' => Hash::make('password123'),
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        // OJT Info for Student 2
        OjtInfo::create([
            'user_id' => $student2->id,
            'student_number' => 'CS-2025-002',
            'course' => 'BS IS',
            'year_level' => 4,
            'company_name' => 'InnovateSoft Technologies',
            'company_email' => 'recruitment@innovatesoft.com',
            'company_address' => '456 Innovation Street, Quezon City',
            'supervisor_name' => 'Patricia Nicole Reyes',
            'supervisor_contact' => '09987654321',
            'ojt_start' => Carbon::parse('2025-06-02'),
            'ojt_end' => Carbon::parse('2025-09-29'),
            'required_hours' => 720,
            'rendered_hours' => 715,
            'ojt_status' => 'completed',
        ]);

        $this->command->info('✓ Sample completed OJT data created successfully!');
        $this->command->info('Student 1: Maria Garcia Santos (CS-2025-001) - 720/720 hours completed');
        $this->command->info('Student 2: Juan Carlos Dela Cruz (CS-2025-002) - 715/720 hours completed');
    }
}
