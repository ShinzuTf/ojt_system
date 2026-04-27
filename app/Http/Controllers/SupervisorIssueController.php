<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorIssueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $trainees = $user->supervisedPlacements()->get()->pluck('student_id')->toArray();
        $issues = Issue::whereIn('student_id', $trainees)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with('student')
            ->paginate(20);
        
        return view('supervisor.issues.index', ['issues' => $issues]);
    }

    public function create()
    {
        $user = Auth::user();
        $trainees = $user->supervisedPlacements()
            ->with('student')
            ->get()
            ->map(fn($p) => [
                'id' => $p->student_id,
                'name' => $p->student->fname . ' ' . $p->student->lname,
            ]);
        
        return view('supervisor.issues.create', ['trainees' => $trainees]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:ojt_placements,student_id',
            'issue_type' => 'required|in:absence,drop_transfer',
            'description' => 'required|string|min:10',
            'impact' => 'required|in:low,medium,high',
        ]);

        $user = Auth::user();
        
        // Verify supervisor has this trainee
        $hasTrainee = $user->supervisedPlacements()
            ->where('student_id', $validated['student_id'])
            ->exists();
        
        if (!$hasTrainee) {
            abort(403, 'You do not supervise this trainee.');
        }

        Issue::create([
            'student_id' => $validated['student_id'],
            'reported_by' => $user->id,
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'impact' => $validated['impact'],
            'status' => 'reported',
        ]);

        return redirect()->route('supervisor.issues.index')->with('success', 'Issue reported successfully. The coordinator will be notified.');
    }

    public function acknowledge(Issue $issue)
    {
        $user = Auth::user();
        
        if (!$user->supervisedPlacements()->where('student_id', $issue->student_id)->exists()) {
            abort(403);
        }

        $issue->update([
            'status' => 'acknowledged',
            'assigned_to' => $user->id,
        ]);

        return back()->with('success', 'Issue acknowledged.');
    }

    public function show(Issue $issue)
    {
        return view('supervisor.issues.show', ['issue' => $issue]);
    }
}
