<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\UserProgress;
use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;
use App\Models\Question;
use App\Models\Skill;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with('level')
            ->withCount(['progress as total_points' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(points_earned), 0)'));
            }])
            ->withCount(['chatbotSessions as last_activity' => function ($query) {
                $query->select(DB::raw('MAX(last_msg_at)'));
            }]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Level filter
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        // Date range filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'points_high':
                $query->orderBy('total_points', 'desc');
                break;
            case 'points_low':
                $query->orderBy('total_points', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(15);

        // Get statistics
        $totalUsers = User::count();
        $adminCount = User::where('role', 1)->count();
        $newThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $activeUsers = User::whereHas('chatbotSessions', function ($query) {
            $query->where('last_msg_at', '>=', Carbon::now()->subDays(7));
        })->count();

        $levels = Level::orderBy('level_order')->get();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminCount',
            'newThisMonth',
            'levels'
        ));
    }


    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $levels = Level::orderBy('level_order')->get();
        return view('admin.users.form', compact('levels'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:0,1',
            'level_id' => 'nullable|exists:levels,level_id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['level', 'progress.skill', 'progress.level']);

        // Get user statistics
        $totalPoints = UserProgress::where('user_id', $user->id)->sum('points_earned');
        $completedSkills = UserProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $inProgressSkills = UserProgress::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();
        $totalTimeSpent = UserProgress::where('user_id', $user->id)
            ->sum('time_spent_minutes');

        // Get recent progress
        $recentProgress = UserProgress::with(['skill', 'level'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get activity by day for chart
        $activityData = UserProgress::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as activities'),
                DB::raw('SUM(points_earned) as points')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get chat sessions count for header
        $chatSessionsCount = ChatbotSession::where('user_id', $user->id)->count();

        // Get recent sessions for chat tab
        $recentSessions = ChatbotSession::withCount('messages')
            ->where('user_id', $user->id)
            ->orderBy('last_msg_at', 'desc')
            ->limit(5)
            ->get();

        // Get completed videos - FIXED
        $completedVideos = Video::whereIn('skill_id', function ($query) use ($user) {
            $query->select('skill_id')
                ->from('user_progress')
                ->where('user_id', $user->id)
                ->where('videos_watched', '>', 0);
        })->get();

        // Get completed questions - FIXED
        $completedQuestions = Question::whereIn('question_id', function ($query) use ($user) {
            $query->select('question_id')
                ->from('user_progress')
                ->where('user_id', $user->id)
                ->where('correct_answers', '>', 0);
        })->get();

        // Get activities for activity tab
        $activities = UserProgress::with(['skill', 'level'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->description = $item->skill
                    ? "Progress updated in {$item->skill->skill_name} - {$item->completion_percentage}% complete"
                    : "Learning activity - {$item->points_earned} points earned";
                return $item;
            });

        // Get next level
        $nextLevel = Level::where('level_order', '>', optional($user->level)->level_order ?? 0)
            ->orderBy('level_order')
            ->first();

        $allLevels = Level::with('skills')
            ->orderBy('level_order')
            ->get();

        return view('admin.users.show', compact(
            'user',
            'totalPoints',
            'completedSkills',
            'inProgressSkills',
            'totalTimeSpent',
            'recentProgress',
            'activityData',
            'chatSessionsCount',
            'completedVideos',
            'completedQuestions',
            'recentSessions',
            'activities',
            'nextLevel',
            'allLevels'
        ));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $levels = Level::orderBy('level_order')->get();
        $totalPoints = UserProgress::where('user_id', $user->id)->sum('points_earned');
        $completedSkills = UserProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $inProgressSkills = UserProgress::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        return view('admin.users.form', compact('user', 'levels', 'totalPoints', 'completedSkills', 'inProgressSkills'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:0,1',
            'level_id' => 'nullable|exists:levels,level_id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->progress()->exists() || $user->chatbotSessions()->exists()) {
            return back()->with('error', 'Cannot delete user with associated progress or chat sessions.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Display user progress.
     */
 /**
 * Display user progress for admin review.
 */
public function progress(User $user)
{
    // 1. Get paginated detailed progress data for the table
    $progress = UserProgress::with(['skill', 'level'])
        ->where('user_id', $user->id)
        ->orderBy('updated_at', 'desc')
        ->paginate(20);

    // 2. Get recent activity for the timeline
    $recentProgress = UserProgress::with(['skill', 'level'])
        ->where('user_id', $user->id)
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    // 3. Fetch all progress records for statistical calculation
    $userProgressCollection = UserProgress::where('user_id', $user->id)->get();

    // 4. Calculate Raw Data
    $totalPoints = (int) $userProgressCollection->sum('points_earned');
    $totalTimeMinutes = (int) $userProgressCollection->sum('time_spent_minutes');
    
    // mastery_count is based on questions answered correctly (questions_answered)
    $questionsMastered = (int) $userProgressCollection->sum('questions_answered');
    
    // total_possible is the sum of questions available in the skills they have started
    $totalPossibleQuestions = (int) $userProgressCollection->sum('total_questions_in_skill');

    // 5. Time Fallback Logic
    // If time is 0 but user has activity, assume 2 mins per active skill record
    if ($totalTimeMinutes === 0 && ($totalPoints > 0 || $questionsMastered > 0)) {
        $activeSkillCount = $userProgressCollection->where('status', '!=', 'not_started')->count();
        $totalTimeMinutes = max(1, $activeSkillCount * 2);
    }

    // 6. Build the Stats Array
    $stats = [
        'total_points' => $totalPoints,
        'completed_skills' => $userProgressCollection->where('status', 'completed')->count(),
        'in_progress_skills' => $userProgressCollection->where('status', 'in_progress')->count(),
        'videos_watched' => (int) $userProgressCollection->sum('videos_watched'),
        'total_videos_count' => (int) $userProgressCollection->sum('total_videos_in_skill'),
        'questions_mastered' => $questionsMastered,
        'total_questions' => $totalPossibleQuestions,
        'total_time' => $totalTimeMinutes,
        'total_skills_in_system' => \App\Models\Skill::count(),
    ];

    // 7. Calculate Mastery Rate (%)
    // Formula: (Correct Questions / Total Questions in Skills Started) * 100
    $stats['mastery_rate'] = $totalPossibleQuestions > 0
        ? round(($questionsMastered / $totalPossibleQuestions) * 100, 1)
        : 0;

    // 8. Calculate Daily Average
    $daysSinceJoined = max(1, $user->created_at->diffInDays(now()));
    $stats['daily_average'] = round($totalTimeMinutes / $daysSinceJoined, 1);

    return view('admin.users.progress', compact('user', 'progress', 'recentProgress', 'stats'));
}

    /**
     * Bulk delete users.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Prevent deleting yourself
        if (in_array(Auth::id(), $request->user_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        $count = User::whereIn('id', $request->user_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} user(s) deleted successfully."
        ]);
    }

    /**
     * Bulk update user roles.
     */
    public function bulkUpdateRole(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|in:0,1'
        ]);

        // Prevent changing your own role
        if (in_array(Auth::id(), $request->user_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role.'
            ], 400);
        }

        $count = User::whereIn('id', $request->user_ids)
            ->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => "{$count} user(s) updated successfully."
        ]);
    }

   /**
 * Export users data 
 */
public function export(Request $request)
{
    $query = User::with(['level', 'progress']);

    if ($request->has('user_ids')) {
        $query->whereIn('id', $request->user_ids);
    }

    $users = $query->get();

    $filename = 'users-export-' . Carbon::now()->format('Y-m-d') . '.csv';
    $handle = fopen('php://temp', 'w+');

    fwrite($handle, "\xEF\xBB\xBF");

    // Simplified headers
    $headers = [
        'ID',
        'Name',
        'Email',
        'Role',
        'Current Level',
        'Joined Date',
        'Total Points',
        'Completed Skills',
        'In Progress Skills',
        'Videos Watched',
        'Questions Answered',
        'Correct Answers',
        'Accuracy Rate (%)',
        'Total Time (mins)',
        'Last Activity',
    ];

    fputcsv($handle, $headers);

    foreach ($users as $user) {
        $totalPoints = $user->progress->sum('points_earned');
        $completedSkills = $user->progress->where('status', 'completed')->count();
        $inProgressSkills = $user->progress->where('status', 'in_progress')->count();
        $videosWatched = $user->progress->sum('videos_watched');
        $questionsAnswered = $user->progress->sum('questions_answered');
        $correctAnswers = $user->progress->sum('correct_answers');
        $accuracyRate = $questionsAnswered > 0 ? round(($correctAnswers / $questionsAnswered) * 100, 2) : 0;
        $totalTimeSpent = $user->progress->sum('time_spent_minutes');
        $lastActivity = $user->progress->max('updated_at')?->format('Y-m-d H:i:s') ?? 'Never';

        $rowData = [
            $user->id,
            $user->name,
            $user->email,
            $user->isAdmin() ? 'Admin' : 'User',
            $user->level?->level_name ?? 'Not assigned',
            $user->created_at->format('Y-m-d H:i:s'),
            $totalPoints,
            $completedSkills,
            $inProgressSkills,
            $videosWatched,
            $questionsAnswered,
            $correctAnswers,
            $accuracyRate,
            $totalTimeSpent,
            $lastActivity,
        ];

        fputcsv($handle, $rowData);
    }

    rewind($handle);
    $content = stream_get_contents($handle);
    fclose($handle);

    return response($content)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

    /**
     * Calculate user's accuracy rate.
     */
    private function calculateAccuracyRate($userId)
    {
        $progress = UserProgress::where('user_id', $userId)
            ->select(
                DB::raw('SUM(questions_answered) as total_answered'),
                DB::raw('SUM(correct_answers) as total_correct')
            )
            ->first();

        if (!$progress->total_answered || $progress->total_answered == 0) {
            return 0;
        }

        return round(($progress->total_correct / $progress->total_answered) * 100, 2);
    }
}
