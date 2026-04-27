<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;

class CoordinatorSupervisorReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->isCoordinator(), 403);

        $query = Evaluation::with(['supervisor', 'trainee'])
            ->whereHas('supervisor', function ($q) {
                $q->where('role', 'supervisor');
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('evaluation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('evaluation_date', '<=', $request->date_to);
        }

        $reports = $query->latest('evaluation_date')
            ->paginate(20)
            ->withQueryString();

        return view('coordinator.supervisor-reports.index', [
            'reports' => $reports,
        ]);
    }
}