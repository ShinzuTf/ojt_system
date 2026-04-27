<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyTimeRecord;
use App\Models\Report;
use App\Models\Issue;
use App\Models\OjtPlacement;

class OjtMonitoringService
{
    /**
     * Get dashboard stats for a student
     */
    public function getStudentStats(User $student): array
    {
        return [
            'dtr_submitted' => DailyTimeRecord::forStudent($student->id)->count(),
            'dtr_verified' => DailyTimeRecord::forStudent($student->id)->verified()->count(),
            'dtr_pending' => DailyTimeRecord::forStudent($student->id)->pending()->count(),
            'hours_worked' => DailyTimeRecord::forStudent($student->id)->sum('hours_worked'),
            'reports_submitted' => Report::where('submitted_by', $student->id)->submitted()->count(),
            'reports_approved' => Report::where('submitted_by', $student->id)->approved()->count(),
            'pending_evaluations' => $student->evaluations()->where('status', 'pending')->count(),
            'active_placement' => OjtPlacement::forStudent($student->id)->active()->first(),
        ];
    }

    /**
     * Get dashboard stats for a supervisor
     */
    public function getSupervisorStats(User $supervisor): array
    {
        return [
            'total_trainees' => User::where('company_id', $supervisor->company_id)->where('role', 'student')->count(),
            'pending_dtr' => DailyTimeRecord::pending()
                ->whereHas('student', fn($q) => $q->where('company_id', $supervisor->company_id))
                ->count(),
            'pending_reports' => Report::submitted()
                ->whereHas('submittedBy', fn($q) => $q->where('company_id', $supervisor->company_id))
                ->count(),
            'open_issues' => Issue::where('reported_by', $supervisor->id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),
        ];
    }

    /**
     * Get dashboard stats for a coordinator
     */
    public function getCoordinatorStats(User $coordinator): array
    {
        return [
            'total_students' => User::where('role', 'student')->count(),
            'active_placements' => OjtPlacement::active()->count(),
            'completed_placements' => OjtPlacement::completed()->count(),
            'pending_issues' => Issue::where('status', 'reported')->count(),
            'pending_certifications' => \App\Models\Certification::submitted()->count(),
            'pending_completions' => \App\Models\CompletionRecord::pending()->count(),
        ];
    }

    /**
     * Calculate student completion progress
     */
    public function getStudentProgress(User $student): array
    {
        $placement = $student->placements()->active()->first();

        if (! $placement) {
            return ['error' => 'No active placement'];
        }

        $hoursWorked = DailyTimeRecord::forStudent($student->id)
            ->verified()
            ->sum('hours_worked');

        return [
            'placement_id' => $placement->id,
            'required_hours' => $placement->total_required_hours,
            'hours_completed' => $hoursWorked,
            'percentage' => ($hoursWorked / $placement->total_required_hours) * 100,
            'days_elapsed' => $placement->getDaysElapsed(),
            'days_remaining' => $placement->getDaysRemaining(),
            'is_on_track' => $this->isStudentOnTrack($student, $placement),
        ];
    }

    /**
     * Check if student is on track with their OJT
     */
    public function isStudentOnTrack(User $student, OjtPlacement $placement): bool
    {
        $hoursWorked = DailyTimeRecord::forStudent($student->id)
            ->verified()
            ->sum('hours_worked');

        $totalDays = $placement->start_date->diffInDays($placement->end_date);
        $daysElapsed = $placement->start_date->diffInDays(now());

        $expectedHours = ($hoursElapsed / $totalDays) * $placement->total_required_hours;
        $threshold = $expectedHours * 0.8; // 80% of expected

        return $hoursWorked >= $threshold;
    }

    /**
     * Generate monitoring report for coordinator
     */
    public function generateMonitoringReport(string $startDate, string $endDate): array
    {
        $placements = OjtPlacement::whereDate('start_date', '>=', $startDate)
            ->whereDate('end_date', '<=', $endDate)
            ->get();

        $report = [];

        foreach ($placements as $placement) {
            $student = $placement->student;
            $hoursWorked = DailyTimeRecord::forStudent($student->id)
                ->forDateRange($startDate, $endDate)
                ->verified()
                ->sum('hours_worked');

            $report[] = [
                'student_name' => $student->fullName(),
                'company' => $placement->company->company_name,
                'hours_completed' => $hoursWorked,
                'hours_required' => $placement->total_required_hours,
                'completion_percentage' => ($hoursWorked / $placement->total_required_hours) * 100,
                'reports_submitted' => Report::where('submitted_by', $student->id)
                    ->forPeriod($startDate, $endDate)
                    ->approved()
                    ->count(),
                'issues_count' => Issue::where('student_id', $student->id)
                    ->whereDate('issue_date', '>=', $startDate)
                    ->whereDate('issue_date', '<=', $endDate)
                    ->count(),
                'status' => $placement->status,
            ];
        }

        return $report;
    }
}
