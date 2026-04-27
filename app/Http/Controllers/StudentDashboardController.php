<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get placement stats
        $placement = $user->placements()->latest()->first();
        $hoursLogged = $user->dailyTimeRecords()
            ->whereMonth('record_date', now()->month)
            ->get()
            ->sum(function($dtr) { return $dtr->hours_worked ?? 0; });
        
        // Get today's DTR
        $todayDtr = $user->dailyTimeRecords()->whereDate('record_date', now())->first();
        
        // Get stats
        $stats = [
            'dtrCount' => $user->dailyTimeRecords()->count(),
            'reportCount' => $user->submittedReports()->count(),
            'evaluationCount' => 0,
            'issueCount' => $user->reportedIssues()->count(),
            'hoursLogged' => $hoursLogged,
        ];
        
        $recentReports = $user->submittedReports()->latest()->take(5)->get();
        $openIssues = $user->reportedIssues()->whereNotIn('status', ['resolved', 'closed'])->latest()->take(5)->get();
        
        return view('student.dashboard-new', [
            'placement' => $placement,
            'todayDtr' => $todayDtr,
            'stats' => $stats,
            'dtrCount' => $stats['dtrCount'],
            'reportCount' => $stats['reportCount'],
            'evaluationCount' => $stats['evaluationCount'],
            'issueCount' => $stats['issueCount'],
            'hoursLogged' => $hoursLogged,
            'recentReports' => $recentReports,
            'openIssues' => $openIssues,
        ]);
    }
}
