<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotRule;
use Illuminate\Http\Request;

class ChatbotRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatbotRule::withCount('messages');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('keyword', 'LIKE', "%{$search}%")
                  ->orWhere('response_text', 'LIKE', "%{$search}%")
                  ->orWhere('link_title', 'LIKE', "%{$search}%")
                  ->orWhere('link_url', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by link presence
        if ($request->has('filter') && !empty($request->filter)) {
            switch ($request->filter) {
                case 'with_links':
                    $query->whereNotNull('link_url');
                    break;
                case 'no_links':
                    $query->whereNull('link_url');
                    break;
                case 'most_used':
                    $query->orderBy('messages_count', 'desc');
                    break;
                case 'least_used':
                    $query->orderBy('messages_count', 'asc');
                    break;
            }
        }
        
        // Default sorting
        if (!$request->has('filter') || !in_array($request->filter, ['most_used', 'least_used'])) {
            $query->latest();
        }
        
        $rules = $query->paginate(10)->withQueryString();
        
        // Get stats for the cards
        $totalRules = ChatbotRule::count();
        $activeKeywords = ChatbotRule::count(); 
        $rulesWithLinks = ChatbotRule::whereNotNull('link_url')->count();
 
            $totalUsage = \App\Models\ChatbotMessage::count();
        
        // Preserve search and filter values for the view
        $search = $request->search;
        $currentFilter = $request->filter;
        
        return view('admin.chatbot.rules.index', compact(
            'rules', 
            'totalRules', 
            'activeKeywords', 
            'rulesWithLinks', 
    'totalUsage',
            'search',
            'currentFilter'
        ));
    }
    
    public function create()
    {
        return view('admin.chatbot.rules.form');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:chatbot_rules',
            'response_text' => 'required|string',
            'link_url' => 'nullable|url|max:255',
            'link_title' => 'nullable|string|max:255',
        ]);
        
        ChatbotRule::create($validated);
        
        return redirect()->route('admin.chatbot.rules.index')
            ->with('success', 'Chatbot rule created successfully.');
    }
    
    public function edit(ChatbotRule $rule)
    {
        return view('admin.chatbot.rules.form', compact('rule'));
    }
    
    public function update(Request $request, ChatbotRule $rule)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:chatbot_rules,keyword,' . $rule->rule_id . ',rule_id',
            'response_text' => 'required|string',
            'link_url' => 'nullable|url|max:255',
            'link_title' => 'nullable|string|max:255',
        ]);
        
        $rule->update($validated);
        
        return redirect()->route('admin.chatbot.rules.index')
            ->with('success', 'Chatbot rule updated successfully.');
    }
    
    public function destroy(ChatbotRule $rule)
    {
        $rule->delete();
        return redirect()->route('admin.chatbot.rules.index')
            ->with('success', 'Chatbot rule deleted successfully.');
    }
}