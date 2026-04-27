<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OjtPlacement;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorDashboardController extends Controller
{
    public function index()
    {
        // Get system-wide stats
        $studentCount = User::where('role', 'student')->count();
        $activePlacementCount = OjtPlacement::where('status', 'active')->count();
        $openIssueCount = Issue::whereNotIn('status', ['resolved', 'closed'])->count();
        $certificationCount = \App\Models\Certification::where('status', '!=', 'approved')->count();
        
        // Get at-risk students (less than 80% progress and time running out)
        $placements = OjtPlacement::where('status', 'active')->with('student')->get();
        $atRiskStudents = $placements->filter(function($p) {
            $progress = $p->getProgressPercentage();
            $daysRemaining = $p->getDaysRemaining();
            return $progress < 80 && $daysRemaining < 14;
        })->map(function($p) {
            $p->student->totalHours = $p->student->dailyTimeRecords()
                ->get()
                ->sum(function($dtr) { return $dtr->hours_worked ?? 0; });
            $p->student->currentPlacement = $p;
            return $p->student;
        });
        
        $onTrackCount = $placements->filter(function($p) {
            return $p->getProgressPercentage() >= 80;
        })->count();
        
        $reportCount = \App\Models\Report::count();
        $resolvedIssueCount = Issue::where('status', 'resolved')->count();
        $avgCompletion = $placements->avg(function($p) { return $p->getProgressPercentage(); }) ?? 0;
        
        $pendingIssues = Issue::whereNotIn('status', ['resolved', 'closed'])->latest()->take(5)->get();
        $pendingCertifications = \App\Models\Certification::where('status', '!=', 'approved')->latest()->take(5)->get();
        
        return view('coordinator.dashboard-new', [
            'studentCount' => $studentCount,
            'activePlacementCount' => $activePlacementCount,
            'openIssueCount' => $openIssueCount,
            'certificationCount' => $certificationCount,
            'atRiskStudents' => $atRiskStudents->take(5),
            'onTrackCount' => $onTrackCount,
            'reportCount' => $reportCount,
            'resolvedIssueCount' => $resolvedIssueCount,
            'avgCompletion' => $avgCompletion,
            'pendingIssues' => $pendingIssues,
            'pendingCertifications' => $pendingCertifications,
        ]);
    }
}
