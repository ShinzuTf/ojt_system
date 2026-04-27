<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueUpdate extends Model
{
    protected $fillable = [
        'issue_id',
        'updated_by',
        'update_description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
