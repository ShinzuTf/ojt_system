<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\TemplateController;
use App\Http\Controllers\Student\NotificationController as StudentNotification;
use App\Http\Controllers\Student\OjtProfileController;
use App\Http\Controllers\Student\DocumentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\StudentController as AdminStudent;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\DocumentGeneratorController;
use App\Http\Controllers\TestDocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes — PHILCST CCS OJT System
|--------------------------------------------------------------------------
*/

// Smart redirect for root route
Route::get('/', function () {
    if (auth()->check()) {
        switch(auth()->user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'coordinator':
                return redirect()->route('supervisor.dashboard');
            default: // student
                return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
});

// ============================================================
// AUTH ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forced Password Change for newly created coordinator accounts
Route::post('/password/force-change', [ProfileController::class, 'forceChangePassword'])->name('password.force-change')->middleware('auth');

// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// ============================================================
// SUPERVISOR/COORDINATOR ROUTES
// ============================================================
Route::middleware(['auth', 'coordinator'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Supervisor\DashboardController::class, 'index'])->name('dashboard');
    
    // Evaluations of trainees' daily time records
    Route::get('/trainees', [\App\Http\Controllers\Supervisor\TraineeController::class, 'index'])->name('trainees');
    Route::get('/trainees/{id}', [\App\Http\Controllers\Supervisor\TraineeController::class, 'show'])->name('trainees.show');
    
    // Daily Time Record Evaluations
    Route::get('/evaluations', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'index'])->name('evaluations');
    Route::get('/evaluations/create/{trainee_id}', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{id}', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('/evaluations/{id}/edit', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::put('/evaluations/{id}', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'update'])->name('evaluations.update');
    Route::post('/evaluations/{id}/approve', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'approve'])->name('evaluations.approve');
    Route::delete('/evaluations/{id}', [\App\Http\Controllers\Supervisor\EvaluationController::class, 'destroy'])->name('evaluations.destroy');
    
    // Profile Settings
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [ProfileController::class, 'changePassword']);
});

// ============================================================
// DOCUMENT DOWNLOAD ROUTES
// ============================================================
Route::middleware(['auth'])->get('/documents/download', [DocumentGeneratorController::class, 'downloadDocument'])->name('document.download');

// ============================================================
// STUDENT ROUTES
// ============================================================
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    // OJT Profile — student fills in their OJT details
    Route::get('/ojt-profile', [OjtProfileController::class, 'index'])->name('ojt-profile');
    Route::put('/ojt-profile', [OjtProfileController::class, 'update'])->name('ojt-profile.update');

    // Template Generation (kept - generate documents for OJT system use)
    Route::get('/documents/templates', [DocumentGeneratorController::class, 'listTemplates'])->name('documents.templates');
    Route::post('/documents/generate', [DocumentGeneratorController::class, 'generateDocument'])->name('documents.generate');
    Route::post('/documents/generate-batch', [DocumentGeneratorController::class, 'generateBatch'])->name('documents.generate-batch');
    Route::get('/documents/preview-template', [DocumentGeneratorController::class, 'previewTemplate'])->name('documents.preview-template');
    Route::get('/templates/generate/{document_name}', [TemplateController::class, 'generate'])->where('document_name', '.*')->name('templates.generate');
    Route::get('/documents/download', [DocumentGeneratorController::class, 'downloadDocument'])->name('documents.download');

    // Document Submission
    Route::get('/documents/submit', [DocumentController::class, 'submit'])->name('documents.submit');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/history', [DocumentController::class, 'history'])->name('documents.history');

    // View Evaluations (trainee viewing own evaluations)
    Route::get('/evaluations', [\App\Http\Controllers\Student\EvaluationController::class, 'myEvaluations'])->name('evaluations');

    // Notifications
    Route::get('/notifications', [StudentNotification::class, 'index'])->name('notifications');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Students
    Route::get('/students', [AdminStudent::class, 'index'])->name('students');
    Route::get('/students/{id}', [AdminStudent::class, 'show'])->name('students.show');
    Route::post('/students', [AdminStudent::class, 'store'])->name('students.store');
    Route::put('/students/{id}', [AdminStudent::class, 'update'])->name('students.update');

    // Templates (View only - no manual assignment)
    Route::get('/templates', [\App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('templates');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // User Management (including supervisors/coordinators)
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/available-companies', [UserController::class, 'getAvailableCompanies'])->name('users.available-companies');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Activity Logs & Reporting
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/reports/logins', [ActivityLogController::class, 'loginReport'])->name('reports.logins');
    Route::get('/reports/documents', [ActivityLogController::class, 'documentReport'])->name('reports.documents');
    Route::get('/reports/evaluations', [ActivityLogController::class, 'evaluationReport'])->name('reports.evaluations');
    Route::get('/reports/user-management', [ActivityLogController::class, 'userManagementReport'])->name('reports.user-management');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

    // Profile Settings
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/change-password', [ProfileController::class, 'changePassword']);
});

// ============================================================
// TEST DOCUMENT GENERATION ROUTES
// ============================================================
Route::middleware(['auth'])->prefix('test')->name('test.')->group(function () {
    Route::get('/document', [TestDocumentController::class, 'showTestForm'])->name('document.form');
    Route::post('/document/generate', [TestDocumentController::class, 'generateTest'])->name('document.generate');
    Route::get('/document/preview', [TestDocumentController::class, 'previewData'])->name('document.preview');
});
