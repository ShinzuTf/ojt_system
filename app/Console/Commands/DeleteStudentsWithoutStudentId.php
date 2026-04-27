<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteStudentsWithoutStudentId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:delete-without-id {--force : Actually delete the students}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all student accounts that do not have a student ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get students without student_number
        $studentsWithoutId = User::where('role', 'student')
            ->whereDoesntHave('ojtInfo', function ($query) {
                $query->whereNotNull('student_number');
            })
            ->orWhere(function ($query) {
                $query->where('role', 'student')
                    ->whereHas('ojtInfo', function ($q) {
                        $q->whereNull('student_number');
                    });
            })
            ->get();

        $count = $studentsWithoutId->count();

        if ($count === 0) {
            $this->info('No students without student ID found.');
            return 0;
        }

        $this->line("Found {$count} student(s) without student ID:\n");

        foreach ($studentsWithoutId as $student) {
            $this->line("  - {$student->fname} {$student->lname} (ID: {$student->id}, Email: {$student->email})");
        }

        if (!$this->option('force')) {
            $this->warn("\nNo students will be deleted. Use --force flag to actually delete them.");
            return 0;
        }

        if (!$this->confirm("\nAre you sure you want to delete these {$count} student(s)?")) {
            $this->info('Cancelled.');
            return 0;
        }

        foreach ($studentsWithoutId as $student) {
            $student->delete();
            $this->line("Deleted: {$student->fname} {$student->lname}");
        }

        $this->info("\n✓ Successfully deleted {$count} student(s).");
        return 0;
    }
}
