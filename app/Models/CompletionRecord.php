<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletionRecord extends Model
{
    protected $table = 'completion_records';
    
    protected $fillable = [
        'student_id',
        'placement_id',
        'completion_date',
        'met_requirements',
        'total_hours_completed',
        'final_grade',
        'status',
        'approved_by',
        'approval_remarks',
        'approved_at',
        'is_completed',
        'certificate_number',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'met_requirements' => 'boolean',
        'is_completed' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function placement(): BelongsTo
    {
        return $this->belongsTo(OjtPlacement::class, 'placement_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    // Methods
    public function approve(User $coordinator, ?string $remarks = null): void
    {
        $this->status = 'approved';
        $this->is_completed = true;
        $this->approved_by = $coordinator->id;
        $this->approval_remarks = $remarks;
        $this->approved_at = now();
        $this->certificate_number = $this->generateCertificateNumber();
        $this->save();

        // Update student status if all requirements met
        if ($this->met_requirements) {
            $this->student->update(['status' => 'inactive']);
        }
    }

    public function markConditional(User $coordinator, string $remarks): void
    {
        $this->status = 'conditional';
        $this->approved_by = $coordinator->id;
        $this->approval_remarks = $remarks;
        $this->approved_at = now();
        $this->save();
    }

    public function generateCertificateNumber(): string
    {
        // Format: OJT-YEAR-STUDENTID-RANDOM
        $year = now()->year;
        $studentId = str_pad($this->student_id, 5, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(uniqid(), -4));

        return "OJT-{$year}-{$studentId}-{$random}";
    }

    public function getGradeText(): string
    {
        if (! $this->final_grade) {
            return 'Not Graded';
        }

        return match (true) {
            $this->final_grade >= 4.5 => 'Excellent (A)',
            $this->final_grade >= 3.5 => 'Very Good (B)',
            $this->final_grade >= 2.5 => 'Good (C)',
            $this->final_grade >= 1.5 => 'Satisfactory (D)',
            default => 'Needs Improvement (F)',
        };
    }
}
