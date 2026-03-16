<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;
use App\Models\ChatbotRule;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $welcomeTitle = 'Welcome, ' . ($currentUser->name ?? 'User');

        // Basic counts
        $totalUsers = User::count();
        $totalLevels = Level::count();
        $totalSkills = Skill::count();
        $totalVideos = Video::count();
        $totalQuestions = Question::count();
        $totalAnswers = Answer::count();
        $totalContent = $totalVideos + $totalQuestions;

        // User statistics
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();

        $adminCount = User::where('role', 1)->count();
        $regularUserCount = User::where('role', 0)->count();

        // Users by level
        $usersByLevel = Level::withCount('users')
            ->orderBy('level_order')
            ->get()
            ->map(function ($level) {
                return [
                    'name' => $level->level_name,
                    'users' => $level->users_count
                ];
            });

        // User progress
        $averageProgress = round(UserProgress::avg('completion_percentage') ?? 0, 1);
        $totalPointsEarned = UserProgress::sum('points_earned') ?? 0;
        $completedContent = UserProgress::whereNotNull('completed_at')->count();

        // Video statistics
        $totalVideoDuration = 0;
        $videos = Video::all();

        foreach ($videos as $video) {
            if (!empty($video->duration)) {
                $totalVideoDuration += $this->parseDurationToSeconds($video->duration);
            }
        }

        $avgVideoDuration = $totalVideos > 0 ? floor($totalVideoDuration / $totalVideos) : 0;
        $avgDurationFormatted = $avgVideoDuration > 0 ? gmdate('H:i:s', $avgVideoDuration) : '00:00:00';

        // Question statistics
        $questionsByDifficulty = [
            'easy' => Question::where('difficulty', 'easy')->count(),
            'medium' => Question::where('difficulty', 'medium')->count(),
            'hard' => Question::where('difficulty', 'hard')->count(),
        ];

        $questionsByType = Question::select('question_type', DB::raw('count(*) as total'))
            ->groupBy('question_type')
            ->pluck('total', 'question_type')
            ->toArray();

        // Skills stats
        $totalActiveSkills = Skill::where('status', true)->count();
        $skillsWithQuestions = Skill::has('questions')->count();

        // Content per level
        $levels = Level::with(['skills'])->withCount('skills')->orderBy('level_order')->get();
        $hasVideoSkillColumn = Schema::hasColumn('videos', 'skill_id');

        $contentPerLevel = $levels->map(function ($level) use ($hasVideoSkillColumn) {
            $skillIds = $level->skills->pluck('skill_id')->toArray();

            $questionsCount = !empty($skillIds)
                ? Question::whereIn('skill_id', $skillIds)->count()
                : 0;

            $videosCount = 0;
            if ($hasVideoSkillColumn && !empty($skillIds)) {
                $videosCount = Video::whereIn('skill_id', $skillIds)->count();
            }

            return (object) [
                'level_name' => $level->level_name,
                'skills_count' => $level->skills_count,
                'videos_count' => $videosCount,
                'questions_count' => $questionsCount,
                'total_content' => $videosCount + $questionsCount,
                'level_order' => $level->level_order
            ];
        });

        // Chatbot
        $totalChatSessions = ChatbotSession::count();
        $totalChatMessages = ChatbotMessage::count();
        $totalBotRules = ChatbotRule::count();

        $activeSessionsToday = ChatbotSession::whereDate('last_msg_at', Carbon::today())->count();
        $activeSessionsThisWeek = ChatbotSession::where('last_msg_at', '>=', Carbon::now()->subDays(7))->count();

        $activeChatUsers = ChatbotSession::where('last_msg_at', '>=', Carbon::now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        $topRules = ChatbotRule::withCount('messages')
            ->having('messages_count', '>', 0)
            ->orderByDesc('messages_count')
            ->take(5)
            ->get();

        // Registrations chart
        $userRegistrations = collect();
        $maxCount = 0;

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $maxCount = max($maxCount, $count);

            $userRegistrations->push([
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'count' => $count,
                'full_date' => $date->format('Y-m-d')
            ]);
        }

        $totalRegistrations = $userRegistrations->sum('count');
        $avgRegistrations = $totalRegistrations > 0 ? round($totalRegistrations / 7, 1) : 0;
        $trend = $this->calculateTrend($userRegistrations);

        // Recent
        $recentUsers = User::with('level')->latest()->take(5)->get();
        $recentChatSessions = ChatbotSession::with('user')->withCount('messages')->latest('last_msg_at')->take(5)->get();
        $recentQuestions = Question::with('skill')->latest()->take(5)->get();
        $recentVideos = Video::with('skill')->latest()->take(5)->get();

        // Storage
        $storageUsed = $this->calculateStorageUsage();
        $storageTotal = 10;
        $storageFree = max(0, $storageTotal - $storageUsed);
        $storagePercentage = $storageTotal > 0 ? min(100, round(($storageUsed / $storageTotal) * 100, 1)) : 0;

        // Health
        $systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $storagePercentage < 90 ? 'healthy' : 'warning',
            'users_growth' => $trend
        ];

        // Profile info
        $profileData = [
            'name' => $currentUser->name ?? '-',
            'email' => $currentUser->email ?? '-',
            'role' => $currentUser && (int)$currentUser->role === 1 ? 'Administrator' : 'User',
            'profile' => $currentUser->profile ?? 'No profile bio added yet.',
            'joined' => $currentUser?->created_at ? $currentUser->created_at->format('d M Y') : '-',
        ];

        $notifications = collect();

        if ($newUsersToday > 0) {
            $notifications->push([
                'title' => 'New Users Today',
                'message' => $newUsersToday . ' new user(s) registered today.',
                'type' => 'info',
                'time' => 'Today',
                'link' => route('admin.users.index'),
            ]);
        }

        if ($activeSessionsToday > 0) {
            $notifications->push([
                'title' => 'Chat Activity',
                'message' => $activeSessionsToday . ' chatbot session(s) active today.',
                'type' => 'success',
                'time' => 'Today',
                'link' => route('admin.chatbot.sessions.index'),
            ]);
        }

        if ($storagePercentage >= 80) {
            $notifications->push([
                'title' => 'Storage Warning',
                'message' => 'Storage usage is at ' . $storagePercentage . '%.',
                'type' => 'warning',
                'time' => 'Now',
                'link' => '#',
            ]);
        }

        if ($totalBotRules === 0) {
            $notifications->push([
                'title' => 'Chatbot Setup',
                'message' => 'No chatbot rules found. Add rules to improve responses.',
                'type' => 'warning',
                'time' => 'Now',
                'link' => route('admin.chatbot.rules.index'),
            ]);
        }

        $notificationCount = $notifications->count();

        return view('dashboard', compact(
            'welcomeTitle',
            'profileData',
            'totalUsers',
            'totalLevels',
            'totalSkills',
            'totalVideos',
            'totalQuestions',
            'totalAnswers',
            'totalContent',
            'newUsersToday',
            'newUsersThisWeek',
            'newUsersThisMonth',
            'adminCount',
            'regularUserCount',
            'usersByLevel',
            'averageProgress',
            'totalPointsEarned',
            'completedContent',
            'totalVideoDuration',
            'avgVideoDuration',
            'avgDurationFormatted',
            'questionsByDifficulty',
            'questionsByType',
            'totalActiveSkills',
            'skillsWithQuestions',
            'contentPerLevel',
            'totalChatSessions',
            'totalChatMessages',
            'totalBotRules',
            'activeSessionsToday',
            'activeSessionsThisWeek',
            'activeChatUsers',
            'topRules',
            'userRegistrations',
            'maxCount',
            'totalRegistrations',
            'avgRegistrations',
            'trend',
            'recentUsers',
            'recentChatSessions',
            'recentQuestions',
            'recentVideos',
            'storageUsed',
            'storageFree',
            'storagePercentage',
            'systemHealth',
            'notifications',
            'notificationCount',
        ));
    }

    private function parseDurationToSeconds($duration)
    {
        if (empty($duration)) {
            return 0;
        }

        $duration = trim((string) $duration);
        $timeParts = explode(':', $duration);

        if (count($timeParts) === 3) {
            return ((int) $timeParts[0] * 3600) + ((int) $timeParts[1] * 60) + (int) $timeParts[2];
        }

        if (count($timeParts) === 2) {
            return ((int) $timeParts[0] * 60) + (int) $timeParts[1];
        }

        return 0;
    }

    private function calculateTrend($registrations)
    {
        if ($registrations->count() < 2) {
            return 'stable';
        }

        $first = $registrations->first()['count'];
        $last = $registrations->last()['count'];
        $middle = $registrations->slice(2, 3)->avg('count') ?? $first;

        if ($last > $first && $last > $middle) {
            return 'up';
        }

        if ($last < $first && $last < $middle) {
            return 'down';
        }

        return 'stable';
    }

    private function calculateStorageUsage()
    {
        try {
            $videos = Video::all();
            $totalSize = 0;

            foreach ($videos as $video) {
                if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
                    $totalSize += Storage::disk('public')->size($video->video_file);
                }
            }

            return round($totalSize / (1024 * 1024 * 1024), 2);
        } catch (\Exception $e) {
            $videoCount = Video::count();
            return round(($videoCount * 50) / 1024, 2);
        }
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
