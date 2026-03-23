<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluations';

    protected $fillable = [
        'trainee_id',
        'supervisor_id',
        'evaluation_date',
        'strengths',
        'areas_for_improvement',
        'skills_to_develop',
        'overall_comments',
        'technical_skills_rating',
        'communication_rating',
        'teamwork_rating',
        'professionalism_rating',
        'initiative_rating',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the trainee (student) being evaluated
     */
    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }

    /**
     * Get the supervisor/coordinator who performed the evaluation
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get average rating across all rating fields
     */
    public function getAverageRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->technical_skills_rating,
            $this->communication_rating,
            $this->teamwork_rating,
            $this->professionalism_rating,
            $this->initiative_rating,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 2);
    }

    /**
     * Scope: Get evaluations pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get evaluations approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Get evaluations by trainee
     */
    public function scopeByTrainee($query, $traineeId)
    {
        return $query->where('trainee_id', $traineeId);
    }

    /**
     * Scope: Get evaluations by supervisor
     */
    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Scope: Get evaluations within a date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('evaluation_date', [$startDate, $endDate]);
    }
}
