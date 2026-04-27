<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class CoordinatorReportController extends Controller
{
    public function index()
    {
        $status = request('status');
        $reports = Report::query();
        
        if ($status) {
            $reports->where('status', $status);
        }
        
        $reports = $reports->with('submittedBy', 'reviewedBy')->paginate(20);
        
        return view('coordinator.reports.index', ['reports' => $reports]);
    }

    public function show(Report $report)
    {
        return view('coordinator.reports.show', ['report' => $report]);
    }

    public function approve(Report $report)
    {
        $report->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Report approved successfully.');
    }

    public function reject(Request $request, Report $report)
    {
        $validated = $request->validate([
            'reviewer_comments' => 'required|string|min:10',
        ]);

        $report->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_comments' => $validated['reviewer_comments'],
        ]);

        return back()->with('success', 'Report rejected. Feedback sent to student.');
    }
}
