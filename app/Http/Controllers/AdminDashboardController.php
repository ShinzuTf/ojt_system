<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCompanies = User::where('role', 'supervisor')->distinct('company_name')->count();
        $activePlacements = \App\Models\OjtPlacement::where('status', 'active')->count();
        
        $usersByRole = [
            'student' => User::where('role', 'student')->count(),
            'supervisor' => User::where('role', 'supervisor')->count(),
            'coordinator' => User::where('role', 'coordinator')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];
        
        $recentCompanies = User::where('role', 'supervisor')
            ->distinct('company_name')
            ->select('company_name', 'company_id')
            ->latest()
            ->take(5)
            ->get();
        
        // Activity stats for today
        $today = now()->toDateString();
        $dtrToday = \App\Models\DailyTimeRecord::whereDate('record_date', $today)->count();
        $reportsToday = \App\Models\Report::whereDate('created_at', $today)->count();
        $issuesReportedToday = \App\Models\Issue::whereDate('created_at', $today)->count();
        $approvalsToday = \App\Models\Report::whereDate('reviewed_at', $today)->count();
        
        $recentLogs = ActivityLog::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard-new', [
            'totalUsers' => $totalUsers,
            'totalCompanies' => $totalCompanies,
            'activePlacements' => $activePlacements,
            'systemHealth' => 100, // Default to 100%
            'usersByRole' => $usersByRole,
            'recentCompanies' => $recentCompanies,
            'dtrToday' => $dtrToday,
            'reportsToday' => $reportsToday,
            'issuesReportedToday' => $issuesReportedToday,
            'approvalsToday' => $approvalsToday,
            'recentLogs' => $recentLogs,
        ]);
    }
}
