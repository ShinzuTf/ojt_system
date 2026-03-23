<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'fname'                 => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
                'mname'                 => 'nullable|string|max:100|regex:/^[a-zA-Z\s\-\']*$/',
                'lname'                 => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
                'suffix'                => 'nullable|string|max:10|regex:/^[a-zA-Z\.]*$/',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|string|min:8|confirmed',
            ],
            [
                'fname.regex'           => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
                'mname.regex'           => 'Middle name can only contain letters, spaces, hyphens, and apostrophes.',
                'lname.regex'           => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
                'suffix.regex'          => 'Suffix can only contain letters and periods.',
            ]
        );

        $user = User::create([
            'fname'    => $validated['fname'],
            'mname'    => $validated['mname'] ?? null,
            'lname'    => $validated['lname'],
            'suffix'   => $validated['suffix'] ?? null,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
            'status'   => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }
}
