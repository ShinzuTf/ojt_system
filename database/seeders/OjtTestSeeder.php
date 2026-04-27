<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DailyTimeRecord;
use App\Models\Report;
use App\Models\Issue;
use App\Models\OjtPlacement;
use App\Models\Certification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OjtTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ojt.local'],
            [
                'fname' => 'Admin',
                'lname' => 'User',
                'email' => 'admin@ojt.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create coordinator user
        $coordinator = User::firstOrCreate(
            ['email' => 'coordinator@ojt.local'],
            [
                'fname' => 'Juan',
                'lname' => 'Coordinator',
                'email' => 'coordinator@ojt.local',
                'password' => Hash::make('coordinator123'),
                'role' => 'coordinator',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create supervisor users
        $supervisor1 = User::firstOrCreate(
            ['email' => 'supervisor1@ojt.local'],
            [
                'fname' => 'Maria',
                'lname' => 'Supervisor',
                'email' => 'supervisor1@ojt.local',
                'password' => Hash::make('supervisor123'),
                'role' => 'supervisor',
                'company_name' => 'TechCorp Philippines',
                'company_position' => 'HR Manager',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $supervisor2 = User::firstOrCreate(
            ['email' => 'supervisor2@ojt.local'],
            [
                'fname' => 'Pedro',
                'lname' => 'Supervisor',
                'email' => 'supervisor2@ojt.local',
                'password' => Hash::make('supervisor123'),
                'role' => 'supervisor',
                'company_name' => 'Digital Solutions Inc',
                'company_position' => 'Project Manager',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create student users
        for ($i = 1; $i <= 3; $i++) {
            $student = User::firstOrCreate(
                ['email' => "student{$i}@ojt.local"],
                [
                    'fname' => "Student",
                    'lname' => "User {$i}",
                    'email' => "student{$i}@ojt.local",
                    'password' => Hash::make('student123'),
                    'role' => 'student',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Create placement for student
            $supervisor = $i % 2 === 0 ? $supervisor2 : $supervisor1;
            
            $placement = OjtPlacement::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'supervisor_id' => $supervisor->id,
                ],
                [
                    'coordinator_id' => $coordinator->id,
                    'company_id' => $supervisor->id,
                    'start_date' => now()->subDays(30),
                    'end_date' => now()->addDays(60),
                    'total_required_hours' => 480,
                    'status' => 'active',
                ]
            );

            // Create DTR entries for student (15 entries, 8 hours each)
            for ($j = 0; $j < 15; $j++) {
                $date = now()->subDays(15 - $j);
                $isVerified = $j % 3 !== 0;
                
                DailyTimeRecord::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'record_date' => $date->toDateString(),
                    ],
                    [
                        'time_in' => '08:00:00',
                        'time_out' => '17:00:00',
                        'hours_worked' => 8.0,
                        'notes' => 'Regular work day - Day ' . ($j + 1),
                        'status' => $isVerified ? 'verified' : 'pending',
                        'verified_by' => $isVerified ? $supervisor->id : null,
                        'verified_at' => $isVerified ? now()->subDays(15 - $j) : null,
                    ]
                );
            }

            // Create reports for student (2 entries)
            for ($j = 0; $j < 2; $j++) {
                $startDate = now()->subDays(30 - ($j * 15));
                $isApproved = $j === 0;
                
                Report::firstOrCreate(
                    [
                        'submitted_by' => $student->id,
                        'report_period_start' => $startDate->toDateString(),
                    ],
                    [
                        'report_type' => $j === 0 ? 'weekly' : 'weekly',
                        'report_period_start' => $startDate,
                        'report_period_end' => $startDate->copy()->addDays(7),
                        'accomplishments' => 'Completed assigned tasks and learned new skills',
                        'activities' => 'Participated in team meetings and project development',
                        'challenges' => 'Had some difficulty with the new system',
                        'learnings' => 'Learned important lessons about teamwork',
                        'recommendations' => 'Continue with current projects',
                        'status' => $isApproved ? 'approved' : 'submitted',
                        'reviewed_by' => $isApproved ? $supervisor->id : null,
                        'reviewed_at' => $isApproved ? now() : null,
                    ]
                );
            }

            // Create issues for student (only student 1)
            if ($i === 1) {
                Issue::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'issue_type' => 'absence',
                    ],
                    [
                        'reported_by' => $student->id,
                        'company_id' => $supervisor->id,
                        'issue_date' => now()->subDays(5)->toDateString(),
                        'description' => 'Absent on April 10 due to personal reasons',
                        'status' => 'acknowledged',
                        'assigned_to' => $supervisor->id,
                    ]
                );
            }

            // Create certification for student
            if ($placement) {
                Certification::firstOrCreate(
                    ['placement_id' => $placement->id],
                    [
                        'student_id' => $student->id,
                        'issued_by' => $supervisor->id,
                        'actual_hours_worked' => 240,
                        'final_rating' => 4.0,
                        'remarks' => 'Good performance throughout the OJT',
                        'status' => 'submitted',
                        'certification_date' => now()->toDateString(),
                    ]
                );
            }
        }

        $this->command->info('OJT test data seeded successfully!');
        $this->command->line('');
        $this->command->info('Test Account Credentials:');
        $this->command->line('Admin:        admin@ojt.local / admin123');
        $this->command->line('Coordinator:  coordinator@ojt.local / coordinator123');
        $this->command->line('Supervisor 1: supervisor1@ojt.local / supervisor123');
        $this->command->line('Supervisor 2: supervisor2@ojt.local / supervisor123');
        $this->command->line('Student 1:    student1@ojt.local / student123');
        $this->command->line('Student 2:    student2@ojt.local / student123');
        $this->command->line('Student 3:    student3@ojt.local / student123');
    }
}
