<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotSession extends Model
{
    use HasFactory;

    protected $table = 'chatbot_sessions';
    protected $primaryKey = 'session_id';

    protected $fillable = [
        'user_id',
        'started_at',
        'last_msg_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_msg_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(ChatbotMessage::class, 'session_id', 'session_id');
    }
}