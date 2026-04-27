<?php

namespace App\Http\Controllers;

use App\Models\OjtPlacement;
use App\Models\User;

class CoordinatorTraineeController extends Controller
{
    public function index()
    {
        $status = request('status');

        $query = User::where('role', 'student')
            ->with([
                'ojtInfo',
                'placements' => function ($q) {
                    $q->with('supervisor')->orderByDesc('start_date');
                },
            ])
            ->orderBy('lname')
            ->orderBy('fname');

        if (in_array($status, ['active', 'completed'], true)) {
            $query->whereHas('placements', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $trainees = $query->paginate(15)->withQueryString();

        $trainees->getCollection()->transform(function ($trainee) {
            $trainee->currentPlacement = $trainee->placements->firstWhere('status', 'active')
                ?? $trainee->placements->first();
            return $trainee;
        });

        return view('coordinator.trainees.index', ['trainees' => $trainees]);
    }

    public function show(User $trainee)
    {
        $placement = OjtPlacement::where('student_id', $trainee->id)
            ->orderByDesc('start_date')
            ->first();

        // Get DTRs
        $dtr = $trainee->dailyTimeRecords()->latest('record_date')->take(10)->get();

        // Get reports
        $reports = $trainee->submittedReports()->latest('created_at')->take(10)->get();

        // Get issues
        $issues = $trainee->issues()->latest('created_at')->take(10)->get();

        return view('coordinator.trainees.show', [
            'trainee' => $trainee,
            'placement' => $placement,
            'dtr' => $dtr,
            'reports' => $reports,
            'issues' => $issues,
        ]);
    }
}
