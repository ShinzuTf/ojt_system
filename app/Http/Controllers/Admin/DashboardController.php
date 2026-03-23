<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OjtInfo;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats
        $totalStudents = User::where('role', 'student')->count();
        
        // Students with completed OJT profiles
        $approvedTrainees = OjtInfo::whereNotNull('student_number')
            ->whereNotNull('company_name')
            ->count();

        // 2. Compliance Summary (students with OJT info and their status)
        $complianceSummary = User::where('role', 'student')
            ->with(['ojtInfo', 'requiredDocuments'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 
            'approvedTrainees',
            'complianceSummary'
        ));
    }
}
