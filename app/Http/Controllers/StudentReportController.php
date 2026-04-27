<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reports = $user->submittedReports()->paginate(15);
        
        return view('student.reports.index', ['reports' => $reports]);
    }

    public function create()
    {
        return view('student.reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:weekly,monthly',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after:report_period_start',
            'file_path' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png',
        ], [
            'file_path.required' => 'Please upload a report document.',
            'file_path.max' => 'File size must not exceed 10MB.',
            'file_path.mimes' => 'File must be a valid document or image format.',
        ]);

        $user = Auth::user();
        
        // Store the file
        $filePath = null;
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('reports/' . $user->id, $fileName, 'public');
        }

        $report = Report::create([
            'submitted_by' => $user->id,
            'report_type' => $validated['report_type'],
            'report_period_start' => $validated['report_period_start'],
            'report_period_end' => $validated['report_period_end'],
            'file_path' => $filePath,
            'file_type' => $request->file('file_path')->getClientOriginalExtension(),
            'status' => 'submitted',
        ]);

        return redirect()->route('student.reports.index')->with('success', 'Report submitted successfully! Awaiting supervisor review.');
    }

    public function show(Report $report)
    {
        if ($report->submitted_by !== Auth::id()) {
            abort(403);
        }

        return view('student.reports.show', ['report' => $report]);
    }

    public function download(Report $report)
    {
        if ($report->submitted_by !== Auth::id()) {
            abort(403);
        }

        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            return redirect()->route('student.reports.index')->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($report->file_path, basename($report->file_path));
    }
}
