<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueUpdate;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    // List issues
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Issue::query();

        if ($user->role === 'student') {
            $query->where('student_id', $user->id);
        } elseif ($user->role === 'supervisor') {
            $query->where('reported_by', $user->id);
        } elseif ($user->role === 'coordinator') {
            // Coordinator sees all or assigned issues
            if ($request->assigned_to_me) {
                $query->where('assigned_to', $user->id);
            }
        }

        $issues = $query
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('issue_type', $request->type))
            ->orderByDesc('issue_date')
            ->paginate(20);

        return response()->json($issues);
    }

    // Create an issue
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'supervisor') {
            return response()->json(['error' => 'Only supervisors can report issues'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'issue_type' => 'required|in:absence,drop,transfer,behavioral,performance,other',
            'issue_date' => 'required|date',
            'description' => 'required|string',
            'action_taken' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        $issue = Issue::create([
            'student_id' => $validated['student_id'],
            'reported_by' => $user->id,
            'company_id' => $user->company_id,
            'issue_type' => $validated['issue_type'],
            'issue_date' => $validated['issue_date'],
            'description' => $validated['description'],
            'action_taken' => $validated['action_taken'],
            'effective_date' => $validated['effective_date'],
            'status' => 'reported',
        ]);

        $issue->addUpdate('Issue reported by '.$user->fullName());

        return response()->json($issue, 201);
    }

    // Get issue details
    public function show(Issue $issue)
    {
        return response()->json($issue->load('updates'));
    }

    // Coordinator: Acknowledge an issue
    public function acknowledge(Issue $issue)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $issue->acknowledge($coordinator);

        return response()->json($issue);
    }

    // Coordinator: Resolve an issue
    public function resolve(Request $request, Issue $issue)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $issue->resolve($coordinator, $validated['resolution_notes']);

        return response()->json($issue);
    }

    // Mark student as dropped
    public function markDropped(Request $request, Issue $issue)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $issue->markDropped($validated['notes'] ?? '');

        return response()->json($issue);
    }

    // Mark student as transferred
    public function markTransferred(Request $request, Issue $issue)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $issue->markTransferred($validated['notes'] ?? '');

        return response()->json($issue);
    }

    // Get issue updates
    public function updates(Issue $issue)
    {
        $updates = $issue->updates()->with('updatedBy')->orderByDesc('created_at')->get();

        return response()->json($updates);
    }
}
