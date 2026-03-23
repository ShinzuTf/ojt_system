<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    protected $table = 'required_documents';

    protected $fillable = [
        'student_id',
        'document_name',
        'description',
        'is_fulfilled',
        'assigned_by',
    ];

    protected $casts = [
        'is_fulfilled' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submission()
    {
        return $this->hasOne(Document::class, 'required_document_id');
    }
}
