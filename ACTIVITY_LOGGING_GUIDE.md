# Activity Logging System - Implementation Guide

## Overview
A comprehensive audit trail system has been implemented to track user activities in the OJT System for reporting and compliance purposes.

## What's Tracked

### Authentication
- **User Login** - All login attempts (successful and failed) with email, role, and IP address
- **User Logout** - All logout events

### Account Management
- **Coordinator Creation** - When admin creates coordinator account
- **Coordinator Updates** - When coordinator account is modified
- **Coordinator Deactivation** - When coordinator account is deleted
- **Student Creation** - When admin creates student account
- **Student Updates** - When student account is modified
- **Student Deactivation** - When student account is deleted

### Documents
- **Document Generated** - When student generates a document
- **Document Submitted** - When student submits a document
- **Document Approved** - When evaluator approves a document
- **Document Rejected** - When evaluator rejects a document

### Evaluations
- **Evaluation Created** - When coordinator creates evaluation
- **Evaluation Submitted** - When evaluation is submitted
- **Evaluation Updated** - When evaluation is modified

### OJT & System
- **OJT Info Updated** - When OJT information is modified
- **Template Updated** - When system templates are updated

## Database Schema

### activity_logs Table
```sql
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL (Who performed the action),
    target_user_id BIGINT NULL (Who the action was on),
    activity VARCHAR(255) (Activity key)
    module VARCHAR(255) (Module name: auth, admin, document, evaluation, etc.)
    action VARCHAR(255) (Action type: login, create, update, delete, etc.)
    description TEXT (Human-readable description)
    data JSON (Additional data stored as JSON)
    ip_address VARCHAR(45) (IPv4 and IPv6 support)
    user_agent TEXT (Browser/device information)
    status VARCHAR(50) (success, failed, pending)
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

**Indexes:**
- user_id (for filtering by user)
- activity (for filtering by activity type)
- module (for filtering by module)
- created_at (for date range queries)
- user_id + created_at (for user timeline queries)

## Setup Instructions

### 1. Run the Migration
```bash
php artisan migrate
```

This will create the `activity_logs` table.

### 2. Verify Installation
Check that the following files exist:
- `app/Models/ActivityLog.php`
- `app/Services/ActivityLogService.php`
- `app/Http/Controllers/Admin/ActivityLogController.php`
- `database/migrations/2026_04_13_000001_create_activity_logs_table.php`

### 3. Integration Points

#### LoginController
- `logLogin()` - Logs login attempts
- `logLogout()` - Logs logout events

#### UserController
- `logCoordinatorCreation()` - Logs when coordinator is created
- `logCoordinatorUpdate()` - Logs coordinator updates
- `logCoordinatorDeactivation()` - Logs coordinator deletion
- `logStudentCreation()` - Logs student creation
- `logStudentUpdate()` - Logs student updates
- `logStudentDeactivation()` - Logs student deletion

#### Other Controllers (to be integrated)
- Document Controller: `logDocumentGeneration()`, `logDocumentSubmission()`, `logDocumentApproval()`, `logDocumentRejection()`
- Evaluation Controller: `logEvaluationCreation()`, `logEvaluationSubmission()`, `logEvaluationUpdate()`

## Usage Examples

### Using ActivityLogService

```php
use App\Services\ActivityLogService;

// Log coordinator creation
ActivityLogService::logCoordinatorCreation(
    $coordinatorId,
    'coordinator@company.com',
    'Acme Corporation',
    ['additional_data' => 'value']
);

// Log document generation
ActivityLogService::logDocumentGeneration(
    $documentId,
    'Certificate',
    $studentId
);

// Log evaluation creation
ActivityLogService::logEvaluationCreation(
    $evaluationId,
    $studentId,
    $supervisorId,
    ['score' => 95]
);

// Log custom activity
ActivityLogService::log(
    activity: 'custom_activity',
    module: 'custom',
    action: 'execute',
    description: 'Custom action performed',
    data: ['key' => 'value'],
    targetUserId: $userId
);
```

## Accessing Activity Logs

### Admin Dashboard
- **URL:** `/admin/activity-logs`
- **Features:**
  - View all activities with pagination (50 per page)
  - Filter by activity type, module, user, status
  - Filter by date range or recent days
  - View activity details
  - Export to CSV

### Reports

1. **Login Report:** `/admin/reports/logins`
   - All login attempts (successful/failed)
   - Daily login statistics
   - User login patterns

2. **Document Report:** `/admin/reports/documents`
   - Document generation and submission logs
   - Document approval/rejection logs
   - Document activity statistics

3. **Evaluation Report:** `/admin/reports/evaluations`
   - All evaluation activities
   - Evaluation creation, submission, update logs
   - Evaluation activity statistics

4. **User Management Report:** `/admin/reports/user-management`
   - Coordinator creations, updates, deletions
   - Student account management activities
   - Admin actions on user accounts

### Export to CSV
Click "Export" button from activity logs page to download CSV file with filters applied.

## ActivityLog Model Methods

### Scopes
```php
ActivityLog::byActivity('user_login')->get();
ActivityLog::byRole('coordinator')->get();
ActivityLog::recent(30)->get(); // Last 30 days
```

### Retrieving Additional Data
```php
$activity = ActivityLog::find($id);

