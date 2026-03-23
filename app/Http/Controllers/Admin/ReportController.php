<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:trainees_by_course,profile_completion,evaluation_summary,trainee_placement',
            'export_format' => 'required|in:pdf,xlsx,csv',
            'course_filter' => 'nullable|in:BSIT,BSCS',
            'academic_year' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $reportData = $this->buildReport($validated);

        return back()->with('success', 'Report generated successfully.');
    }

    private function buildReport($filters)
    {
        $query = User::where('role', 'student')->with('ojtInfo', 'evaluationsReceived');

        // Apply course filter
        if (!empty($filters['course_filter'])) {
            $query->whereHas('ojtInfo', function($q) use ($filters) {
                $q->where('course', $filters['course_filter']);
            });
        }

        switch ($filters['report_type']) {
            case 'trainees_by_course':
                return $query->get()->groupBy(function($student) {
                    return $student->ojtInfo?->course ?? 'No Course';
                });

            case 'profile_completion':
                return $query->get()->map(function($student) {
                    $ojt = $student->ojtInfo;
                    return [
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'course' => $ojt?->course ?? 'Not Set',
                        'company' => $ojt?->company_name ?? 'Incomplete',
                        'supervisor' => $ojt?->supervisor_name ?? 'Not Assigned',
                        'status' => ($ojt?->student_number && $ojt?->company_name) ? 'Complete' : 'Incomplete',
                        'completion_date' => $ojt?->updated_at,
                    ];
                });

            case 'evaluation_summary':
                return $query->get()->map(function($student) {
                    $evaluations = $student->evaluationsReceived;
                    return [
                        'name' => $student->full_name,
                        'course' => $student->ojtInfo?->course ?? 'Not Set',
                        'evaluations_count' => $evaluations->count(),
                        'average_score' => $evaluations->count() > 0 
                            ? round($evaluations->avg('overall_performance'), 2)
                            : 'N/A',
                        'supervisor' => $evaluations->first()?->supervisor?->full_name ?? 'No Evaluations',
                    ];
                });

            case 'trainee_placement':
                return $query->get()->map(function($student) {
                    $ojt = $student->ojtInfo;
                    return [
                        'name' => $student->full_name,
                        'student_no' => $ojt?->student_number ?? 'Not Assigned',
                        'course' => $ojt?->course ?? 'Not Set',
                        'company' => $ojt?->company_name ?? 'Not Assigned',
                        'department' => $ojt?->department ?? 'N/A',
                        'supervisor' => $ojt?->supervisor_name ?? 'Not Assigned',
                        'start_date' => $ojt?->ojt_start_date,
                        'end_date' => $ojt?->ojt_end_date,
                    ];
                });

            default:
                return [];
        }
    }
}
