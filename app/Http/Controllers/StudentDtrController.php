<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDtrController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dtrs = $user->dailyTimeRecords()->orderBy('record_date', 'desc')->paginate(15);
        
        $totalHours = $user->dailyTimeRecords()->get()->sum(function($dtr) { 
            return $dtr->hours_worked ?? 0;
        });
        $verifiedCount = $user->dailyTimeRecords()->where('status', 'verified')->count();
        $pendingCount = $user->dailyTimeRecords()->where('status', 'pending')->count();
        
        return view('student.dtr.index', [
            'dtrs' => $dtrs,
            'totalHours' => $totalHours,
            'verifiedCount' => $verifiedCount,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function create()
    {
        return view('student.dtr.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
        ], [
            'date.required' => 'Date is required.',
            'time_in.required' => 'Time In is required.',
        ]);

        $user = Auth::user();
        
        $dtr = DailyTimeRecord::create([
            'student_id' => $user->id,
            'record_date' => $validated['date'],
            'time_in' => $validated['date'] . ' ' . $validated['time_in'],
            'time_out' => null,
            'description' => null,
            'status' => 'pending',
        ]);

        return redirect()->route('student.dtr.show', $dtr->id)->with('success', 'Clocked in successfully. Set your clock out time now.');
    }

    public function show(DailyTimeRecord $dtr)
    {
        if ($dtr->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.dtr.show', ['dtr' => $dtr]);
    }

    public function update(Request $request, DailyTimeRecord $dtr)
    {
        if ($dtr->student_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'time_out' => 'required|date_format:H:i',
        ], [
            'time_out.required' => 'Clock Out time is required.',
        ]);

        // Get the date from time_in
        $date = $dtr->time_in->format('Y-m-d');
        
        $dtr->update([
            'time_out' => $date . ' ' . $validated['time_out'],
        ]);

        return redirect()->route('student.dtr.show', $dtr->id)->with('success', 'Clocked out successfully. Your time record has been saved.');
    }
}
