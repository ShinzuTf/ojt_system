<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class TraineeController extends Controller
{
    /**
     * Display list of trainees
     */
    public function index()
    {
        $supervisor = auth()->user();
        
        // Only show trainees from the coordinator's assigned company
        $trainees = User::where('role', 'student')
            ->with('ojtInfo')
            ->join('ojt_info', 'users.id', '=', 'ojt_info.user_id')
            ->where('ojt_info.company_email', $supervisor->company_email)
            ->select('users.*')
            ->orderBy('users.lname')
            ->orderBy('users.fname')
            ->paginate(15);

        return view('supervisor.trainees.index', compact('trainees', 'supervisor'));
    }

    /**
     * Display trainee details and evaluation history
     */
    public function show($id)
    {
        $supervisor = auth()->user();

        $trainee = User::where('id', $id)
            ->where('role', 'student')
            ->with('ojtInfo')
            ->firstOrFail();

        // Verify that the trainee belongs to the supervisor's company
        if (!$trainee->ojtInfo || $trainee->ojtInfo->company_email !== $supervisor->company_email) {
            abort(403, 'You do not have permission to view this trainee.');
        }

        // Get evaluation history for this trainee by this supervisor
        $evaluations = Evaluation::where('trainee_id', $id)
            ->where('supervisor_id', $supervisor->id)
            ->latest('evaluation_date')
            ->paginate(10);

        // Calculate evaluation statistics
        $stats = [
            'total_evaluations' => $evaluations->total(),
            'approved' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->count(),
            'pending' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'pending')
                ->count(),
            'needs_revision' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'needs_revision')
                ->count(),
        ];

        // Calculate average ratings
        $avgRatings = [
            'technical_skills' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->whereNotNull('technical_skills_rating')
                ->avg('technical_skills_rating'),
            'communication' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->whereNotNull('communication_rating')
                ->avg('communication_rating'),
            'teamwork' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->whereNotNull('teamwork_rating')
                ->avg('teamwork_rating'),
            'professionalism' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->whereNotNull('professionalism_rating')
                ->avg('professionalism_rating'),
            'initiative' => Evaluation::where('trainee_id', $id)
                ->where('supervisor_id', $supervisor->id)
                ->where('status', 'approved')
                ->whereNotNull('initiative_rating')
                ->avg('initiative_rating'),
        ];

        return view('supervisor.trainees.show', compact(
            'trainee',
            'supervisor',
            'evaluations',
            'stats',
            'avgRatings'
        ));
    }
}
