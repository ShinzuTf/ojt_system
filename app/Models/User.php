<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'suffix',
        'email',
        'role',
        'status',
        'password',
        'company_name',
        'company_email',
        'must_change_password',
    ];

    /**
     * Get full name formatted as "Last Name, First Name M."
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->lname . ', ' . $this->fname;
        if ($this->mname) {
            $name .= ' ' . strtoupper(substr($this->mname, 0, 1)) . '.';
        }
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return $name;
    }

    /**
     * Short full name: First + Last only
     */
    public function getShortNameAttribute(): string
    {
        return $this->fname . ' ' . $this->lname;
    }

    /**
     * Relationship: OJT Info (one-to-one)
     */
    public function ojtInfo()
    {
        return $this->hasOne(OjtInfo::class, 'user_id');
    }

    /**
     * Relationship: Required Documents assigned by admin
     */
    public function requiredDocuments()
    {
        return $this->hasMany(RequiredDocument::class, 'student_id');
    }

    /**
     * Relationship: Submitted Documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    /**
     * Relationship: Notifications
     */
    public function notifications()
    {
        return $this->hasMany(NotificationLog::class, 'user_id');
    }

    /**
     * Relationship: Evaluations given by this user (supervisor/coordinator)
     */
    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'supervisor_id');
    }

    /**
     * Relationship: Evaluations received by this user (trainee/student)
     */
    public function evaluationsReceived()
    {
        return $this->hasMany(Evaluation::class, 'trainee_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
