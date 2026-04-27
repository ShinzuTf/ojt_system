<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use App\Models\DtrCorrection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DailyTimeRecordController extends Controller
{
    // Student: View their DTR entries
    public function index(Request $request)
    {
        $student = auth()->user();
        
        if ($student->role !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $records = DailyTimeRecord::forStudent($student->id)
            ->when($request->date_from, fn($q) => $q->whereDate('record_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('record_date', '<=', $request->date_to))
            ->orderByDesc('record_date')
            ->paginate(15);

        return response()->json($records);
    }

    // Student: Create a new DTR entry
    public function store(Request $request)
    {
        $validated = $request->validate([
            'record_date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $student = auth()->user();

        $dtr = DailyTimeRecord::create([
            'student_id' => $student->id,
            'record_date' => $validated['record_date'],
            'time_in' => $validated['time_in'],
            'time_out' => $validated['time_out'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        $dtr->calculateHours();
        $dtr->save();

        return response()->json($dtr, 201);
    }

    // Student: Update their DTR entry
    public function update(Request $request, DailyTimeRecord $dtr)
    {
        if ($dtr->student_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($dtr->status !== 'pending') {
            return response()->json(['error' => 'Can only edit pending DTR entries'], 422);
        }

        $validated = $request->validate([
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $dtr->update($validated);
        $dtr->calculateHours();
        $dtr->save();

        return response()->json($dtr);
    }

    // Supervisor: View pending DTR entries for their trainees
    public function supervisorPending(Request $request)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get supervisors assigned trainees
        $records = DailyTimeRecord::pending()
            ->whereHas('student', fn($q) => $q->where('company_id', $supervisor->company_id))
            ->orderByDesc('record_date')
            ->paginate(20);

        return response()->json($records);
    }

    // Supervisor: Verify a DTR entry
    public function verify(Request $request, DailyTimeRecord $dtr)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'supervisor_remarks' => 'nullable|string',
        ]);

        $dtr->verify($supervisor, $validated['supervisor_remarks'] ?? null);

        return response()->json($dtr);
    }

    // Supervisor: Reject a DTR entry
    public function reject(Request $request, DailyTimeRecord $dtr)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'supervisor_remarks' => 'required|string',
        ]);

        $dtr->reject($supervisor, $validated['supervisor_remarks']);

        return response()->json($dtr);
    }

    // Student: Request correction
    public function requestCorrection(Request $request, DailyTimeRecord $dtr)
    {
        if ($dtr->student_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'new_time_in' => 'required|date_format:H:i',
            'new_time_out' => 'required|date_format:H:i',
            'reason' => 'required|string',
        ]);

        $correction = DtrCorrection::create([
            'dtr_id' => $dtr->id,
            'student_id' => $dtr->student_id,
            'original_time_in' => $dtr->time_in,
            'new_time_in' => $validated['new_time_in'],
            'original_time_out' => $dtr->time_out,
            'new_time_out' => $validated['new_time_out'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return response()->json($correction, 201);
    }

    // Supervisor: View pending corrections
    public function supervisorCorrections(Request $request)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $corrections = DtrCorrection::pending()
            ->whereHas('student', fn($q) => $q->where('company_id', $supervisor->company_id))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($corrections);
    }

    // Supervisor: Approve correction
    public function approveCorrection(DtrCorrection $correction)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $correction->approve($supervisor);

        return response()->json($correction);
    }

    // Supervisor: Reject correction
    public function rejectCorrection(DtrCorrection $correction)
    {
        $supervisor = auth()->user();
        
        if ($supervisor->role !== 'supervisor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $correction->reject($supervisor);

        return response()->json($correction);
    }

    // Get DTR summary for a student
    public function summary($studentId, Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $records = DailyTimeRecord::forStudent($studentId)
            ->forDateRange($startDate, $endDate)
            ->get();

        $totalHours = $records->sum('hours_worked');
        $verifiedCount = $records->where('status', 'verified')->count();
        $pendingCount = $records->where('status', 'pending')->count();

        return response()->json([
            'total_records' => $records->count(),
            'total_hours' => $totalHours,
            'verified_count' => $verifiedCount,
            'pending_count' => $pendingCount,
            'records' => $records,
        ]);
    }
}
