<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LevelSkill extends Pivot
{
    use HasFactory; 

    protected $table = 'level_skill';
    public $incrementing = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'level_id',
        'skill_id'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'level_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }
}