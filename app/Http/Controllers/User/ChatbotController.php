<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatbotRule;
use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;

class ChatbotController extends Controller
{
    /**
     * Fetch chat history for the logged-in user
     */
    public function getHistory()
    {
        $session = ChatbotSession::where('user_id', Auth::id())->latest()->first();
        
        return response()->json([
            'messages' => $session ? $session->messages()->orderBy('created_at', 'asc')->get() : []
        ]);
    }

    /**
     * Handle incoming chatbot messages
     */
    public function sendMessage(Request $request) 
    {
        $userInput = strtolower(trim($request->message));
        $userId = Auth::id();

        $chatSession = ChatbotSession::firstOrCreate(
            ['user_id' => $userId],
    [
        'started_at' => now(), 
        'last_msg_at' => now()
    ]
);
        $chatSession->update(['last_msg_at' => now()]);

        $matchedRule = null;

        if (str_contains($userInput, 'more') || str_contains($userInput, 'another')) {
            $lastMessage = ChatbotMessage::where('session_id', $chatSession->session_id)
                ->whereNotNull('rule_id')
                ->latest()
                ->first();

            if ($lastMessage) {
                $prevRule = ChatbotRule::find($lastMessage->rule_id);
                $prevKeyword = $prevRule->keyword; 

                if (preg_match('/(\d+)$/', $prevKeyword, $matches)) {
                    $number = (int)$matches[1];
                    $baseKeyword = trim(str_replace($number, '', $prevKeyword));
                    $nextKeyword = $baseKeyword . ' ' . ($number + 1);
                } else {
                    $baseKeyword = $prevKeyword;
                    $nextKeyword = $prevKeyword . ' 2';
                }

                $matchedRule = ChatbotRule::where('keyword', $nextKeyword)->first();
                
                if (!$matchedRule) {
                    $matchedRule = ChatbotRule::where('keyword', $baseKeyword)->first();
                }
            }
        }

        if (!$matchedRule) {
            $rules = ChatbotRule::orderByRaw('LENGTH(keyword) DESC')->get();
            
            foreach ($rules as $rule) {
                $keyword = strtolower($rule->keyword);
                $words = explode(' ', $keyword);
                $allWordsPresent = true;

                foreach ($words as $word) {
                    if (is_numeric($word)) continue;
                    
                    if (!str_contains($userInput, $word)) {
                        $allWordsPresent = false;
                        break;
                    }
                }

                if ($allWordsPresent) {
                    $matchedRule = $rule;
                    break;
                }
            }
        }

        // 4. Final Response Preparation
        if ($matchedRule) {
            $response = $matchedRule->response_text;
            $url = $matchedRule->link_url;
            $title = $matchedRule->link_title;
            $ruleId = $matchedRule->rule_id;
        } else {
            $response = "I'm not sure about that. Try asking for a level like 'Beginner' or a skill like 'Listening'!";
            $url = null;
            $title = null;
            $ruleId = null;
        }

        // 5. STORE FOR ADMIN HISTORY
        ChatbotMessage::create([
            'session_id' => $chatSession->session_id,
            'user_message' => $request->message,
            'bot_response' => $response,
            'link_url' => $url,
            'link_title' => $title,
            'rule_id' => $ruleId
        ]);

        return response()->json([
            'bot_response' => $response,
            'link_url' => $url,
            'link_title' => $title
        ]);
    }
}