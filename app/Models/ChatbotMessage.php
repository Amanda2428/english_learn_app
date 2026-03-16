<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $table = 'chatbot_messages';
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'session_id',
        'user_message',
        'bot_response',
        'link_url',
        'link_title',
        'rule_id'
    ];

    public function session()
    {
        return $this->belongsTo(ChatbotSession::class, 'session_id', 'session_id');
    }

    public function rule()
    {
        return $this->belongsTo(ChatbotRule::class, 'rule_id', 'rule_id');
    }
}