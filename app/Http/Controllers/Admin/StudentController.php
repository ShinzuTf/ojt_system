<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OjtInfo;
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
}
