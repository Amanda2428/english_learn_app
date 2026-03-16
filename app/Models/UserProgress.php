<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $primaryKey = 'progress_id';
    
    protected $fillable = [
        'user_id',
        'level_id',
        'skill_id',
        'points_earned',
        'completion_percentage',
        'videos_watched',
        'total_videos_in_skill',
        'questions_answered',
        'correct_answers',
        'total_questions_in_skill',
        'time_spent_minutes',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the level associated with this progress.
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'level_id');
    }

    /**
     * Get the skill associated with this progress.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }

    /**
     * Check if the skill is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the skill is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Get accuracy rate for questions.
     */
    public function getAccuracyRateAttribute(): float
    {
        if ($this->questions_answered === 0) {
            return 0;
        }
        
        return round(($this->correct_answers / $this->questions_answered) * 100, 2);
    }

    /**
     * Get video progress percentage.
     */
    public function getVideoProgressAttribute(): float
    {
        if ($this->total_videos_in_skill === 0) {
            return 0;
        }
        
        return round(($this->videos_watched / $this->total_videos_in_skill) * 100, 2);
    }

    /**
     * Get question progress percentage.
     */
    public function getQuestionProgressAttribute(): float
    {
        if ($this->total_questions_in_skill === 0) {
            return 0;
        }
        
        return round(($this->correct_answers / $this->total_questions_in_skill) * 100, 2);
    }

    /**
     * Scope for completed progress.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for in-progress progress.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for not started progress.
     */
    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not_started');
    }

    /**
     * Scope for specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific level.
     */
    public function scopeForLevel($query, int $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope for specific skill.
     */
    public function scopeForSkill($query, int $skillId)
    {
        return $query->where('skill_id', $skillId);
    }
}