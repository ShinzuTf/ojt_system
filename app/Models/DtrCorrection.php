<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DtrCorrection extends Model
{
    protected $fillable = [
        'dtr_id',
        'student_id',
        'original_time_in',
        'new_time_in',
        'original_time_out',
        'new_time_out',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function dtr(): BelongsTo
    {
        return $this->belongsTo(DailyTimeRecord::class, 'dtr_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function approve(User $reviewer): void
    {
        $this->status = 'approved';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->save();

        // Update the DTR record with new times
        $dtr = $this->dtr;
        $dtr->time_in = $this->new_time_in;
        $dtr->time_out = $this->new_time_out;
        $dtr->calculateHours();
        $dtr->save();
    }

    public function reject(User $reviewer): void
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->save();
    }
}
