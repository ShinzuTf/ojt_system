<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;

class EvaluationController extends Controller
{
    /**
     * Display list of evaluations received by student
     */
    public function myEvaluations()
    {
        $student = auth()->user();

        $evaluations = Evaluation::where('trainee_id', $student->id)
            ->with('supervisor')
            ->latest('evaluation_date')
            ->paginate(15);

        // Calculate statistics - dynamically from all evaluations
        $allEvaluations = Evaluation::where('trainee_id', $student->id)->get();
        
        $stats = [
            'total_evaluations' => $allEvaluations->count(),
            'avg_technical_skills' => $allEvaluations->whereNotNull('technical_skills_rating')->avg('technical_skills_rating'),
            'avg_communication' => $allEvaluations->whereNotNull('communication_rating')->avg('communication_rating'),
            'avg_teamwork' => $allEvaluations->whereNotNull('teamwork_rating')->avg('teamwork_rating'),
            'avg_professionalism' => $allEvaluations->whereNotNull('professionalism_rating')->avg('professionalism_rating'),
            'avg_initiative' => $allEvaluations->whereNotNull('initiative_rating')->avg('initiative_rating'),
        ];

        return view('student.evaluations.index', compact('evaluations', 'stats', 'student'));
    }
}
