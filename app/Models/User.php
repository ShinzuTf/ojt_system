<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'suffix',
        'email',
        'role',
        'status',
        'password',
        'student_number',
        'course',
        'year_level',
        'company_name',
        'company_email',
        'must_change_password',
    ];

    /**
     * Get full name formatted as "Last Name, First Name M."
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->lname . ', ' . $this->fname;
        if ($this->mname) {
            $name .= ' ' . strtoupper(substr($this->mname, 0, 1)) . '.';
        }
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return $name;
    }

    /**
     * Short full name: First + Last only
     */
    public function getShortNameAttribute(): string
    {
        return $this->fname . ' ' . $this->lname;
    }

    /**
     * Method to get full name (used in various controllers)
     */
    public function fullName(): string
    {
        return $this->full_name;
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is a supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if user is a coordinator
     */
    public function isCoordinator(): bool
    {
        return $this->role === 'coordinator';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relationship: OJT Info (one-to-one)
     */
    public function ojtInfo()
    {
        return $this->hasOne(OjtInfo::class, 'user_id');
    }

    /**
     * Relationship: Past OJT Records (archived records from previous semesters)
     */
    public function pastOjtRecords()
    {
        return $this->hasMany(PastOjtRecord::class, 'user_id');
    }

    /**
     * Relationship: Required Documents assigned by admin
     */
    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class, 'student_id');
    }

    /**
     * Relationship: Submitted Documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    /**
     * Relationship: Notifications
     */
    public function notifications()
    {
        return $this->hasMany(NotificationLog::class, 'user_id');
    }

    /**
     * Relationship: Evaluations given by this user (supervisor/coordinator)
     */
    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'supervisor_id');
    }

    /**
     * Relationship: Evaluations received by this user (trainee/student)
     */
    public function evaluationsReceived()
    {
        return $this->hasMany(Evaluation::class, 'trainee_id');
    }

    /**
     * ==================== NEW RELATIONSHIPS FOR OJT SYSTEM ====================
     */

    // DTR Relationships
    /**
     * Relationship: Daily Time Records submitted by student
     */
    public function dailyTimeRecords()
    {
        return $this->hasMany(DailyTimeRecord::class, 'student_id');
    }

    /**
     * Relationship: DTR entries verified by supervisor
     */
    public function verifiedDtrs()
    {
        return $this->hasMany(DailyTimeRecord::class, 'verified_by');
    }

    /**
     * Relationship: DTR corrections requested by student
     */
    public function dtrCorrections()
    {
        return $this->hasMany(DtrCorrection::class, 'student_id');
    }

    // Report Relationships
    /**
     * Relationship: Reports submitted by user
     */
    public function submittedReports()
    {
        return $this->hasMany(Report::class, 'submitted_by');
    }

    /**
     * Relationship: Reports reviewed by user (supervisor/coordinator)
     */
    public function reviewedReports()
    {
        return $this->hasMany(Report::class, 'reviewed_by');
    }

    /**
     * Relationship: Reports escalated to coordinator
     */
    public function escalatedReports()
    {
        return $this->hasMany(Report::class, 'escalated_to');
    }

    // Issue Relationships
    /**
     * Relationship: Issues reported by supervisor/coordinator
     */
    public function reportedIssues()
    {
        return $this->hasMany(Issue::class, 'reported_by');
    }

    /**
     * Relationship: Issues assigned to coordinator
     */
    public function assignedIssues()
    {
        return $this->hasMany(Issue::class, 'assigned_to');
    }

    /**
     * Relationship: Issues related to student
     */
    public function issues()
    {
        return $this->hasMany(Issue::class, 'student_id');
    }

    // OJT Placement Relationships
    /**
     * Relationship: OJT placements for student
     */
    public function placements()
    {
        return $this->hasMany(OjtPlacement::class, 'student_id');
    }

    /**
     * Relationship: OJT placements supervised by supervisor
     */
    public function supervisedPlacements()
    {
        return $this->hasMany(OjtPlacement::class, 'supervisor_id');
    }

    /**
     * Relationship: OJT placements coordinated by coordinator
     */
    public function coordinatedPlacements()
    {
        return $this->hasMany(OjtPlacement::class, 'coordinator_id');
    }

    /**
     * Relationship: Company placements (for company users)
     */
    public function companyPlacements()
    {
        return $this->hasMany(OjtPlacement::class, 'company_id');
    }

    // Certification Relationships
    /**
     * Relationship: Certifications issued by supervisor
     */
    public function issuedCertifications()
    {
        return $this->hasMany(Certification::class, 'issued_by');
    }

    /**
     * Relationship: Certifications verified by coordinator
     */
    public function verifiedCertifications()
    {
        return $this->hasMany(Certification::class, 'verified_by');
    }

    /**
     * Relationship: Certifications for student
     */
    public function receivedCertifications()
    {
        return $this->hasMany(Certification::class, 'student_id');
    }

    // Completion Record Relationships
    /**
     * Relationship: Completion records for student
     */
    public function completionRecords()
    {
        return $this->hasMany(CompletionRecord::class, 'student_id');
    }

    /**
     * Relationship: Completion records approved by coordinator
     */
    public function approvedCompletions()
    {
        return $this->hasMany(CompletionRecord::class, 'approved_by');
    }

    // Activity Log Relationship
    /**
     * Relationship: Activity logs created by user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
