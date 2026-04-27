<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\CoordinatorDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StudentDtrController;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\StudentIssueController;
use App\Http\Controllers\SupervisorDtrController;
use App\Http\Controllers\SupervisorReportController;
use App\Http\Controllers\SupervisorIssueController;
use App\Http\Controllers\CoordinatorPlacementController;
use App\Http\Controllers\CoordinatorReportController;
use App\Http\Controllers\CoordinatorIssueController;
use App\Http\Controllers\CoordinatorCertificationController;
use App\Http\Controllers\CoordinatorTraineeController;
use App\Http\Controllers\CoordinatorSupervisorReportController;

// Student Routes - with proper /student prefix
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::resource('dtr', StudentDtrController::class)->names('dtr')->only(['index', 'create', 'store', 'show', 'update']);
    Route::resource('reports', StudentReportController::class)->names('reports')->only(['index', 'create', 'store', 'show']);
    Route::get('reports/{report}/download', [StudentReportController::class, 'download'])->name('reports.download');
    Route::resource('issues', StudentIssueController::class)->names('issues')->only(['index', 'create', 'store', 'show']);
    
    // Document generation
    Route::get('/documents/generate', [\App\Http\Controllers\StudentDocumentGeneratorController::class, 'index'])->name('documents.generate');
    Route::post('/documents/generate', [\App\Http\Controllers\StudentDocumentGeneratorController::class, 'generate'])->name('documents.generate.submit');
    
    // Evaluations route (view only)
    Route::get('/evaluations', function() { 
        return view('student.evaluations.index'); 
    })->name('evaluations.index');
});

// Supervisor Routes - with proper /supervisor prefix
Route::middleware(['auth'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::prefix('dtr')->name('dtr.')->group(function () {
        Route::get('/', [SupervisorDtrController::class, 'index'])->name('index');
        Route::post('/{dtr}/verify', [SupervisorDtrController::class, 'verify'])->name('verify');
        Route::post('/{dtr}/reject', [SupervisorDtrController::class, 'reject'])->name('reject');
        Route::get('/{dtr}', [SupervisorDtrController::class, 'show'])->name('show');
    });
    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [SupervisorReportController::class, 'index'])->name('index');
        Route::get('/{report}', [SupervisorReportController::class, 'show'])->name('show');
    });
    
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', [SupervisorIssueController::class, 'index'])->name('index');
        Route::get('/create', [SupervisorIssueController::class, 'create'])->name('create');
        Route::post('/', [SupervisorIssueController::class, 'store'])->name('store');
        Route::get('/{issue}', [SupervisorIssueController::class, 'show'])->name('show');
        Route::post('/{issue}/acknowledge', [SupervisorIssueController::class, 'acknowledge'])->name('acknowledge');
    });
    
    // Trainees
    Route::get('/trainees', function() { return view('supervisor.trainees.index'); })->name('trainees.index');
    Route::get('/trainees/{id}', function() { return view('supervisor.trainees.show'); })->name('trainees.show');
});

// Coordinator Routes - with proper /coordinator prefix
Route::middleware(['auth'])->prefix('coordinator')->name('coordinator.')->group(function () {
    Route::resource('placements', CoordinatorPlacementController::class)->names('placements')->only(['index', 'create', 'store', 'show']);
    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [CoordinatorReportController::class, 'index'])->name('index');
        Route::get('/{report}', [CoordinatorReportController::class, 'show'])->name('show');
        Route::post('/{report}/approve', [CoordinatorReportController::class, 'approve'])->name('approve');
        Route::post('/{report}/reject', [CoordinatorReportController::class, 'reject'])->name('reject');
    });

    Route::get('/supervisor-reports', [CoordinatorSupervisorReportController::class, 'index'])->name('supervisor-reports.index');
    
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', [CoordinatorIssueController::class, 'index'])->name('index');
        Route::get('/{issue}', [CoordinatorIssueController::class, 'show'])->name('show');
        Route::put('/{issue}', [CoordinatorIssueController::class, 'update'])->name('update');
    });
    
    Route::prefix('certifications')->name('certifications.')->group(function () {
        Route::get('/', [CoordinatorCertificationController::class, 'index'])->name('index');
        Route::get('/{certification}', [CoordinatorCertificationController::class, 'show'])->name('show');
        Route::post('/{certification}/approve', [CoordinatorCertificationController::class, 'approve'])->name('approve');
        Route::post('/{certification}/reject', [CoordinatorCertificationController::class, 'reject'])->name('reject');
    });
    
    Route::prefix('trainees')->name('trainees.')->group(function () {
        Route::get('/', [CoordinatorTraineeController::class, 'index'])->name('index');
        Route::get('/{trainee}', [CoordinatorTraineeController::class, 'show'])->name('show');
    });
});
