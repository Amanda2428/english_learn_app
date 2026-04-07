<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'levels';
    protected $primaryKey = 'level_id';

    protected $fillable = [
        'level_name',
        'level_order',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'level_id', 'level_id');
    }
    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'level_skill',
            'level_id',
            'skill_id',
            'level_id',
            'skill_id'
        );
    }
    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,
            Skill::class,
            'level_id', 
            'skill_id', 
            'level_id', 
            'skill_id'  
        );
    }

}
