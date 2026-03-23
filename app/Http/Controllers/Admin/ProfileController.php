<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin.profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function forceChangePassword(Request $request)
    {
        $action = $request->input('action', 'change');

        if ($action === 'keep') {
            // If coordinator wants to keep the current password, just clear the flag
            Auth::user()->update([
                'must_change_password' => false,
            ]);
            return back()->with('success', 'Welcome! You can now access the system.');
        }

        // action === 'change' or default
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Password changed successfully. You can now access the system.');
    }
}
