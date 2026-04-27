<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentIssueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $issues = $user->reportedIssues()->paginate(15);
        
        return view('student.issues.index', ['issues' => $issues]);
    }

    public function create()
    {
        return view('student.issues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'issue_type' => 'required|in:absence,performance,workplace_issue,health_safety,schedule,other',
            'description' => 'required|string|min:10',
            'impact' => 'required|in:low,medium,high',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        $user = Auth::user();
        
        Issue::create([
            'student_id' => $user->id,
            'reported_by' => $user->id,
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'impact' => $validated['impact'],
            'status' => 'reported',
        ]);

        return redirect()->route('student.issues.index')->with('success', 'Issue reported successfully. Your supervisor will be notified.');
    }

    public function show(Issue $issue)
    {
        if ($issue->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.issues.show', ['issue' => $issue]);
    }
}
