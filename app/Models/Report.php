<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = [
        'submitted_by',
        'report_type',
        'report_period_start',
        'report_period_end',
        'accomplishments',
        'activities',
        'challenges',
        'learnings',
        'recommendations',
        'file_path',
        'file_type',
        'status',
        'reviewed_by',
        'reviewer_comments',
        'reviewed_at',
        'escalated_to',
        'escalation_reason',
        'escalated_at',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'reviewed_at' => 'datetime',
        'escalated_at' => 'datetime',
    ];

    // Relationships
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ReportHistory::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeWeekly($query)
    {
        return $query->where('report_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('report_type', 'monthly');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereDate('report_period_start', '>=', $startDate)
                     ->whereDate('report_period_end', '<=', $endDate);
    }

    // Methods
    public function approve(User $reviewer, ?string $comments = null): void
    {
        $this->status = 'approved';
        $this->reviewed_by = $reviewer->id;
        $this->reviewer_comments = $comments;
        $this->reviewed_at = now();
        $this->save();

        $this->addHistory("Report approved by {$reviewer->fullName()}");
    }

    public function reject(User $reviewer, string $comments): void
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewer->id;
        $this->reviewer_comments = $comments;
        $this->reviewed_at = now();
        $this->save();

        $this->addHistory("Report rejected by {$reviewer->fullName()}: {$comments}");
    }

    public function requestRevision(User $reviewer, string $comments): void
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewer->id;
        $this->reviewer_comments = $comments;
        $this->reviewed_at = now();
        $this->save();

        $this->addHistory("Revision requested by {$reviewer->fullName()}: {$comments}");
    }

    public function escalate(User $coordinator, string $reason): void
    {
        $this->escalated_to = $coordinator->id;
        $this->escalation_reason = $reason;
        $this->escalated_at = now();
        $this->status = 'under_review';
        $this->save();

        $this->addHistory("Report escalated to {$coordinator->fullName()}: {$reason}");
    }

    public function addHistory(string $description): void
    {
        ReportHistory::create([
            'report_id' => $this->id,
            'changes_description' => $description,
            'changed_by' => auth()->id(),
        ]);
    }
}
