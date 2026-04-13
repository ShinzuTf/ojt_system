<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log a user activity
     *
     * @param string $activity Activity key (e.g., 'user_login', 'document_generated')
     * @param string $module Module name (e.g., 'auth', 'document', 'evaluation')
     * @param string $action Action performed (e.g., 'login', 'create', 'update')
     * @param string|null $description Human-readable description
     * @param array $data Additional data to store
     * @param int|null $targetUserId ID of the user being acted upon
     * @param string $status Status of the action (success, failed, pending)
     *
     * @return ActivityLog|null
     */
    public static function log(
        string $activity,
        string $module,
        string $action,
        ?string $description = null,
        array $data = [],
        ?int $targetUserId = null,
        string $status = 'success'
    ): ?ActivityLog {
        try {
            $userId = Auth::id();

            // Don't log if no authenticated user
            if (!$userId) {
                return null;
            }

            $log = ActivityLog::create([
                'user_id' => $userId,
                'target_user_id' => $targetUserId,
                'activity' => $activity,
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'data' => !empty($data) ? $data : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
                'status' => $status,
            ]);

            return $log;
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage(), [
                'activity' => $activity,
                'exception' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Log user login
     */
    public static function logLogin(string $email, bool $success = true): ?ActivityLog
    {
        $user = \App\Models\User::where('email', $email)->first();

        return self::log(
            activity: 'user_login',
            module: 'auth',
            action: 'login',
            description: $success ? "User {$email} logged in successfully" : "Login attempt failed for {$email}",
            data: [
                'email' => $email,
                'user_role' => $user?->role,
            ],
            targetUserId: $user?->id,
            status: $success ? 'success' : 'failed'
        );
    }

    /**
     * Log user logout
     */
    public static function logLogout(): ?ActivityLog
    {
        $user = Auth::user();

        return self::log(
            activity: 'user_logout',
            module: 'auth',
            action: 'logout',
            description: "User {$user->email} logged out",
            targetUserId: $user->id
        );
    }

    /**
     * Log coordinator creation
     */
    public static function logCoordinatorCreation(
        int $coordinatorId,
        string $email,
        string $companyName,
        array $additionalData = []
    ): ?ActivityLog {
        $data = array_merge([
            'coordinator_id' => $coordinatorId,
            'email' => $email,
            'company_name' => $companyName,
        ], $additionalData);

        return self::log(
            activity: 'coordinator_created',
            module: 'admin',
            action: 'create',
            description: "Coordinator account created for {$email} ({$companyName})",
            data: $data,
            targetUserId: $coordinatorId
        );
    }

    /**
     * Log coordinator update
     */
    public static function logCoordinatorUpdate(
        int $coordinatorId,
        string $email,
        array $changes = []
    ): ?ActivityLog {
        return self::log(
            activity: 'coordinator_updated',
            module: 'admin',
            action: 'update',
            description: "Coordinator account updated: {$email}",
            data: [
                'coordinator_id' => $coordinatorId,
                'email' => $email,
                'changes' => $changes,
            ],
            targetUserId: $coordinatorId
        );
    }

    /**
     * Log coordinator deactivation
     */
    public static function logCoordinatorDeactivation(
        int $coordinatorId,
        string $email,
        string $companyName
    ): ?ActivityLog {
        return self::log(
            activity: 'coordinator_deactivated',
            module: 'admin',
            action: 'delete',
            description: "Coordinator account deactivated: {$email} ({$companyName})",
            data: [
                'coordinator_id' => $coordinatorId,
                'email' => $email,
                'company_name' => $companyName,
            ],
            targetUserId: $coordinatorId
        );
    }

    /**
     * Log student creation
     */
    public static function logStudentCreation(
        int $studentId,
        string $email,
        string $course,
        array $additionalData = []
    ): ?ActivityLog {
        $data = array_merge([
            'student_id' => $studentId,
            'email' => $email,
            'course' => $course,
        ], $additionalData);

        return self::log(
            activity: 'student_created',
            module: 'admin',
            action: 'create',
            description: "Student account created: {$email} ({$course})",
            data: $data,
            targetUserId: $studentId
        );
    }

    /**
     * Log student update
     */
    public static function logStudentUpdate(
        int $studentId,
        string $email,
        array $changes = []
    ): ?ActivityLog {
        return self::log(
            activity: 'student_updated',
            module: 'admin',
            action: 'update',
            description: "Student account updated: {$email}",
            data: [
                'student_id' => $studentId,
                'email' => $email,
                'changes' => $changes,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log student deactivation
     */
    public static function logStudentDeactivation(
        int $studentId,
        string $email,
        string $course
    ): ?ActivityLog {
        return self::log(
            activity: 'student_deactivated',
            module: 'admin',
            action: 'delete',
            description: "Student account deactivated: {$email} ({$course})",
            data: [
                'student_id' => $studentId,
                'email' => $email,
                'course' => $course,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log document generation
     */
    public static function logDocumentGeneration(
        int $documentId,
        string $documentType,
        int $studentId,
        array $additionalData = []
    ): ?ActivityLog {
        $data = array_merge([
            'document_id' => $documentId,
            'document_type' => $documentType,
            'student_id' => $studentId,
        ], $additionalData);

        return self::log(
            activity: 'document_generated',
            module: 'document',
            action: 'create',
            description: "Document generated: {$documentType}",
            data: $data,
            targetUserId: $studentId
        );
    }

    /**
     * Log document submission
     */
    public static function logDocumentSubmission(
        int $documentId,
        string $documentType,
        int $studentId
    ): ?ActivityLog {
        return self::log(
            activity: 'document_submitted',
            module: 'document',
            action: 'submit',
            description: "Document submitted: {$documentType}",
            data: [
                'document_id' => $documentId,
                'document_type' => $documentType,
                'student_id' => $studentId,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log document approval
     */
    public static function logDocumentApproval(
        int $documentId,
        string $documentType,
        int $studentId,
        string $approverRole
    ): ?ActivityLog {
        return self::log(
            activity: 'document_approved',
            module: 'document',
            action: 'approve',
            description: "{$approverRole} approved document: {$documentType}",
            data: [
                'document_id' => $documentId,
                'document_type' => $documentType,
                'student_id' => $studentId,
                'approver_role' => $approverRole,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log document rejection
     */
    public static function logDocumentRejection(
        int $documentId,
        string $documentType,
        int $studentId,
        string $reason
    ): ?ActivityLog {
        return self::log(
            activity: 'document_rejected',
            module: 'document',
            action: 'reject',
            description: "Document rejected: {$documentType} - Reason: {$reason}",
            data: [
                'document_id' => $documentId,
                'document_type' => $documentType,
                'student_id' => $studentId,
                'rejection_reason' => $reason,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log evaluation creation
     */
    public static function logEvaluationCreation(
        int $evaluationId,
        int $studentId,
        int $supervisorId,
        array $scores
    ): ?ActivityLog {
        return self::log(
            activity: 'evaluation_created',
            module: 'evaluation',
            action: 'create',
            description: "Evaluation created for student",
            data: [
                'evaluation_id' => $evaluationId,
                'student_id' => $studentId,
                'supervisor_id' => $supervisorId,
                'scores' => $scores,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log evaluation submission
     */
    public static function logEvaluationSubmission(
        int $evaluationId,
        int $studentId,
        int $supervisorId
    ): ?ActivityLog {
        return self::log(
            activity: 'evaluation_submitted',
            module: 'evaluation',
            action: 'submit',
            description: "Evaluation submitted for student",
            data: [
                'evaluation_id' => $evaluationId,
                'student_id' => $studentId,
                'supervisor_id' => $supervisorId,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log evaluation update
     */
    public static function logEvaluationUpdate(
        int $evaluationId,
        int $studentId,
        array $changes
    ): ?ActivityLog {
        return self::log(
            activity: 'evaluation_updated',
            module: 'evaluation',
            action: 'update',
            description: "Evaluation updated for student",
            data: [
                'evaluation_id' => $evaluationId,
                'student_id' => $studentId,
                'changes' => $changes,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log OJT information update
     */
    public static function logOjtInfoUpdate(
        int $studentId,
        array $changes
    ): ?ActivityLog {
        return self::log(
            activity: 'ojt_info_updated',
            module: 'ojt',
            action: 'update',
            description: "OJT information updated",
            data: [
                'student_id' => $studentId,
                'changes' => $changes,
            ],
            targetUserId: $studentId
        );
    }

    /**
     * Log template update
     */
    public static function logTemplateUpdate(
        int $templateId,
        string $templateName,
        array $changes
    ): ?ActivityLog {
        return self::log(
            activity: 'template_updated',
            module: 'template',
            action: 'update',
            description: "Template updated: {$templateName}",
            data: [
                'template_id' => $templateId,
                'template_name' => $templateName,
                'changes' => $changes,
            ]
        );
    }
}
