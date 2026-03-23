<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OjtInfo;
use App\Services\CoordinatorMailService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->with('ojtInfo')->latest()->get();
        $coordinators = User::where('role', 'coordinator')->latest()->get();
        
        return view('admin.users', compact('students', 'coordinators'));
    }

    /**
     * Get available company emails for coordinator assignment
     */
    public function getAvailableCompanies()
    {
        $companies = OjtInfo::where('company_email', '!=', null)
            ->select('company_name', 'company_email')
            ->distinct()
            ->orderBy('company_name')
            ->get()
            ->map(fn($ojt) => [
                'company_name' => $ojt->company_name,
                'company_email' => $ojt->company_email,
            ]);

        return response()->json($companies);
    }

    public function store(Request $request)
    {
        Log::info('User creation attempt', ['data' => $request->all()]);
        
        // Check if trying to create an admin
        if ($request->input('role') === 'admin') {
            return back()->with('error', 'Cannot create additional admin accounts. System allows only 1 admin.');
        }

        $rules = [
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:student,coordinator',
            'password' => 'required|min:8|confirmed',
        ];

        if ($request->input('role') === 'student') {
            $rules['course'] = 'required|in:BSIT,BSCS';
        } elseif ($request->input('role') === 'coordinator') {
            $rules['company_email'] = 'required|exists:ojt_info,company_email';
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        // Store original password before hashing (for email sending)
        $originalPassword = $request->password;
        $username = $request->email;

        $userData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ];

        // If coordinator, get company info from OjtInfo and set must_change_password
        if ($request->role === 'coordinator') {
            $company = OjtInfo::where('company_email', $request->company_email)
                ->first();
            if ($company) {
                $userData['company_name'] = $company->company_name;
                $userData['company_email'] = $company->company_email;
            }
            $userData['must_change_password'] = true;
        }

        try {
            $user = User::create($userData);
            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }

        // Send welcome email to coordinator with credentials (async, non-blocking)
        if ($request->role === 'coordinator') {
            $coordinatorName = $request->fname . ' ' . $request->lname;
            try {
                CoordinatorMailService::sendWelcomeEmail(
                    $request->email,
                    $username,
                    $originalPassword,
                    $coordinatorName
                );
            } catch (\Exception $e) {
                Log::error('Coordinator welcome email failed: ' . $e->getMessage());
                // Continue anyway - don't block account creation
            }
            return back()->with('success', 'Coordinator account created successfully!');
        }

        return back()->with('success', ucfirst($request->role) . ' account created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $rules = [
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
        ];

        if ($user->role === 'student') {
            $rules['course'] = 'required|in:BSIT,BSCS';
        } elseif ($user->role === 'coordinator') {
            $rules['company_email'] = 'required|exists:ojt_info,company_email';
        }

        $request->validate($rules);

        $updateData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
        ];

        // If coordinator, update company info
        if ($user->role === 'coordinator' && $request->has('company_email')) {
            $company = OjtInfo::where('company_email', $request->company_email)->first();
            if ($company) {
                $updateData['company_name'] = $company->company_name;
                $updateData['company_email'] = $company->company_email;
            }
        }

        $user->update($updateData);

        return back()->with('success', 'User account updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting if only admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return back()->with('error', 'Cannot deactivate the only admin account.');
        }

        $user->delete();
        return back()->with('success', 'User account deactivated.');
    }
}
