<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotSession;
use Illuminate\Http\Request;

class ChatbotSessionController extends Controller
{
    public function index()
    {
        $sessions = ChatbotSession::with('user')
            ->withCount('messages')
            ->latest('last_msg_at')
            ->paginate(15);
            
        return view('admin.chatbot.sessions.index', compact('sessions'));
    }
    
    public function show(ChatbotSession $session)
    {
        $session->load(['user', 'messages' => function($query) {
            $query->with('rule')->latest();
        }]);
        
        return view('admin.chatbot.sessions.show', compact('session'));
    }
}