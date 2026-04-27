<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoordinatorIssueController extends Controller
{
    public function index()
    {
        $status = request('status');
        $issues = Issue::query();
        
        if ($status) {
            $issues->where('status', $status);
        }
        
        $issues = $issues->with('student', 'assignedTo')->paginate(20);
        
        return view('coordinator.issues.index', ['issues' => $issues]);
    }

    public function show(Issue $issue)
    {
        return view('coordinator.issues.show', ['issue' => $issue]);
    }

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string|min:10',
            'student_status' => 'required|in:active,dropped,transferred',
            'transfer_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($validated['student_status'] === 'transferred' && ! $request->hasFile('transfer_certificate')) {
            return back()
                ->withErrors(['transfer_certificate' => 'A transfer certificate is required when the student is marked as transferred.'])
                ->withInput();
        }

        $certificatePath = $issue->transfer_certificate_path;
        $certificateName = $issue->transfer_certificate_name;

        if ($request->hasFile('transfer_certificate')) {
            if ($certificatePath) {
                Storage::disk('public')->delete($certificatePath);
            }

            $uploadedFile = $request->file('transfer_certificate');
            $certificatePath = $uploadedFile->store('issue-certificates', 'public');
            $certificateName = $uploadedFile->getClientOriginalName();
        }

        $issue->update([
            'status' => 'resolved',
            'resolution_notes' => $validated['resolution_notes'],
            'resolution_date' => now(),
            'student_status' => $validated['student_status'],
            'transfer_certificate_path' => $certificatePath,
            'transfer_certificate_name' => $certificateName,
        ]);

        return back()->with('success', 'Issue resolved successfully.');
    }
}
