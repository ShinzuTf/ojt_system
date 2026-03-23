<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EvaluationController extends Controller
{
    /**
     * Display list of evaluations
     */
    public function index(Request $request)
    {
        $supervisor = auth()->user();
        
        // Get trainees from this supervisor's company
        $companyTraineeIds = User::where('role', 'student')
            ->whereHas('ojtInfo', function($query) use ($supervisor) {
                $query->where('company_email', $supervisor->company_email);
            })
            ->pluck('id')
            ->toArray();

        $query = Evaluation::where('supervisor_id', $supervisor->id)
            ->whereIn('trainee_id', $companyTraineeIds)
            ->with(['trainee', 'trainee.ojtInfo']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by trainee
        if ($request->filled('trainee_id')) {
            $query->where('trainee_id', $request->trainee_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('evaluation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('evaluation_date', '<=', $request->date_to);
        }

        $evaluations = $query->latest('evaluation_date')
            ->paginate(15);

        $traineesData = User::where('role', 'student')
            ->whereHas('ojtInfo', function($query) use ($supervisor) {
                $query->where('company_email', $supervisor->company_email);
            })
            ->orderBy('lname')
            ->orderBy('fname')
            ->get();

        $trainees = $traineesData->pluck('full_name', 'id');

        return view('supervisor.evaluations.index', compact(
            'evaluations',
            'trainees',
            'supervisor'
        ));
    }

    /**
     * Show form to create new evaluation
     */
    public function create($trainee_id)
    {
        $supervisor = auth()->user();
        $trainee = User::where('id', $trainee_id)
            ->where('role', 'student')
            ->with('ojtInfo')
            ->firstOrFail();

        // Verify that the trainee belongs to the supervisor's company
        if (!$trainee->ojtInfo || $trainee->ojtInfo->company_email !== $supervisor->company_email) {
            abort(403, 'You do not have permission to evaluate this trainee.');
        }

        // Get today's evaluation if exists
        $today = today();
        $existingEvaluation = Evaluation::where('trainee_id', $trainee_id)
            ->where('supervisor_id', $supervisor->id)
            ->whereDate('evaluation_date', $today)
            ->first();

        if ($existingEvaluation && $existingEvaluation->status === 'approved') {
            return redirect()
                ->route('supervisor.trainees.show', $trainee_id)
                ->with('warning', 'An approved evaluation already exists for today.');
        }

        return view('supervisor.evaluations.create', compact(
            'trainee',
            'supervisor',
            'existingEvaluation'
        ));
    }

    /**
     * Store new evaluation
     */
    public function store(Request $request)
    {
        $supervisor = auth()->user();
        
        $validated = $request->validate([
            'trainee_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'strengths' => 'required|string|min:10',
            'areas_for_improvement' => 'required|string|min:10',
            'skills_to_develop' => 'required|string|min:10',
            'overall_comments' => 'nullable|string|max:1500',
            'technical_skills_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'teamwork_rating' => 'required|integer|min:1|max:5',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'initiative_rating' => 'required|integer|min:1|max:5',
        ]);

        // Ensure trainee is a student
        $trainee = User::where('id', $validated['trainee_id'])
            ->where('role', 'student')
            ->firstOrFail();

        // Verify that the trainee belongs to the supervisor's company
        if (!$trainee->ojtInfo || $trainee->ojtInfo->company_email !== $supervisor->company_email) {
            abort(403, 'You do not have permission to evaluate this trainee.');
        }

        // Check for duplicate evaluation on same date
        $existing = Evaluation::where('trainee_id', $validated['trainee_id'])
            ->where('supervisor_id', $supervisor->id)
            ->whereDate('evaluation_date', $validated['evaluation_date'])
            ->first();

        if ($existing && $existing->status === 'approved') {
            return redirect()
                ->back()
                ->with('error', 'An approved evaluation already exists for this date.');
        }

        $validated['supervisor_id'] = $supervisor->id;
        $validated['status'] = 'approved';

        if ($existing) {
            $existing->update($validated);
            $evaluation = $existing;
            $message = 'Evaluation updated successfully.';
        } else {
            $evaluation = Evaluation::create($validated);
            $message = 'Evaluation created successfully.';
        }

        return redirect()
            ->route('supervisor.trainees.show', $validated['trainee_id'])
            ->with('success', $message);
    }

    /**
     * Display evaluation details
     */
    public function show($id)
    {
        $supervisor = auth()->user();
        $evaluation = Evaluation::where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->with(['trainee', 'trainee.ojtInfo'])
            ->firstOrFail();

        return view('supervisor.evaluations.show', compact('evaluation', 'supervisor'));
    }

    /**
     * Show form to edit evaluation
     */
    public function edit($id)
    {
        $supervisor = auth()->user();
        $evaluation = Evaluation::where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->with('trainee')
            ->firstOrFail();

        // Check if already approved
        if ($evaluation->status === 'approved') {
            return redirect()
                ->route('supervisor.evaluations.show', $id)
                ->with('warning', 'Approved evaluations cannot be edited. Please contact administrator.');
        }

        return view('supervisor.evaluations.edit', compact('evaluation', 'supervisor'));
    }

    /**
     * Update evaluation
     */
    public function update(Request $request, $id)
    {
        $supervisor = auth()->user();
        $evaluation = Evaluation::where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->firstOrFail();

        // Check if already approved
        if ($evaluation->status === 'approved') {
            return redirect()
                ->back()
                ->with('error', 'Approved evaluations cannot be edited.');
        }

        $validated = $request->validate([
            'evaluation_date' => 'required|date|before_or_equal:today',
            'strengths' => 'required|string|min:10',
            'areas_for_improvement' => 'required|string|min:10',
            'skills_to_develop' => 'required|string|min:10',
            'overall_comments' => 'nullable|string|max:1500',
            'technical_skills_rating' => 'required|integer|min:1|max:5',
            'communication_rating' => 'required|integer|min:1|max:5',
            'teamwork_rating' => 'required|integer|min:1|max:5',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'initiative_rating' => 'required|integer|min:1|max:5',
        ]);

        $evaluation->update($validated);

        return redirect()
            ->route('supervisor.trainees.show', $evaluation->trainee_id)
            ->with('success', 'Evaluation updated successfully.');
    }

    /**
     * Approve evaluation
     */
    public function approve(Request $request, $id)
    {
        $supervisor = auth()->user();
        $evaluation = Evaluation::where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->firstOrFail();

        if ($evaluation->status === 'approved') {
            return redirect()
                ->back()
                ->with('warning', 'This evaluation is already approved.');
        }

        $evaluation->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Evaluation approved successfully.');
    }

    /**
     * Delete evaluation
     */
    public function destroy($id)
    {
        $supervisor = auth()->user();
        $evaluation = Evaluation::where('id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->firstOrFail();

        if ($evaluation->status === 'approved') {
            return redirect()
                ->back()
                ->with('error', 'Approved evaluations cannot be deleted.');
        }

        $trainee_id = $evaluation->trainee_id;
        $evaluation->delete();

        return redirect()
            ->route('supervisor.trainees.show', $trainee_id)
            ->with('success', 'Evaluation deleted successfully.');
    }
}
