<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Log successful login
            ActivityLogService::logLogin($credentials['email'], true);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'coordinator') {
                return redirect()->route('supervisor.dashboard');
            }

            return redirect()->route('student.dashboard');
        }

        // Check if email exists to provide specific error
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            // Log failed login - email not found
            ActivityLogService::logLogin($credentials['email'], false);
            
            return back()->withErrors([
                'email' => 'Email address not found in our records.',
            ])->onlyInput('email');
        }
        
        // Log failed login - wrong password
        ActivityLogService::logLogin($credentials['email'], false);
        
        return back()->withErrors([
            'password' => 'The password you entered is incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log logout before clearing auth
        ActivityLogService::logLogout();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
