<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display supervisor/coordinator dashboard
     */
    public function index()
    {
        $supervisor = auth()->user();

        // Get all trainees from this supervisor's company
        $trainees = User::where('role', 'student')
            ->whereHas('ojtInfo', function($query) use ($supervisor) {
                $query->where('company_email', $supervisor->company_email);
            })
            ->with('ojtInfo')
            ->get();

        // Calculate statistics
        $totalTrainees = $trainees->count();
        $pendingEvaluations = Evaluation::where('supervisor_id', $supervisor->id)
            ->where('status', 'pending')
            ->count();
        $approvedEvaluations = Evaluation::where('supervisor_id', $supervisor->id)
            ->where('status', 'approved')
            ->count();
        
        // Get recent evaluations (only for trainees in this supervisor's company)
        $traineeIds = $trainees->pluck('id')->toArray();
        $recentEvaluations = Evaluation::where('supervisor_id', $supervisor->id)
            ->whereIn('trainee_id', $traineeIds)
            ->with(['trainee', 'trainee.ojtInfo'])
            ->latest('evaluation_date')
            ->limit(10)
            ->get();

        // Get trainees needing evaluation today
        $todayEvaluated = Evaluation::where('supervisor_id', $supervisor->id)
            ->whereIn('trainee_id', $traineeIds)
            ->whereDate('evaluation_date', today())
            ->count();

        return view('supervisor.dashboard', compact(
            'supervisor',
            'trainees',
            'totalTrainees',
            'pendingEvaluations',
            'approvedEvaluations',
            'recentEvaluations',
            'todayEvaluated'
        ));
    }
}
