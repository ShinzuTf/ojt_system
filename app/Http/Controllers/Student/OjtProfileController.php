<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OjtInfo;
use App\Services\TemplateAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OjtProfileController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $ojt     = $user->ojtInfo ?? new OjtInfo();
        return view('student.ojt_profile', compact('user', 'ojt'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'student_number'     => 'required|string|max:30',
            'course'             => 'required|in:BSIT,BSCS',
            'year_level'         => 'required|integer|in:3,4',
            'company_name'       => 'required|string|max:255',
            'company_email'      => 'required|email|max:255',
            'company_address'    => 'nullable|string|max:500',
            'supervisor_name'    => 'nullable|string|max:200',
            'supervisor_contact' => 'nullable|string|max:100',
            'ojt_start'          => 'nullable|date',
            'ojt_end'            => 'nullable|date|after_or_equal:ojt_start',
        ]);

        OjtInfo::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($validated, ['user_id' => $user->id])
        );

        // Auto-assign required templates when user completes their profile
        TemplateAssignmentService::assignRequiredTemplatesForStudent($user);

        return back()->with('success', 'OJT profile updated successfully! Required templates are now available on your dashboard.');
    }
}
