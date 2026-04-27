<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    protected $fillable = [
        'student_id',
        'reported_by',
        'company_id',
        'issue_type',
        'issue_date',
        'description',
        'action_taken',
        'attachment_path',
        'status',
        'assigned_to',
        'resolution_notes',
        'resolution_date',
        'effective_date',
        'student_status',
        'transfer_certificate_path',
        'transfer_certificate_name',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'resolution_date' => 'date',
        'effective_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IssueUpdate::class);
    }

    // Scopes
    public function scopeReported($query)
    {
        return $query->where('status', 'reported');
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeAbsence($query)
    {
        return $query->where('issue_type', 'absence');
    }

    public function scopeDropped($query)
    {
        return $query->where('issue_type', 'drop');
    }

    public function scopeTransferred($query)
    {
        return $query->where('issue_type', 'transfer');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Methods
    public function acknowledge(User $coordinator): void
    {
        $this->status = 'acknowledged';
        $this->assigned_to = $coordinator->id;
        $this->save();

        $this->addUpdate("Issue acknowledged by {$coordinator->fullName()}");
    }

    public function resolve(User $coordinator, string $notes): void
    {
        $this->status = 'resolved';
        $this->resolution_notes = $notes;
        $this->resolution_date = now()->toDateString();
        $this->save();

        $this->addUpdate("Issue resolved by {$coordinator->fullName()}: {$notes}");
    }

    public function markDropped(string $notes = ''): void
    {
        $this->student_status = 'dropped';
        $this->effective_date = now()->toDateString();
        $this->resolution_notes = $notes;
        $this->status = 'resolved';
        $this->save();

        $this->student->update(['status' => 'inactive']);

        $this->addUpdate("Student marked as dropped. {$notes}");
    }

    public function markTransferred(string $notes = ''): void
    {
        $this->student_status = 'transferred';
        $this->effective_date = now()->toDateString();
        $this->resolution_notes = $notes;
        $this->status = 'resolved';
        $this->save();

        $this->addUpdate("Student marked as transferred. {$notes}");
    }

    public function addUpdate(string $description): void
    {
        IssueUpdate::create([
            'issue_id' => $this->id,
            'updated_by' => auth()->id(),
            'update_description' => $description,
        ]);
    }
}
