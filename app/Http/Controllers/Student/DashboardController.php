<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $ojt  = $user->ojtInfo;

        // Redirect to profile if critical info is missing
        if (!$ojt || !$ojt->student_number || !$ojt->company_name) {
            return redirect()->route('student.ojt-profile')
                ->with('warning', 'Please complete your OJT profile before accessing the dashboard.');
        }

        // Fetch document slots assigned by admin
        $requiredDocs = $user->requiredDocuments()
            ->with(['assignedBy'])
            ->get();

        // Attach latest submission to each slot
        foreach ($requiredDocs as $req) {
            $req->submission = \App\Models\Document::where('user_id', $user->id)
                ->where('required_document_id', $req->id)
                ->latest()
                ->first();
        }

        return view('student.dashboard', compact('requiredDocs'));
    }
}
