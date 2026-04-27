<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorDtrController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all trainees' pending DTR
        $trainees = $user->supervisedPlacements()->get()->pluck('student_id')->toArray();
        $dtrs = DailyTimeRecord::whereIn('student_id', $trainees)
            ->where('status', 'pending')
            ->with('student')
            ->paginate(20);
        
        return view('supervisor.dtr.index', ['dtrs' => $dtrs]);
    }

    public function verify(DailyTimeRecord $dtr)
    {
        $user = Auth::user();
        
        // Check if user is supervisor of this trainee
        if (!$user->supervisedPlacements()->where('student_id', $dtr->student_id)->exists()) {
            abort(403);
        }

        $dtr->update([
            'status' => 'verified',
            'verified_by' => $user->id,
        ]);

        return back()->with('success', 'DTR entry verified successfully.');
    }

    public function reject(DailyTimeRecord $dtr)
    {
        $user = Auth::user();
        
        if (!$user->supervisedPlacements()->where('student_id', $dtr->student_id)->exists()) {
            abort(403);
        }

        $dtr->update([
            'status' => 'rejected',
            'verified_by' => $user->id,
        ]);

        return back()->with('error', 'DTR entry rejected.');
    }

    public function show(DailyTimeRecord $dtr)
    {
        return view('supervisor.dtr.show', ['dtr' => $dtr]);
    }
}
