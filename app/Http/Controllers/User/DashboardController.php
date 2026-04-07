<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function welcome()
    {
        // If you still want a separate user welcome page, keep this.
        // Otherwise you can remove this method.

        $totalSkills = Skill::where('status', true)->count();
        $totalVideos = Video::count();
        $totalQuestions = Question::count();
        $totalLevels = Level::count();

        $levels = Level::with(['skills' => function ($query) {
            $query->where('status', true)->limit(4);
        }])->orderBy('level_order')->limit(3)->get();

        $featuredSkills = Skill::where('status', true)
            ->with(['levels', 'videos' => function ($query) {
                $query->limit(3);
            }])
            ->limit(4)
            ->get();

        $popularVideos = Video::with('skill')
            ->latest()
            ->limit(6)
            ->get();

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

        return view('user.welcome', compact(
            'totalSkills',
            'totalVideos',
            'totalQuestions',
            'totalLevels',
            'levels',
            'featuredSkills',
            'popularVideos',
            'userProgress',
            'recentActivities',
            'recommendedSkills'
        ));
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role == 1) {
            return redirect()->route('admin.dashboard');
        }

        $totalProgress = UserProgress::where('user_id', $user->id)->get();

        $stats = [
            'total_points' => $totalProgress->sum('points_earned'),
            'completed_skills' => $totalProgress->where('status', 'completed')->count(),
            'in_progress_skills' => $totalProgress->where('status', 'in_progress')->count(),
            'accuracy_rate' => $this->calculateAccuracyRate($totalProgress),
            'total_time_spent' => $totalProgress->sum('time_spent_minutes'),
            'videos_watched' => $totalProgress->sum('videos_watched'),
            'questions_answered' => $totalProgress->sum('questions_answered'),
        ];

        $levels = Level::with(['skills' => function ($query) {
            $query->where('status', true);
        }])->orderBy('level_order')->get();

        $userProgress = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->level_id . '-' . $item->skill_id;
            });

        $recentActivities = UserProgress::where('user_id', $user->id)
            ->with(['level', 'skill'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $recommendedSkills = $this->getRecommendedSkills($user);

        return view('user.dashboard', compact(
            'user',
            'stats',
            'levels',
            'userProgress',
            'recentActivities',
            'recommendedSkills'
        ));
    }

    public function progress()
    {
        $user = Auth::user();

        $progress = UserProgress::where('user_id', $user->id)
            ->with(['level', 'skill'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $summary = [
            'total_points' => $progress->sum('points_earned'),
            'completed_count' => $progress->where('status', 'completed')->count(),
            'in_progress_count' => $progress->where('status', 'in_progress')->count(),
            'average_accuracy' => $this->calculateAverageAccuracy($progress),
        ];

        return view('user.progress', compact('progress', 'summary'));
    }

    private function calculateAccuracyRate($progress)
    {
        $totalAnswered = $progress->sum('questions_answered');
        $totalCorrect = $progress->sum('correct_answers');

        if ($totalAnswered == 0) {
            return 0;
        }

        return round(($totalCorrect / $totalAnswered) * 100, 1);
    }

    private function calculateAverageAccuracy($progress)
    {
        $accuracies = $progress->map(function ($item) {
            if ($item->questions_answered > 0) {
                return ($item->correct_answers / $item->questions_answered) * 100;
            }
            return 0;
        })->filter();

        if ($accuracies->isEmpty()) {
            return 0;
        }

        return round($accuracies->avg(), 1);
    }

    private function getRecommendedSkills($user)
    {
        if ($user->level_id) {
            $startedSkillIds = UserProgress::where('user_id', $user->id)
                ->whereIn('status', ['in_progress', 'completed'])
                ->pluck('skill_id')
                ->toArray();

            return Skill::whereHas('levels', function ($query) use ($user) {
                    $query->where('levels.level_id', $user->level_id);
                })
                ->where('status', true)
                ->whereNotIn('skill_id', $startedSkillIds)
                ->limit(4)
                ->get();
        }

        return collect();
    }
}