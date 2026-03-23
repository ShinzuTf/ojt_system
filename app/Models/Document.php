<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'document_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get a human-readable document type label
     */
    public function getTypeLabelAttribute(): string
    {
        // If it's linked to a required document slot, use that name
        if ($this->required_document_id && $this->relationLoaded('requiredDocument')) {
            return $this->requiredDocument->document_name;
        }

        // Fallback or legacy matching
        return match($this->document_type) {
            'application_letter'    => 'Application Letter',
            'training_agreement'    => 'MOA / Training Agreement',
            'consent_form'          => 'Parental Consent Form',
            'progress_report'       => 'OJT Weekly Reports',
            'accomplishment_report' => 'Accomplishment Report',
            'final_evaluation'      => 'Final Evaluation',
            default                 => $this->document_type ? ucwords(str_replace('_', ' ', $this->document_type)) : 'Document',
        };
    }

    public function requiredDocument()
    {
        return $this->belongsTo(RequiredDocument::class, 'required_document_id');
    }
}
