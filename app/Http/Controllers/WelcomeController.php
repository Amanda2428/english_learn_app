<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\ChatbotRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        $totalSkills = Skill::where('status', true)->count();
        $totalVideos = Video::count();
        $totalQuestions = Question::count();
        $activeUsers = User::whereHas('progress')->count();

        if ($activeUsers == 0) {
            $activeUsers = 15234;
        }

        $stats = [
            'total_skills' => $totalSkills,
            'total_videos' => $totalVideos,
            'total_questions' => $totalQuestions,
            'active_users' => $activeUsers,
        ];

        $levels = Level::with(['skills' => function ($query) {
            $query->where('status', true);
        }])->orderBy('level_order')->get();

        foreach ($levels as $level) {
            $level->skills_count = $level->skills->count();
        }

        $popularSkills = Skill::where('status', true)
            ->withCount(['videos', 'questions'])
            ->orderBy('videos_count', 'desc')
            ->orderBy('questions_count', 'desc')
            ->take(8)
            ->get();

        $featuredVideos = Video::with('skill')
            ->latest()
            ->take(3)
            ->get();

        $dbRules = ChatbotRule::inRandomOrder()->take(3)->get();

        $sampleRules = $dbRules->map(function ($rule) {
            $colors = ['green', 'blue', 'purple', 'orange'];
            return [
                'name' => '💡 ' . $rule->keyword,
                'response' => $rule->response_text,
                'color' => $colors[array_rand($colors)]
            ];
        });

        if ($sampleRules->isEmpty()) {
            $sampleRules = collect([
                ['name' => '📚 Elementary', 'response' => 'Ask about basic levels!', 'color' => 'green'],
                ['name' => '🎯 Intermediate', 'response' => 'Ask about intermediate lessons!', 'color' => 'blue']
            ]);
        }

        $leaderboard = UserProgress::select(
            'user_id',
            DB::raw('SUM(points_earned) as total_points'),
            DB::raw('COUNT(DISTINCT skill_id) as skills_count')
        )
            ->where('points_earned', '>', 0)
            ->groupBy('user_id')
            ->orderBy('total_points', 'desc')
            ->limit(10)
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'email', 'profile');
            }])
            ->get()
            ->map(function ($progress) {
                return (object) [
                    'user_id' => $progress->user_id,
                    'name' => $progress->user->name ?? 'Unknown User',
                    'email' => $progress->user->email ?? '',
                    'profile' => $progress->user->profile ?? null,
                    'total_points' => $progress->total_points,
                    'skills_count' => $progress->skills_count
                ];
            });

        $userPoints = 0;
        if (Auth::check() && Auth::user()->role == 0) {
            $userPoints = UserProgress::where('user_id', Auth::id())->sum('points_earned');
        }
   // 1. Get real data from DB
    $dbLevels = Level::orderBy('level_order')->pluck('level_name')->toArray();
    $dbSkills = Skill::where('status', true)->pluck('skill_name')->toArray();

    // 2. Format the greeting dynamically
    $levelString = implode(', ', $dbLevels);
    $skillString = implode(', ', $dbSkills);
    
    $userName = Auth::check() ? Auth::user()->name : 'there';

    // This is the dynamic bot intro based on YOUR database
    $botIntro = "👋 Hello {$userName}! I'm your personal English tutor. I can help you find lessons. Try asking about: \n\n" .
                "📚 **Levels:** {$levelString}\n" . 
                "🗣️ **Skills:** {$skillString}\n\n" .
                "What would you like to practice today?";

    return view('welcome', compact('stats', 'levels', 'botIntro', 'popularSkills', 'leaderboard', 'userPoints'));
    }
}
