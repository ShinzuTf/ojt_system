<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $fileName = $this->getFileName($validated);

        if ($validated['export_format'] === 'csv') {
            return $this->exportCsv($reportData, $fileName, $validated['report_type']);
        } elseif ($validated['export_format'] === 'xlsx') {
            return $this->exportXlsx($reportData, $fileName, $validated['report_type']);
        } elseif ($validated['export_format'] === 'pdf') {
            return $this->exportPdf($reportData, $fileName, $validated['report_type']);
        }

        return back()->with('error', 'Invalid export format.');
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
                        'student_number' => $ojt?->student_number ?? 'Not Set',
                        'course' => $ojt?->course ?? 'Not Set',
                        'company' => $ojt?->company_name ?? 'Incomplete',
                        'supervisor' => $ojt?->supervisor_name ?? 'Not Assigned',
                        'status' => ($ojt?->student_number && $ojt?->company_name) ? 'Complete' : 'Incomplete',
                        'completion_date' => $ojt?->updated_at ? $ojt->updated_at->format('Y-m-d') : 'N/A',
                    ];
                });

            case 'evaluation_summary':
                return $query->get()->map(function($student) {
                    $evaluations = $student->evaluationsReceived;
                    return [
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'student_number' => $student->ojtInfo?->student_number ?? 'Not Set',
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
                        'email' => $student->email,
                        'student_no' => $ojt?->student_number ?? 'Not Assigned',
                        'course' => $ojt?->course ?? 'Not Set',
                        'company' => $ojt?->company_name ?? 'Not Assigned',
                        'department' => $ojt?->department ?? 'N/A',
                        'supervisor' => $ojt?->supervisor_name ?? 'Not Assigned',
                        'start_date' => $ojt?->ojt_start_date ? ($ojt->ojt_start_date instanceof \DateTime ? $ojt->ojt_start_date->format('Y-m-d') : $ojt->ojt_start_date) : 'N/A',
                        'end_date' => $ojt?->ojt_end_date ? ($ojt->ojt_end_date instanceof \DateTime ? $ojt->ojt_end_date->format('Y-m-d') : $ojt->ojt_end_date) : 'N/A',
                    ];
                });

            default:
                return [];
        }
    }

    private function getFileName($filters)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $reportType = $filters['report_type'];
        return "report_{$reportType}_{$timestamp}";
    }

    private function exportCsv($reportData, $fileName, $reportType)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}.csv\"",
        ];

        $callback = function() use ($reportData, $reportType) {
            $file = fopen('php://output', 'w');

            // Get headers from first item
            if ($reportType === 'trainees_by_course') {
                // Special handling for grouped data
                foreach ($reportData as $course => $students) {
                    fputcsv($file, ['Course: ' . $course]);
                    fputcsv($file, ['Name', 'Email', 'Student Number', 'Company', 'Supervisor']);
                    foreach ($students as $student) {
                        fputcsv($file, [
                            $student->full_name,
                            $student->email,
                            $student->ojtInfo?->student_number ?? 'Not Set',
                            $student->ojtInfo?->company_name ?? 'Not Assigned',
                            $student->ojtInfo?->supervisor_name ?? 'Not Assigned',
                        ]);
                    }
                    fputcsv($file, []); // blank line between courses
                }
            } else {
                if (count($reportData) > 0) {
                    $firstRow = $reportData[0];
                    fputcsv($file, array_keys($firstRow));
                    foreach ($reportData as $row) {
                        fputcsv($file, array_values($row));
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportXlsx($reportData, $fileName, $reportType)
    {
        // For now, export as CSV and notify user
        // If phpoffice/phpspreadsheet is installed, we can upgrade this
        return $this->exportCsv($reportData, $fileName, $reportType)->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')->header('Content-Disposition', "attachment; filename=\"{$fileName}.csv\"");
    }

    private function exportPdf($reportData, $fileName, $reportType)
    {
        $html = $this->generateHtmlReport($reportData, $reportType);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}.pdf\"",
        ];

        // Since we don't have a PDF library, generate as HTML that can be printed to PDF
        // Return HTML with print styles
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}.html\"",
        ]);
    }

    private function generateHtmlReport($reportData, $reportType)
    {
        $timestamp = now()->format('F j, Y \a\t g:i A');
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OJT System Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1 { color: #8b5cf6; text-align: center; margin-bottom: 30px; }
        h2 { color: #6b7280; margin-top: 25px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #8b5cf6; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .footer { text-align: center; color: #9ca3af; font-size: 12px; margin-top: 40px; }
    </style>
</head>
<body>
    <h1>OJT System Report</h1>
    <p style="text-align: center; color: #6b7280;">Generated on: {$timestamp}</p>
HTML;

        if ($reportType === 'trainees_by_course') {
            foreach ($reportData as $course => $students) {
                $html .= "<h2>Course: {$course}</h2>";
                $html .= '<table><thead><tr><th>Name</th><th>Email</th><th>Student Number</th><th>Company</th><th>Supervisor</th></tr></thead><tbody>';
                foreach ($students as $student) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($student->full_name) . '</td>';
                    $html .= '<td>' . htmlspecialchars($student->email) . '</td>';
                    $html .= '<td>' . htmlspecialchars($student->ojtInfo?->student_number ?? 'Not Set') . '</td>';
                    $html .= '<td>' . htmlspecialchars($student->ojtInfo?->company_name ?? 'Not Assigned') . '</td>';
                    $html .= '<td>' . htmlspecialchars($student->ojtInfo?->supervisor_name ?? 'Not Assigned') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
            }
        } else {
            if (count($reportData) > 0) {
                $firstRow = $reportData[0];
                $headers = array_keys($firstRow);
                $html .= '<table><thead><tr>';
                foreach ($headers as $header) {
                    $html .= '<th>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $header))) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                
                foreach ($reportData as $row) {
                    $html .= '<tr>';
                    foreach ($row as $value) {
                        $html .= '<td>' . htmlspecialchars($value) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
            }
        }

        $html .= <<<HTML2
    <div class="footer">
        <p>OJT Management System - Confidential</p>
    </div>
</body>
</html>
HTML2;

        return $html;
    }
}
