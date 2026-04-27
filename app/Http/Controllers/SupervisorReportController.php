<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $trainees = $user->supervisedPlacements()->get()->pluck('student_id')->toArray();
        $reports = Report::whereIn('student_id', $trainees)
            ->where('status', 'submitted')
            ->with('student')
            ->paginate(20);
        
        return view('supervisor.reports.index', ['reports' => $reports]);
    }

    public function show(Report $report)
    {
        $user = Auth::user();
        
        if (!$user->supervisedPlacements()->where('student_id', $report->student_id)->exists()) {
            abort(403);
        }

        return view('supervisor.reports.show', ['report' => $report]);
    }
}
