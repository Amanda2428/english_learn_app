<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'role',
        'level_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 1;
    }

    public function isUser(): bool
    {
        return $this->role === 0;
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'level_id');
    }

    public function chatbotSessions()
    {
        return $this->hasMany(ChatbotSession::class, 'user_id', 'id');
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'user_id', 'id');
    }
    /**
     * Get the user's total points from progress.
     */
    public function getTotalPointsAttribute()
    {
        return $this->progress()->sum('points_earned');
    }

    /**
     * Get the user's last activity timestamp.
     */
    public function getLastActivityAttribute()
    {
        $lastSession = $this->chatbotSessions()->latest('last_msg_at')->first();
        return $lastSession ? $lastSession->last_msg_at : null;
    }
}
