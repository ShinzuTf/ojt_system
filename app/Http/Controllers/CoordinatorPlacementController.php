<?php

namespace App\Http\Controllers;

use App\Models\OjtPlacement;
use App\Models\OjtInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoordinatorPlacementController extends Controller
{
    public function index()
    {
        // Get all students without active placements
        $students = User::where('role', 'student')
            ->with('ojtInfo')
            ->whereDoesntHave('placements', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('lname')
            ->orderBy('fname')
            ->paginate(20);
        
        return view('coordinator.placements.index', ['students' => $students]);
    }

    public function create()
    {
        $studentId = request('student_id');
        
        $companiesFromOjtInfo = OjtInfo::query()
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->pluck('company_name');

        $companiesFromSupervisors = User::query()
            ->where('role', 'supervisor')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->pluck('company_name');

        $companies = $companiesFromOjtInfo
            ->merge($companiesFromSupervisors)
            ->unique()
            ->sort()
            ->values();

        return view('coordinator.placements.create', [
            'companies' => $companies,
            'studentId' => $studentId,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isCoordinator(), 403);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'company_name' => 'required|string|max:255',
            'supervisor_fname' => 'required|string|max:100',
            'supervisor_lname' => 'required|string|max:100',
            'supervisor_email' => 'required|email|max:255|unique:users,email',
            'supervisor_password' => 'required|string|min:8',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'total_required_hours' => 'nullable|integer|min:100',
        ]);

        $supervisor = User::create([
            'fname' => trim($validated['supervisor_fname']),
            'lname' => trim($validated['supervisor_lname']),
            'email' => strtolower($validated['supervisor_email']),
            'password' => Hash::make($validated['supervisor_password']),
            'role' => 'supervisor',
            'status' => 'active',
            'company_name' => trim($validated['company_name']),
        ]);

        OjtPlacement::create([
            'student_id' => $validated['student_id'],
            'company_id' => $supervisor->id,
            'supervisor_id' => $supervisor->id,
            'coordinator_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_required_hours' => $validated['total_required_hours'] ?? 480,
            'status' => 'active',
        ]);

        OjtInfo::updateOrCreate(
            ['user_id' => $validated['student_id']],
            [
                'company_name' => $validated['company_name'],
                'supervisor_name' => trim($validated['supervisor_fname'] . ' ' . $validated['supervisor_lname']),
                'supervisor_contact' => strtolower($validated['supervisor_email']),
            ]
        );

        return redirect()->route('coordinator.placements.index')->with('success', 'Student company has been set successfully.');
    }

    public function show(OjtPlacement $placement)
    {
        return view('coordinator.placements.show', ['placement' => $placement]);
    }
}
