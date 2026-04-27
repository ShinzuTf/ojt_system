<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyTimeRecord extends Model
{
    protected $fillable = [
        'student_id',
        'record_date',
        'time_in',
        'time_out',
        'hours_worked',
        'notes',
        'status',
        'verified_by',
        'supervisor_remarks',
        'verified_at',
    ];

    protected $casts = [
        'record_date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(DtrCorrection::class, 'dtr_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('record_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereDate('record_date', '>=', $startDate)
                     ->whereDate('record_date', '<=', $endDate);
    }

    // Methods
    public function calculateHours(): void
    {
        if ($this->time_in && $this->time_out) {
            $in = strtotime($this->time_in);
            $out = strtotime($this->time_out);
            $this->hours_worked = ($out - $in) / 3600; // Convert seconds to hours
        }
    }

    public function verify(User $supervisor, ?string $remarks = null): void
    {
        $this->status = 'verified';
        $this->verified_by = $supervisor->id;
        $this->supervisor_remarks = $remarks;
        $this->verified_at = now();
        $this->save();
    }

    public function reject(User $supervisor, string $remarks): void
    {
        $this->status = 'rejected';
        $this->verified_by = $supervisor->id;
        $this->supervisor_remarks = $remarks;
        $this->verified_at = now();
        $this->save();
    }
}
