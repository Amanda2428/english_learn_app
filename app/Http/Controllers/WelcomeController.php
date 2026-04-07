<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalSkills = Skill::where('status', true)->count();
        $totalVideos = Video::count();
        $totalQuestions = Question::count();

        // Count active users
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

        // Get levels with their skills
        $levels = Level::with(['skills' => function ($query) {
            $query->where('status', true);
        }])->orderBy('level_order')->get();

        foreach ($levels as $level) {
            $level->skills_count = $level->skills->count();
        }

        // Get popular skills
        $popularSkills = Skill::where('status', true)
            ->withCount(['videos', 'questions'])
            ->orderBy('videos_count', 'desc')
            ->orderBy('questions_count', 'desc')
            ->take(8)
            ->get();

        foreach ($popularSkills as $skill) {
            $skill->videos_count = $skill->videos_count ?? 0;
            $skill->questions_count = $skill->questions_count ?? 0;
        }

        // Get featured videos (limit to 3 for the welcome page)
        $featuredVideos = Video::with('skill')
            ->latest()
            ->take(3)
            ->get();

        $sampleRules = [
            [
                'name' => '📚 Elementary Level',
                'response' => 'Start with basic greetings, simple present tense, and everyday vocabulary.',
                'color' => 'green'
            ],
            [
                'name' => '🎯 Intermediate Level',
                'response' => 'Practice past tenses, future plans, and business English basics.',
                'color' => 'blue'
            ],
            [
                'name' => '📖 Advanced Level',
                'response' => 'Master academic writing, professional presentations, and idioms.',
                'color' => 'purple'
            ],
        ];

        // Get leaderboard data - Top 10 users by total points from user_progress
        $leaderboard = UserProgress::select(
                'user_id',
                DB::raw('SUM(points_earned) as total_points'),
                DB::raw('COUNT(DISTINCT skill_id) as skills_count')
            )
            ->where('points_earned', '>', 0)
            ->groupBy('user_id')
            ->orderBy('total_points', 'desc')
            ->limit(10)
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get()
            ->map(function($progress) {
                return (object) [
                    'user_id' => $progress->user_id,
                    'name' => $progress->user->name ?? 'Unknown User',
                    'email' => $progress->user->email ?? '',
                    'total_points' => $progress->total_points,
                    'skills_count' => $progress->skills_count
                ];
            });

        // Get current user's points for display
        $userPoints = 0;
        if (Auth::check() && Auth::user()->role == 0) {
            $userPoints = UserProgress::where('user_id', Auth::id())->sum('points_earned');
        }

        $userProgress = null;
        $recentActivities = null;
        $recommendedSkills = collect();

        if (Auth::check() && Auth::user()->role == 0) {
            $user = Auth::user();

            $userProgress = UserProgress::where('user_id', $user->id)
                ->with(['level', 'skill'])
                ->get();

            $recentActivities = UserProgress::where('user_id', $user->id)
                ->with(['level', 'skill'])
                ->latest('updated_at')
                ->limit(5)
                ->get();

            if ($user->level_id) {
                $recommendedSkills = Skill::whereHas('levels', function ($query) use ($user) {
                        $query->where('levels.level_id', $user->level_id);
                    })
                    ->where('status', true)
                    ->limit(3)
                    ->get();
            }
        }

        return view('welcome', compact(
            'stats',
            'levels',
            'popularSkills',
            'featuredVideos',
            'sampleRules',
            'userProgress',
            'recentActivities',
            'recommendedSkills',
            'leaderboard',
            'userPoints'
        ));
    }
}