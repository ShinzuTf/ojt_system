<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OjtInfo;
use App\Services\CoordinatorMailService;
use App\Services\ActivityLogService;
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
        $adminId = auth()->id();
        $role = $request->input('role');
        
        Log::info('Account creation initiated', [
            'admin_id' => $adminId,
            'role' => $role,
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
        ]);
        
        // Check if trying to create an admin
        if ($role === 'admin') {
            Log::warning('Attempt to create admin account blocked', [
                'admin_id' => $adminId,
                'requested_email' => $request->input('email'),
            ]);
            return back()->with('error', 'Cannot create additional admin accounts. System allows only 1 admin.');
        }

        // === VALIDATION RULES ===
        $rules = [
            'fname' => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
            'lname' => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
            'email' => 'required|email|email:rfc|unique:users,email',
            'role' => 'required|in:student,coordinator',
            'password' => 'required|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ];

        if ($role === 'student') {
            $rules['course'] = 'required|in:BSIT,BSCS';
            Log::debug('Student account validation rules applied', ['admin_id' => $adminId]);
        } elseif ($role === 'coordinator') {
            $rules['company_email'] = 'required|email|exists:ojt_info,company_email';
            Log::debug('Coordinator account validation rules applied', ['admin_id' => $adminId]);
        }

        // === VALIDATE INPUT ===
        try {
            $validated = $request->validate($rules);
            Log::info('Input validation passed', [
                'admin_id' => $adminId,
                'role' => $role,
                'email' => $validated['email'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Input validation failed', [
                'admin_id' => $adminId,
                'role' => $role,
                'email' => $request->input('email'),
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // === COORDINATOR-SPECIFIC VALIDATION ===
        if ($role === 'coordinator') {
            $coordinatorValidation = $this->validateCoordinatorData($request, $adminId);
            if (!$coordinatorValidation['valid']) {
                Log::warning('Coordinator validation failed', [
                    'admin_id' => $adminId,
                    'reason' => $coordinatorValidation['message'],
                    'company_email' => $request->input('company_email'),
                ]);
                return back()->withErrors(['coordinator' => $coordinatorValidation['message']]);
            }
            Log::info('Coordinator-specific validation passed', [
                'admin_id' => $adminId,
                'company_email' => $request->input('company_email'),
                'company_name' => $coordinatorValidation['company_name'],
            ]);
        }

        // Store original password before hashing (for email sending)
        $originalPassword = $request->password;
        $username = $request->email;

        $userData = [
            'fname' => trim($request->fname),
            'lname' => trim($request->lname),
            'email' => strtolower($request->email),
            'role' => $role,
            'password' => bcrypt($request->password),
        ];

        // If coordinator, get company info from OjtInfo and set must_change_password
        if ($role === 'coordinator') {
            $company = OjtInfo::where('company_email', $request->company_email)
                ->first();
            if ($company) {
                $userData['company_name'] = $company->company_name;
                $userData['company_email'] = $company->company_email;
                Log::debug('Company data retrieved', [
                    'admin_id' => $adminId,
                    'company_id' => $company->id,
                    'company_name' => $company->company_name,
                ]);
            }
            $userData['must_change_password'] = true;
        }

        // === CREATE USER ===
        try {
            $user = User::create($userData);
            
            Log::info('Account created successfully', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'role' => $user->role,
                'email' => $user->email,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Account creation failed', [
                'admin_id' => $adminId,
                'role' => $role,
                'email' => $request->input('email'),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // === SEND WELCOME EMAIL FOR COORDINATOR ===
        if ($role === 'coordinator') {
            $coordinatorName = $request->fname . ' ' . $request->lname;
            try {
                Log::debug('Sending coordinator welcome email', [
                    'admin_id' => $adminId,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                CoordinatorMailService::sendWelcomeEmail(
                    $request->email,
                    $username,
                    $originalPassword,
                    $coordinatorName
                );
                
                Log::info('Coordinator welcome email sent successfully', [
                    'admin_id' => $adminId,
                    'user_id' => $user->id,
                    'recipient_email' => $request->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Coordinator welcome email failed', [
                    'admin_id' => $adminId,
                    'user_id' => $user->id,
                    'recipient_email' => $request->email,
                    'error_message' => $e->getMessage(),
                ]);
                // Continue anyway - don't block account creation
            }
            
            // Log activity: Coordinator created
            ActivityLogService::logCoordinatorCreation(
                $user->id,
                $user->email,
                $user->company_name,
                ['course' => $user->course ?? null]
            );
            
            return back()->with('success', 'Coordinator account created successfully!');
        }

        // Log activity: Student created
        ActivityLogService::logStudentCreation(
            $user->id,
            $user->email,
            $request->course
        );

        return back()->with('success', ucfirst($role) . ' account created successfully.');
    }

    /**
     * Validate coordinator-specific data
     *
     * @param Request $request
     * @param int $adminId
     * @return array
     */
    private function validateCoordinatorData(Request $request, $adminId): array
    {
        // Check if company email exists in ojt_info
        $company = OjtInfo::where('company_email', $request->company_email)->first();
        if (!$company) {
            return [
                'valid' => false,
                'message' => 'The selected company email does not exist in the system.',
            ];
        }

        // Check if a coordinator already exists for this company
        $existingCoordinator = User::where('role', 'coordinator')
            ->where('company_email', $request->company_email)
            ->first();
        if ($existingCoordinator) {
            Log::warning('Duplicate coordinator for company', [
                'admin_id' => $adminId,
                'company_email' => $request->company_email,
                'existing_coordinator_id' => $existingCoordinator->id,
                'existing_coordinator_email' => $existingCoordinator->email,
            ]);
            return [
                'valid' => false,
                'message' => 'A coordinator already exists for this company (' . $existingCoordinator->email . '). Please assign a different company or deactivate the existing coordinator first.',
            ];
        }

        // Validate company has valid data
        if (empty($company->company_name)) {
            return [
                'valid' => false,
                'message' => 'Company record is incomplete (missing company name).',
            ];
        }

        // Check email format matches standard corporate email patterns
        if (!filter_var($request->company_email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Invalid company email format.',
            ];
        }

        Log::debug('Coordinator validation checks passed', [
            'admin_id' => $adminId,
            'company_email' => $request->company_email,
            'company_name' => $company->company_name,
        ]);

        return [
            'valid' => true,
            'company_name' => $company->company_name,
        ];
    }

    public function update(Request $request, $id)
    {
        $adminId = auth()->id();
        $user = User::findOrFail($id);
        
        Log::info('Account update initiated', [
            'admin_id' => $adminId,
            'user_id' => $user->id,
            'role' => $user->role,
            'email' => $user->email,
        ]);
        
        $rules = [
            'fname' => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
            'lname' => 'required|string|max:100|regex:/^[a-zA-Z\s\-\']+$/',
            'email' => ['required', 'email', 'email:rfc', Rule::unique('users')->ignore($id)],
        ];

        if ($user->role === 'student') {
            $rules['course'] = 'required|in:BSIT,BSCS';
        } elseif ($user->role === 'coordinator') {
            $rules['company_email'] = 'required|email|exists:ojt_info,company_email';
        }

        try {
            $validated = $request->validate($rules);
            Log::info('Account update validation passed', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Account update validation failed', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // Additional validation for coordinator updates
        if ($user->role === 'coordinator' && $request->has('company_email')) {
            $coordinatorValidation = $this->validateCoordinatorUpdate($user->id, $request, $adminId);
            if (!$coordinatorValidation['valid']) {
                Log::warning('Coordinator update validation failed', [
                    'admin_id' => $adminId,
                    'user_id' => $user->id,
                    'reason' => $coordinatorValidation['message'],
                ]);
                return back()->withErrors(['coordinator' => $coordinatorValidation['message']]);
            }
        }

        $updateData = [
            'fname' => trim($request->fname),
            'lname' => trim($request->lname),
            'email' => strtolower($request->email),
        ];

        // If coordinator, update company info
        if ($user->role === 'coordinator' && $request->has('company_email')) {
            $company = OjtInfo::where('company_email', $request->company_email)->first();
            if ($company) {
                $updateData['company_name'] = $company->company_name;
                $updateData['company_email'] = $company->company_email;
                Log::debug('Coordinator company info updated', [
                    'admin_id' => $adminId,
                    'user_id' => $user->id,
                    'new_company_email' => $company->company_email,
                    'new_company_name' => $company->company_name,
                ]);
            }
        }

        try {
            $user->update($updateData);
            Log::info('Account updated successfully', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'role' => $user->role,
                'new_email' => $updateData['email'],
                'timestamp' => now(),
            ]);
            
            // Log activity based on user role
            if ($user->role === 'coordinator') {
                ActivityLogService::logCoordinatorUpdate(
                    $user->id,
                    $updateData['email'],
                    $updateData
                );
            } elseif ($user->role === 'student') {
                ActivityLogService::logStudentUpdate(
                    $user->id,
                    $updateData['email'],
                    $updateData
                );
            }
        } catch (\Exception $e) {
            Log::error('Account update failed', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return back()->with('success', 'User account updated successfully.');
    }

    /**
     * Validate coordinator-specific data during update
     *
     * @param int $coordinatorId
     * @param Request $request
     * @param int $adminId
     * @return array
     */
    private function validateCoordinatorUpdate($coordinatorId, Request $request, $adminId): array
    {
        // Check if company email exists in ojt_info
        $company = OjtInfo::where('company_email', $request->company_email)->first();
        if (!$company) {
            return [
                'valid' => false,
                'message' => 'The selected company email does not exist in the system.',
            ];
        }

        // Check if another coordinator already has this company assigned (excluding current coordinator)
        $existingCoordinator = User::where('role', 'coordinator')
            ->where('company_email', $request->company_email)
            ->where('id', '!=', $coordinatorId)
            ->first();
        if ($existingCoordinator) {
            Log::warning('Cannot reassign company to different coordinator', [
                'admin_id' => $adminId,
                'current_coordinator_id' => $coordinatorId,
                'existing_coordinator_id' => $existingCoordinator->id,
                'company_email' => $request->company_email,
            ]);
            return [
                'valid' => false,
                'message' => 'This company is already assigned to another coordinator (' . $existingCoordinator->email . ').',
            ];
        }

        return [
            'valid' => true,
            'company_name' => $company->company_name,
        ];
    }

    public function destroy($id)
    {
        $adminId = auth()->id();
        $user = User::findOrFail($id);
        
        Log::info('Account deactivation initiated', [
            'admin_id' => $adminId,
            'user_id' => $user->id,
            'role' => $user->role,
            'email' => $user->email,
        ]);
        
        // Prevent deleting if only admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            Log::warning('Attempt to deactivate only admin account blocked', [
                'admin_id' => $adminId,
                'target_user_id' => $user->id,
            ]);
            return back()->with('error', 'Cannot deactivate the only admin account.');
        }

        try {
            // Store user data before deletion for activity logging
            $userRole = $user->role;
            $userEmail = $user->email;
            $companyName = $user->company_name ?? null;
            $course = null;
            if ($user->role === 'student' && $user->ojtInfo) {
                $course = $user->ojtInfo->course ?? null;
            }
            
            $user->delete();
            Log::info('Account deactivated successfully', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'role' => $user->role,
                'email' => $user->email,
                'timestamp' => now(),
            ]);
            
            // Log activity based on user role
            if ($userRole === 'coordinator') {
                ActivityLogService::logCoordinatorDeactivation(
                    $id,
                    $userEmail,
                    $companyName
                );
            } elseif ($userRole === 'student') {
                ActivityLogService::logStudentDeactivation(
                    $id,
                    $userEmail,
                    $course
                );
            }
        } catch (\Exception $e) {
            Log::error('Account deactivation failed', [
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return back()->with('success', 'User account deactivated.');
    }
}
