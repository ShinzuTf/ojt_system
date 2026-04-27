<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OjtInfo;
use App\Models\PastOjtRecord;
use App\Services\OjtDeactivationService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->with(['ojtInfo'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.students', compact('students'));
    }

    public function show($id)
    {
        $student = User::where('role', 'student')
            ->with(['ojtInfo'])
            ->findOrFail($id);

        return view('admin.students', compact('student'));
    }

    public function store(Request $request)
    {
        // Admin manually adds a student account (just name + email, OJT details filled by student)
        $validated = $request->validate([
            'fname'  => 'required|string|max:100',
            'mname'  => 'nullable|string|max:100',
            'lname'  => 'required|string|max:100',
            'suffix' => 'nullable|string|max:10',
            'email'  => 'required|email|unique:users,email',
        ]);

        User::create(array_merge($validated, [
            'password' => bcrypt('philcst2024'), // default password
            'role'     => 'student',
            'status'   => 'active',
        ]));

        return back()->with('success', 'Student account created. Default password: philcst2024');
    }

    public function update(Request $request, $id)
    {
        $student = User::findOrFail($id);

        $validated = $request->validate([
            'fname'  => 'required|string|max:100',
            'mname'  => 'nullable|string|max:100',
            'lname'  => 'required|string|max:100',
            'suffix' => 'nullable|string|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        $student->update($validated);

        return back()->with('success', 'Student record updated successfully.');
    }

    /**
     * End of semester OJT deactivation
     * Deactivates all active OJT student accounts and archives their records
     */
    public function deactivateAllOjt(Request $request)
    {
        try {
            // Trigger the deactivation service
            $result = OjtDeactivationService::deactivateAndArchiveOjtRecords(
                adminId: auth()->id(),
                notes: $request->input('notes', null)
            );

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error during OJT deactivation: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate a single student OJT account
     * Used when a student re-enrolls for the next semester
     */
    public function reactivateStudent($id)
    {
        try {
            $student = User::findOrFail($id);

            $result = OjtDeactivationService::reactivateStudent($id);

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error reactivating student: ' . $e->getMessage());
        }
    }

    /**
     * View past OJT records for a student
     */
    public function viewPastOjtRecords($studentId)
    {
        $student = User::findOrFail($studentId);
        $pastRecords = PastOjtRecord::where('user_id', $studentId)
            ->orderBy('archived_at', 'desc')
            ->get();

        return view('admin.past-ojt-records', compact('student', 'pastRecords'));
    }

    /**
     * Get summary of deactivation impact (preview)
     * Shows how many students would be affected by end-of-semester deactivation
     */
    public function getDeactivationSummary()
    {
        $activeStudents = User::where('role', 'student')
            ->where('status', 'active')
            ->with('ojtInfo')
            ->get();

        $studentsWithOjt = $activeStudents->filter(fn($student) => $student->ojtInfo !== null);

        $summary = [
            'total_active_students' => $activeStudents->count(),
            'students_with_ojt_info' => $studentsWithOjt->count(),
            'students_to_deactivate' => $studentsWithOjt->count(),
            'details' => $studentsWithOjt->map(fn($student) => [
                'id' => $student->id,
                'name' => $student->full_name,
                'email' => $student->email,
                'company' => $student->ojtInfo?->company_name,
                'course' => $student->ojtInfo?->course,
            ])->values(),
        ];

        return response()->json($summary);
    }
}
