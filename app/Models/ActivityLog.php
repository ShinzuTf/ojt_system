<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data' => 'json',
    ];

    /**
     * Get the user who performed the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the target user (if applicable)
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Scope: Filter by activity type
     */
    public function scopeByActivity($query, $activity)
    {
        return $query->where('activity', $activity);
    }

    /**
     * Scope: Filter by user role
     */
    public function scopeByRole($query, $role)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('role', $role);
        });
    }

    /**
     * Scope: Recent activities (last N days)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get readable activity description
     */
    public function getDescriptionAttribute(): string
    {
        $descriptions = [
            'user_login' => 'User logged in',
            'user_logout' => 'User logged out',
            'coordinator_created' => 'Coordinator account created',
            'coordinator_updated' => 'Coordinator account updated',
            'coordinator_deactivated' => 'Coordinator account deactivated',
            'student_created' => 'Student account created',
            'student_updated' => 'Student account updated',
            'student_deactivated' => 'Student account deactivated',
            'document_generated' => 'Document generated',
            'document_submitted' => 'Document submitted',
            'document_approved' => 'Document approved',
            'document_rejected' => 'Document rejected',
            'evaluation_created' => 'Evaluation created',
            'evaluation_submitted' => 'Evaluation submitted',
            'evaluation_updated' => 'Evaluation updated',
            'ojt_info_updated' => 'OJT information updated',
            'template_updated' => 'Template updated',
        ];

        return $descriptions[$this->activity] ?? $this->activity;
    }
}
