<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get all levels with their skills
        $levels = Level::with(['skills' => function($query) {
            $query->where('status', true);
        }])->orderBy('level_order')->get();

        // Get all active skills
        $skills = Skill::where('status', true)
            ->withCount(['videos', 'questions'])
            ->get();

        // Get featured videos (latest 3)
        $featuredVideos = Video::with('skill')
            ->latest()
            ->take(3)
            ->get();

        // Get statistics
        $stats = [
            'total_skills' => Skill::where('status', true)->count(),
            'total_videos' => Video::count(),
            'total_questions' => Question::count(),
            'active_users' => User::whereHas('progress')->count() ?: 1500, // Fallback if no users
        ];

        // Get levels with skill counts for display
        $levelsWithCounts = Level::withCount('skills')
            ->orderBy('level_order')
            ->get();

        // Get popular skills (with most videos/questions)
        $popularSkills = Skill::where('status', true)
            ->withCount(['videos', 'questions'])
            ->orderBy('videos_count', 'desc')
            ->orderBy('questions_count', 'desc')
            ->take(8)
            ->get();

        // For the chatbot preview, get some sample rules
        $sampleRules = [
            'elementary' => [
                'name' => 'Elementary Level',
                'response' => 'Start with basic greetings, simple present tense, and everyday vocabulary.',
                'color' => 'green'
            ],
            'intermediate' => [
                'name' => 'Intermediate Level',
                'response' => 'Practice past tenses, future plans, and business English basics.',
                'color' => 'blue'
            ],
            'advanced' => [
                'name' => 'Advanced Level',
                'response' => 'Master academic writing, professional presentations, and idioms.',
                'color' => 'purple'
            ],
        ];

        return view('welcome', compact(
            'levels', 
            'skills', 
            'featuredVideos', 
            'stats', 
            'levelsWithCounts',
            'popularSkills',
            'sampleRules'
        ));
    }
}