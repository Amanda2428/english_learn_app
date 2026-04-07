<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Video;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    /**
     * Display a listing of levels for authenticated users.
     */
    public function index()
    {
        $user = Auth::user();

        // Get all levels with their skills
        $levels = Level::with(['skills' => function ($query) {
            $query->where('status', true);
        }])->withCount('skills')->orderBy('level_order')->get();

        // Get user's progress across levels
        $userProgress = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->level_id . '-' . $item->skill_id;
            });

        // Calculate stats for header
        $totalSkills = $levels->sum(function ($level) {
            return $level->skills->count();
        });

        $completedLevels = 0;
        $inProgress = 0;

        foreach ($levels as $level) {
            $completedSkills = $userProgress
                ->filter(function ($progress) use ($level) {
                    return ($progress->level_id ?? null) == $level->level_id && $progress->status === 'completed';
                })->count();

            if ($completedSkills == $level->skills->count() && $level->skills->count() > 0) {
                $completedLevels++;
            } elseif ($completedSkills > 0) {
                $inProgress++;
            }
        }

        $totalLessons = $levels->sum(function ($level) {
            return $level->skills->sum(function ($skill) {
                return $skill->videos()->count();
            });
        });

        return view('user.levels.index', compact(
            'levels',
            'userProgress',
            'totalLessons',
            'completedLevels',
            'inProgress',
            'totalSkills'
        ));
    }

    /**
     * Display the specified level for authenticated users.
     */
  public function show(Level $level)
    {
        $user = Auth::user();
        
        if ($user && $user->level_id != $level->level_id) {
            User::where('id', $user->id)->update([
                'level_id' => $level->level_id
            ]);
            // Refresh the user object
            $user = User::find($user->id);
        }
    
        
        // Load skills with their related data
        $level->load(['skills' => function($query) {
            $query->where('status', true)
                  ->withCount(['videos', 'questions']);
        }]);
        
        // Get user's progress for skills in this level
        $skillIds = $level->skills->pluck('skill_id')->toArray();
        $userProgress = UserProgress::where('user_id', $user->id)
            ->whereIn('skill_id', $skillIds)
            ->get()
            ->keyBy('skill_id');
        
        // Calculate level progress
        $totalSkills = $level->skills->count();
        $completedSkills = $userProgress->where('status', 'completed')->count();
        $levelProgress = $totalSkills > 0 ? round(($completedSkills / $totalSkills) * 100) : 0;
        
        // Get next and previous levels
        $nextLevel = Level::where('level_order', '>', $level->level_order)
            ->orderBy('level_order')
            ->first();
            
        $prevLevel = Level::where('level_order', '<', $level->level_order)
            ->orderBy('level_order', 'desc')
            ->first();
        
        return view('user.levels.show', compact('level', 'userProgress', 'levelProgress', 'nextLevel', 'prevLevel'));
    }

    /**
     * Select/activate a level for the user
     */
    public function select(Request $request, Level $level)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $updated = User::where('id', $user->id)->update([
                'level_id' => $level->level_id
            ]);

            if (!$updated) {
                throw new \Exception('Failed to update user level');
            }

            // Refresh the user object
            $user = User::find($user->id);

            foreach ($level->skills as $skill) {
                UserProgress::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'level_id' => $level->level_id,
                        'skill_id' => $skill->skill_id,
                    ],
                    [
                        'status' => 'not_started',
                        'points_earned' => 0,
                        'completion_percentage' => 0,
                        'videos_watched' => 0,
                        'total_videos_in_skill' => $skill->videos()->count(),
                        'questions_answered' => 0,
                        'correct_answers' => 0,
                        'total_questions_in_skill' => $skill->questions()->count(),
                        'time_spent_minutes' => 0,
                        'started_at' => null,
                        'completed_at' => null
                    ]
                );
            }

            DB::commit();

            return redirect()->route('user.levels.show', $level)
                ->with('success', '🎉 You have selected the ' . $level->level_name . ' level. Start learning now!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Get next level in sequence
     */
    public function nextLevel(Level $level)
    {
        $nextLevel = Level::where('level_order', '>', $level->level_order)
            ->orderBy('level_order')
            ->first();

        if ($nextLevel) {
            return redirect()->route('user.levels.show', $nextLevel);
        }

        return redirect()->route('user.levels.index')
            ->with('info', 'You have completed all levels! Congratulations! 🎉');
    }
}
