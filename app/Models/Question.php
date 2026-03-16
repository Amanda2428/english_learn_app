<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'skill_id',
        'video_id',
        'question_text',
        'difficulty',
        'points',
        'question_type',
        'level_id', 
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    /**
     * Get the skill that owns the question.
     */
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }

    /**
     * Get the video associated with the question.
     */
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id', 'video_id');
    }

    /**
     * Get the level associated with the question.
     * ADD THIS METHOD
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'level_id');
    }

    /**
     * Get the answers for the question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    public function getCorrectAnswers()
    {
        return $this->answers()->where('is_correct', true)->get();
    }

    public function getCleanQuestionTextAttribute()
    {
        if (!$this->question_text) {
            return '';
        }

        $text = preg_replace('/<!-- VIDEO:\d+ -->/', '', $this->question_text);
        $text = preg_replace('/<!-- LEVEL:\d+ -->/', '', $text);
        return trim($text);
    }

    public function getHasVideoAttribute()
    {
        return $this->video_id !== null;
    }

    public function getLevelNameAttribute()
    {
        return $this->level?->level_name ?? 'No Level';
    }
}