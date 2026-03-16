<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';
    protected $primaryKey = 'video_id';

    protected $fillable = [
        'skill_id',
        'title',
        'description',
        'video_file',
        'duration'
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'video_id', 'video_id');
    }

    /**
     * Get the level ID from the description
     */
    public function getLevelIdAttribute()
    {
        if ($this->description && preg_match('/<!-- LEVEL:(\d+) -->/', $this->description, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Get the level model instance from description marker
     */
    public function getLevelAttribute()
    {
        $levelId = $this->level_id;
        if ($levelId) {
            return Level::find($levelId);
        }
        return null;
    }

    /**
     * Get the progress records for this video.
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'skill_id', 'skill_id')
            ->where('skill_id', $this->skill_id);
    }

    /**
     * Get the level name from description marker
     */
    public function getLevelNameAttribute()
    {
        return $this->level?->level_name ?? 'No Level';
    }

    /**
     * Get clean description without the level marker
     */
    public function getCleanDescriptionAttribute()
    {
        if (!$this->description) {
            return '';
        }
        return preg_replace('/<!-- LEVEL:\d+ -->/', '', $this->description);
    }

    /**
     * Get description with level marker for display
     */
    public function getRawDescriptionAttribute()
    {
        return $this->description;
    }



    public function getSkillLevelsAttribute()
    {
        return $this->skill ? $this->skill->levels : collect();
    }
}