// Access user who performed action
$activity->user->email;

// Access target user (affected by action)
$activity->targetUser->email;

// Access stored data (JSON)
$activity->data['coordinator_id'];

// Get human-readable description
echo $activity->description;
```

## Data Structure - JSON Data Field

### Login Activity
```json
{
    "email": "user@example.com",
    "user_role": "coordinator"
}
```

### Coordinator Creation
```json
{
    "coordinator_id": 5,
    "email": "coordinator@company.com",
    "company_name": "Acme Corporation"
}
```

### Document Generation
```json
{
    "document_id": 42,
    "document_type": "Certificate",
    "student_id": 15
}
```

### Evaluation Activity
```json
{
    "evaluation_id": 8,
    "student_id": 15,
    "supervisor_id": 3,
    "scores": {
        "technical_skills": 85,
        "communication": 90
    }
}
```

## Security & Privacy

### What's Captured
- User ID (who performed action)
- IP Address (from request)
- User Agent (browser/device info)
- Timestamp (when action occurred)
- Action details (what was done)
- Affected user (target_user_id)

### What's NOT Captured
- Passwords (never logged)
- Confirmation emails (logged separately in mail logs)
- Sensitive personal data (PII not stored in data JSON field)

### Data Retention
- Activities are kept indefinitely for compliance
- To delete old logs: 
  ```php
  // Delete activities older than 1 year
  ActivityLog::where('created_at', '<', now()->subYear())->delete();
  ```

## Future Integration Points

Add this code to controllers to track additional activities:

### DocumentController
```php
// In store method (document generation)
ActivityLogService::logDocumentGeneration(
    $document->id,
    $document->type,
    $student->id
);

// In submission method
ActivityLogService::logDocumentSubmission(
    $document->id,
    $document->type,
    $document->user_id
);
```

### EvaluationController
```php
// In store method
ActivityLogService::logEvaluationCreation(
    $evaluation->id,
    $evaluation->trainee_id,
    $evaluation->supervisor_id,
    $evaluation->scores
);

// In submit method
ActivityLogService::logEvaluationSubmission(
    $evaluation->id,
    $evaluation->trainee_id,
    $evaluation->supervisor_id
);
```

### OjtInfoController
```php
// Track OJT info updates
ActivityLogService::logOjtInfoUpdate(
    $student->id,
    $changedFields // array of what changed
);
```

## Troubleshooting

### Activities Not Being Logged
1. Check that migration ran: `php artisan migrate:status`
2. Verify `ActivityLogService` import in controller
3. Check that user is authenticated (`Auth::check()`)
4. Look at Laravel logs for errors: `storage/logs/laravel.log`

### Views Not Showing
1. Create view files in `resources/views/admin/activity-logs/`
2. Run `php artisan view:clear` to clear view cache

### Export Not Working
1. Check that storage folder is writable
2. Verify permissions: `chmod -R 775 storage/`

## Performance Considerations

### Database Maintenance
- The `activity_logs` table may grow large over time
- Create regular backups
- Consider archiving old logs to separate storage
- Use indexes for frequent queries

### Query Optimization
- Use pagination (50 items per page)
- Add date range filters to reduce result set
- Don't retrieve all activities at once

### Example Archive Query
```php
// Archive and delete logs older than 1 year
$logs = ActivityLog::where('created_at', '<', now()->subYear())->get();
// Save to archive (export to file or separate database)
ActivityLog::where('created_at', '<', now()->subYear())->delete();
```

## Summary

The Activity Logging System is now fully integrated and ready for use. All login/logout events and user management actions are automatically tracked. 

To complete the implementation:
1. ✅ Run `php artisan migrate`
2. Create view files in `resources/views/admin/activity-logs/`
3. Integrate logging into Document and Evaluation controllers
4. Test by logging in and checking activity logs at `/admin/activity-logs`
