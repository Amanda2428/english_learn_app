<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';
    protected $primaryKey = 'skill_id';

    protected $fillable = [
        'skill_name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function levels()
    {
        return $this->belongsToMany(
            Level::class,
            'level_skill',
            'skill_id',
            'level_id',
            'skill_id',
            'level_id'
        )->withTimestamps();
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'skill_id', 'skill_id');
    }
    
    /**
     * Get questions for a specific level
     */
    public function questionsForLevel($levelId)
    {
        return $this->questions()->where('level_id', $levelId);
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'skill_id', 'skill_id');
    }
}