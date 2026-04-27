<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all trainees assigned to this supervisor
        $trainees = $user->supervisedPlacements()->get()->pluck('student')->unique();
        
        // Get pending DTR
        $pendingDtr = $trainees->flatMap(function($trainee) {
            return $trainee->dailyTimeRecords()->where('status', 'pending')->get();
        });
        
        // Get open issues
        $openIssues = $trainees->flatMap(function($trainee) {
            return $trainee->reportedIssues()->whereNotIn('status', ['resolved', 'closed'])->get();
        });
        
        return view('supervisor.dashboard-new', [
            'trainees' => $trainees,
            'traineeCount' => $trainees->count(),
            'pendingDtrCount' => $pendingDtr->count(),
            'openIssueCount' => $openIssues->count(),
            'pendingDtr' => $pendingDtr->take(5),
            'openIssues' => $openIssues->take(5),
        ]);
    }
}
