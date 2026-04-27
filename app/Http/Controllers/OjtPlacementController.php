<?php

namespace App\Http\Controllers;

use App\Models\OjtPlacement;
use App\Models\Certification;
use App\Models\CompletionRecord;
use Illuminate\Http\Request;

class OjtPlacementController extends Controller
{
    // Get placements
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = OjtPlacement::query();

        if ($user->role === 'student') {
            $query->where('student_id', $user->id);
        } elseif ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        } elseif ($user->role === 'coordinator') {
            $query->where('coordinator_id', $user->id);
        }

        $placements = $query
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->with(['student', 'company', 'supervisor', 'coordinator'])
            ->orderByDesc('start_date')
            ->paginate(20);

        return response()->json($placements);
    }

    // Create placement
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'coordinator') {
            return response()->json(['error' => 'Only coordinators can create placements'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id,role,student',
            'company_id' => 'required|exists:users,id,role,supervisor',
            'supervisor_id' => 'required|exists:users,id,role,supervisor',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'total_required_hours' => 'required|integer|min:240|max:1000',
        ]);

        $placement = OjtPlacement::create([
            'student_id' => $validated['student_id'],
            'company_id' => $validated['company_id'],
            'supervisor_id' => $validated['supervisor_id'],
            'coordinator_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_required_hours' => $validated['total_required_hours'],
            'status' => 'active',
        ]);

        return response()->json($placement, 201);
    }

    // Get placement details
    public function show(OjtPlacement $placement)
    {
        return response()->json($placement->load(['student', 'company', 'supervisor', 'coordinator']));
    }

    // Get progress
    public function progress(OjtPlacement $placement)
    {
        $progressPercentage = $placement->getProgressPercentage();
        $daysElapsed = $placement->getDaysElapsed();
        $daysRemaining = $placement->getDaysRemaining();

        return response()->json([
            'placement_id' => $placement->id,
            'progress_percentage' => $progressPercentage,
            'days_elapsed' => $daysElapsed,
            'days_remaining' => $daysRemaining,
            'is_overdue' => $placement->isOverdue(),
            'total_required_hours' => $placement->total_required_hours,
            'start_date' => $placement->start_date,
            'end_date' => $placement->end_date,
        ]);
    }

    // Certification endpoints
    public function certifications(OjtPlacement $placement)
    {
        $certifications = $placement->certifications()->with(['issuedBy', 'verifiedBy'])->get();

        return response()->json($certifications);
    }

    // Create certification (supervisor)
    public function createCertification(Request $request, OjtPlacement $placement)
    {
        $supervisor = auth()->user();

        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'certification_date' => 'required|date',
            'actual_hours_worked' => 'required|integer|min:0',
            'final_rating' => 'nullable|numeric|min:1|max:5',
            'remarks' => 'nullable|string',
        ]);

        $certification = Certification::create([
            'placement_id' => $placement->id,
            'student_id' => $placement->student_id,
            'issued_by' => $supervisor->id,
            'certification_date' => $validated['certification_date'],
            'actual_hours_worked' => $validated['actual_hours_worked'],
            'final_rating' => $validated['final_rating'],
            'remarks' => $validated['remarks'],
            'status' => 'submitted',
        ]);

        return response()->json($certification, 201);
    }

    // Verify certification (coordinator)
    public function verifyCertification(Certification $certification)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $certification->verify($coordinator);

        return response()->json($certification);
    }

    // Approve certification (coordinator)
    public function approveCertification(Certification $certification)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $certification->approve($coordinator);

        return response()->json($certification);
    }

    // Get completion record
    public function completionRecord(OjtPlacement $placement)
    {
        $record = CompletionRecord::where('placement_id', $placement->id)->first();

        if (! $record) {
            return response()->json(['error' => 'No completion record found'], 404);
        }

        return response()->json($record);
    }

    // Mark as completed
    public function markCompleted(Request $request, OjtPlacement $placement)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'total_hours_completed' => 'required|integer|min:0',
            'final_grade' => 'nullable|numeric|min:1|max:5',
        ]);

        $record = CompletionRecord::updateOrCreate(
            ['placement_id' => $placement->id, 'student_id' => $placement->student_id],
            [
                'completion_date' => now()->toDateString(),
                'total_hours_completed' => $validated['total_hours_completed'],
                'final_grade' => $validated['final_grade'],
                'met_requirements' => $validated['total_hours_completed'] >= $placement->total_required_hours,
                'status' => 'pending',
            ]
        );

        return response()->json($record);
    }

    // Approve completion
    public function approveCompletion(Request $request, CompletionRecord $record)
    {
        $coordinator = auth()->user();

        if ($coordinator->role !== 'coordinator') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $record->approve($coordinator, $validated['remarks'] ?? null);

        return response()->json($record);
    }
}
