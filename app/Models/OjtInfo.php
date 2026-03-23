<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OjtInfo extends Model
{
    protected $table = 'ojt_info';

    protected $fillable = [
        'user_id',
        'student_number',
        'course',
        'year_level',
        'company_name',
        'company_email',
        'company_address',
        'supervisor_name',
        'supervisor_contact',
        'ojt_start',
        'ojt_end',
        'required_hours',
        'rendered_hours',
        'ojt_status',
    ];

    protected $casts = [
        'ojt_start' => 'date',
        'ojt_end'   => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get OJT progress percentage
     */
    public function getProgressPercentAttribute(): int
    {
        if (!$this->required_hours || $this->required_hours == 0) return 0;
        return min(100, (int) round(($this->rendered_hours / $this->required_hours) * 100));
    }

    /**
     * Get course full name
     */
    public function getCourseFullNameAttribute(): string
    {
        return match($this->course) {
            'BSIT' => 'BS Information Technology',
            'BSCS' => 'BS Computer Science',
            'BSIS' => 'BS Information Systems',
            'ACT'  => 'Associate in Computer Technology',
            default => $this->course ?? '—',
        };
    }
}
