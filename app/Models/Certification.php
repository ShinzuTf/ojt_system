<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $fillable = [
        'placement_id',
        'student_id',
        'issued_by',
        'verified_by',
        'certification_date',
        'actual_hours_worked',
        'final_rating',
        'remarks',
        'status',
        'verified_at',
        'certificate_path',
        'certificate_file_name',
    ];

    protected $casts = [
        'certification_date' => 'date',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function placement(): BelongsTo
    {
        return $this->belongsTo(OjtPlacement::class, 'placement_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function verify(User $coordinator): void
    {
        $this->status = 'verified';
        $this->verified_by = $coordinator->id;
        $this->verified_at = now();
        $this->save();
    }

    public function approve(User $coordinator): void
    {
        $this->status = 'approved';
        $this->verified_by = $coordinator->id;
        $this->verified_at = now();
        $this->save();

        // Mark placement as completed
        $this->placement->markCompleted();
    }

    public function getRatingText(): string
    {
        if (! $this->final_rating) {
            return 'Not Rated';
        }

        return match (true) {
            $this->final_rating >= 4.5 => 'Excellent',
            $this->final_rating >= 3.5 => 'Very Good',
            $this->final_rating >= 2.5 => 'Good',
            $this->final_rating >= 1.5 => 'Satisfactory',
            default => 'Needs Improvement',
        };
    }
}
