<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyTimeRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\OjtPlacementController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Daily Time Record Routes
    Route::prefix('dtr')->group(function () {
        Route::get('/', [DailyTimeRecordController::class, 'index']);
        Route::post('/', [DailyTimeRecordController::class, 'store']);
        Route::patch('/{dtr}', [DailyTimeRecordController::class, 'update']);
        Route::get('/summary/{studentId}', [DailyTimeRecordController::class, 'summary']);
        
        // Supervisor routes
        Route::prefix('supervisor')->middleware('role:supervisor')->group(function () {
            Route::get('/pending', [DailyTimeRecordController::class, 'supervisorPending']);
            Route::patch('/{dtr}/verify', [DailyTimeRecordController::class, 'verify']);
            Route::patch('/{dtr}/reject', [DailyTimeRecordController::class, 'reject']);
            Route::get('/corrections', [DailyTimeRecordController::class, 'supervisorCorrections']);
            Route::patch('/corrections/{correction}/approve', [DailyTimeRecordController::class, 'approveCorrection']);
            Route::patch('/corrections/{correction}/reject', [DailyTimeRecordController::class, 'rejectCorrection']);
        });
        
        // Student routes
        Route::prefix('student')->middleware('role:student')->group(function () {
            Route::post('/{dtr}/request-correction', [DailyTimeRecordController::class, 'requestCorrection']);
        });
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'store']);
        Route::patch('/{report}', [ReportController::class, 'update']);
        Route::delete('/{report}', [ReportController::class, 'destroy']);
        Route::get('/{report}/history', [ReportController::class, 'history']);
        
        // Submit for review
        Route::post('/{report}/submit', [ReportController::class, 'submit'])->middleware('role:student');
        
        // Review actions
        Route::patch('/{report}/approve', [ReportController::class, 'approve'])->middleware('role:supervisor,coordinator');
        Route::patch('/{report}/reject', [ReportController::class, 'reject'])->middleware('role:supervisor,coordinator');
        
        // Escalate
        Route::post('/{report}/escalate', [ReportController::class, 'escalate'])->middleware('role:supervisor');
    });

    // Issue Routes
    Route::prefix('issues')->group(function () {
        Route::get('/', [IssueController::class, 'index']);
        Route::post('/', [IssueController::class, 'store']);
        Route::get('/{issue}', [IssueController::class, 'show']);
        Route::get('/{issue}/updates', [IssueController::class, 'updates']);
        
        // Coordinator actions
        Route::patch('/{issue}/acknowledge', [IssueController::class, 'acknowledge'])->middleware('role:coordinator');
        Route::patch('/{issue}/resolve', [IssueController::class, 'resolve'])->middleware('role:coordinator');
        Route::patch('/{issue}/mark-dropped', [IssueController::class, 'markDropped'])->middleware('role:coordinator');
        Route::patch('/{issue}/mark-transferred', [IssueController::class, 'markTransferred'])->middleware('role:coordinator');
    });

    // OJT Placement Routes
    Route::prefix('placements')->group(function () {
        Route::get('/', [OjtPlacementController::class, 'index']);
        Route::post('/', [OjtPlacementController::class, 'store'])->middleware('role:coordinator');
        Route::get('/{placement}', [OjtPlacementController::class, 'show']);
        Route::get('/{placement}/progress', [OjtPlacementController::class, 'progress']);
        
        // Certification routes
        Route::get('/{placement}/certifications', [OjtPlacementController::class, 'certifications']);
        Route::post('/{placement}/certifications', [OjtPlacementController::class, 'createCertification'])->middleware('role:supervisor');
        Route::patch('/certifications/{certification}/verify', [OjtPlacementController::class, 'verifyCertification'])->middleware('role:coordinator');
        Route::patch('/certifications/{certification}/approve', [OjtPlacementController::class, 'approveCertification'])->middleware('role:coordinator');
        
        // Completion routes
        Route::get('/{placement}/completion', [OjtPlacementController::class, 'completionRecord']);
        Route::post('/{placement}/mark-completed', [OjtPlacementController::class, 'markCompleted'])->middleware('role:coordinator');
        Route::patch('/completion/{record}/approve', [OjtPlacementController::class, 'approveCompletion'])->middleware('role:coordinator');
    });
});
