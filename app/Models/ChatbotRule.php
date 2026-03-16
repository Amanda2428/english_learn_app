<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotRule extends Model
{
    use HasFactory;

    protected $table = 'chatbot_rules';
    protected $primaryKey = 'rule_id';

    protected $fillable = [
        'keyword',
        'response_text',
        'link_url',
        'link_title'
    ];

    public function messages()
    {
        return $this->hasMany(ChatbotMessage::class, 'rule_id', 'rule_id');
    }
}