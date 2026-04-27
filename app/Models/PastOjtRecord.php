<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PastOjtRecord extends Model
{
    protected $table = 'past_ojt_records';

    protected $fillable = [
        'user_id',
        'student_number',
        'course',
        'year_level',
        'company_name',
        'company_email',
        'company_address',
        'supervisor_name',
        'supervisor_contact',
        'ojt_start',
        'ojt_end',
        'required_hours',
        'rendered_hours',
        'ojt_status',
        'archived_at',
        'archived_by',
        'archive_notes',
    ];

    protected $casts = [
        'ojt_start' => 'date',
        'ojt_end' => 'date',
        'archived_at' => 'datetime',
    ];

    /**
     * Relationship: Student who owns this past OJT record
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Admin who archived this record
     */
    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Get course full name
     */
    public function getCourseFullNameAttribute(): string
    {
        return match($this->course) {
            'BSIT' => 'BS Information Technology',
            'BSCS' => 'BS Computer Science',
            'BSIS' => 'BS Information Systems',
            'ACT'  => 'Associate in Computer Technology',
            default => $this->course ?? '—',
        };
    }

    /**
     * Get OJT progress percentage
     */
    public function getProgressPercentAttribute(): int
    {
        if (!$this->required_hours || $this->required_hours == 0) return 0;
        return min(100, (int) round(($this->rendered_hours / $this->required_hours) * 100));
    }
}
