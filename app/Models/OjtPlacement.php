<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OjtPlacement extends Model
{
    protected $table = 'ojt_placements';
    
    protected $fillable = [
        'student_id',
        'company_name',
        'position',
        'company_id',
        'supervisor_id',
        'coordinator_id',
        'start_date',
        'end_date',
        'total_required_hours',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'placement_id');
    }

    public function completionRecord(): BelongsTo
    {
        return $this->belongsTo(CompletionRecord::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Methods
    public function getDaysElapsed(): int
    {
        return $this->start_date->diffInDays(now());
    }

    public function getDaysRemaining(): int
    {
        return max(0, now()->diffInDays($this->end_date));
    }

    public function getProgressPercentage(): float
    {
        $dtr = DailyTimeRecord::forStudent($this->student_id)->sum('hours_worked');
        return min(100, ($dtr / $this->total_required_hours) * 100);
    }

    public function isOverdue(): bool
    {
        return now()->isAfter($this->end_date);
    }

    public function markCompleted(): void
    {
        $this->status = 'completed';
        $this->save();
    }
}
