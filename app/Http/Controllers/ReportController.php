<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportHistory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Get all reports for authenticated user
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Report::query();

        if ($user->role === 'student') {
            $query->where('submitted_by', $user->id);
        } elseif ($user->role === 'supervisor') {
            $query->whereHas('submittedBy', fn($q) => $q->where('company_id', $user->company_id));
        } elseif ($user->role === 'coordinator') {
            // Coordinator sees all reports
        }

        $reports = $query
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('report_type', $request->type))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($reports);
    }

    // Create a draft report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:weekly,monthly,incident',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
            'accomplishments' => 'required|string',
            'activities' => 'required|string',
            'challenges' => 'nullable|string',
            'learnings' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $report = Report::create([
            'submitted_by' => auth()->id(),
            'report_type' => $validated['report_type'],
            'report_period_start' => $validated['report_period_start'],
            'report_period_end' => $validated['report_period_end'],
            'accomplishments' => $validated['accomplishments'],
            'activities' => $validated['activities'],
            'challenges' => $validated['challenges'],
            'learnings' => $validated['learnings'],
            'recommendations' => $validated['recommendations'],
            'status' => 'draft',
        ]);

        return response()->json($report, 201);
    }

    // Update a draft report
    public function update(Request $request, Report $report)
    {
        if ($report->submitted_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($report->status !== 'draft') {
            return response()->json(['error' => 'Can only edit draft reports'], 422);
        }

        $validated = $request->validate([
            'accomplishments' => 'sometimes|string',
            'activities' => 'sometimes|string',
            'challenges' => 'nullable|string',
            'learnings' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $report->update($validated);
        $report->addHistory('Report updated by '.auth()->user()->fullName());

        return response()->json($report);
    }

    // Submit a report for review
    public function submit(Report $report)
    {
        if ($report->submitted_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($report->status !== 'draft') {
            return response()->json(['error' => 'Can only submit draft reports'], 422);
        }

        $report->status = 'submitted';
        $report->save();
        $report->addHistory('Report submitted for review');

        return response()->json($report);
    }

    // Reviewer: Approve a report
    public function approve(Request $request, Report $report)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['supervisor', 'coordinator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($report->status !== 'submitted' && $report->status !== 'under_review') {
            return response()->json(['error' => 'Report not in reviewable status'], 422);
        }

        $comments = $request->input('comments');

        $report->approve($user, $comments);

        return response()->json($report);
    }

    // Reviewer: Reject or request revision
    public function reject(Request $request, Report $report)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['supervisor', 'coordinator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $report->requestRevision($user, $validated['comments']);

        return response()->json($report);
    }

    // Escalate to coordinator
    public function escalate(Request $request, Report $report)
    {
        $user = auth()->user();

        if ($user->role !== 'supervisor') {
            return response()->json(['error' => 'Only supervisors can escalate'], 403);
        }

        $validated = $request->validate([
            'coordinator_id' => 'required|exists:users,id,role,coordinator',
            'reason' => 'required|string',
        ]);

        $coordinator = \App\Models\User::find($validated['coordinator_id']);

        $report->escalate($coordinator, $validated['reason']);

        return response()->json($report);
    }

    // Get report history
    public function history(Report $report)
    {
        $history = $report->histories()->with('changedBy')->orderByDesc('created_at')->get();

        return response()->json($history);
    }

    // Delete draft report
    public function destroy(Report $report)
    {
        if ($report->submitted_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($report->status !== 'draft') {
            return response()->json(['error' => 'Can only delete draft reports'], 422);
        }

        $report->delete();

        return response()->json(null, 204);
    }
}
