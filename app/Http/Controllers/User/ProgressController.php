<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserProgress;
use App\Models\Skill;
use App\Models\Level;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $overallStats = $this->getOverallStats($user);
        $skillsProgress = $this->getSkillsProgress($user);
        $levelsProgress = $this->getLevelsProgress($user);
        $recentActivity = $this->getRecentActivity($user);
        $pointsHistory = $this->getPointsHistory($user);
        $skillMastery = $this->getSkillMastery($user);
        $recommendations = $this->getRecommendations($user);

        return view('user.progress.index', compact(
            'overallStats',
            'skillsProgress',
            'levelsProgress',
            'recentActivity',
            'pointsHistory',
            'skillMastery',
            'recommendations'
        ));
    }

    private function getOverallStats($user)
    {
        $progress = UserProgress::where('user_id', $user->id)
            ->where('status', '!=', 'not_started')
            ->get();

        $totalPoints = (int) $progress->sum('points_earned');
        $totalSkillsStarted = $progress->count();
        $completedSkills = $progress->where('status', 'completed')->count();
        $totalAvailableSkills = Skill::count();

        $avgCompletion = $progress->count() > 0
            ? round($progress->avg('completion_percentage'), 1)
            : 0;

        $totalTimeSpent = (int) $progress->sum('time_spent_minutes');
        $hoursSpent = floor($totalTimeSpent / 60);
        $minutesSpent = $totalTimeSpent % 60;

        $questionsMastered = 0;
        $totalQuestions = 0;

        foreach ($progress as $item) {
            $itemTotalQuestions = (int) $item->total_questions_in_skill;
            $itemQuestionsMastered = min((int) $item->questions_answered, $itemTotalQuestions);

            $questionsMastered += $itemQuestionsMastered;
            $totalQuestions += $itemTotalQuestions;
        }

        $masteryRate = $totalQuestions > 0
            ? round(($questionsMastered / $totalQuestions) * 100, 1)
            : 0;

        return (object) [
            'total_points' => $totalPoints,
            'skills_started' => $totalSkillsStarted,
            'completed_skills' => $completedSkills,
            'total_skills' => $totalAvailableSkills,
            'completion_rate' => $totalAvailableSkills > 0
                ? round(($completedSkills / $totalAvailableSkills) * 100, 1)
                : 0,
            'avg_completion' => $avgCompletion,
            'total_time_spent' => $totalTimeSpent,
            'hours_spent' => $hoursSpent,
            'minutes_spent' => $minutesSpent,
            'questions_mastered' => $questionsMastered,
            'total_questions' => $totalQuestions,
            'mastery_rate' => min(100, $masteryRate),
        ];
    }

    private function getSkillsProgress($user)
    {
        return UserProgress::where('user_id', $user->id)
            ->where('status', '!=', 'not_started')
            ->with(['skill', 'level'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($progress) {
                $totalQuestions = (int) $progress->total_questions_in_skill;
                $questionsMastered = min((int) $progress->questions_answered, $totalQuestions);

                $mastery = $totalQuestions > 0
                    ? round(($questionsMastered / $totalQuestions) * 100, 1)
                    : 0;

                $completion = min(100, (float) $progress->completion_percentage);

                return (object) [
                    'progress_id' => $progress->progress_id,
                    'skill_id' => $progress->skill_id,
                    'skill_name' => $progress->skill->skill_name ?? 'Unknown Skill',
                    'level_id' => $progress->level_id,
                    'level_name' => $progress->level->level_name ?? 'No Level',
                    'completion' => $completion,
                    'points' => (int) $progress->points_earned,
                    'status' => $progress->status,
                    'videos_watched' => min((int) $progress->videos_watched, (int) $progress->total_videos_in_skill),
                    'total_videos' => (int) $progress->total_videos_in_skill,
                    'questions_answered' => $questionsMastered,
                    'total_questions' => $totalQuestions,
                    'mastery' => min(100, $mastery),
                    'time_spent_minutes' => (int) $progress->time_spent_minutes,
                    'updated_at' => $progress->updated_at,
                ];
            });
    }

    private function getLevelsProgress($user)
    {
        $levels = Level::with('skills')->orderBy('level_order')->get();

        return $levels->map(function ($level) use ($user) {
            $levelSkills = $level->skills;
            $totalSkillsInLevel = $levelSkills->count();

            $progressRecords = UserProgress::where('user_id', $user->id)
                ->where('level_id', $level->level_id)
                ->get()
                ->keyBy('skill_id');

            $completedSkills = 0;
            $inProgressSkills = 0;
            $totalPoints = 0;
            $questionsMastered = 0;
            $totalQuestions = 0;
            $totalTimeSpent = 0;
            $sumCompletion = 0;

            foreach ($levelSkills as $skill) {
                $progress = $progressRecords->get($skill->skill_id);

                if ($progress) {
                    $completion = min(100, (float) $progress->completion_percentage);
                    $sumCompletion += $completion;

                    $totalPoints += (int) $progress->points_earned;

                    $skillTotalQuestions = (int) $progress->total_questions_in_skill;
                    $skillQuestionsMastered = min((int) $progress->questions_answered, $skillTotalQuestions);

                    $questionsMastered += $skillQuestionsMastered;
                    $totalQuestions += $skillTotalQuestions;
                    $totalTimeSpent += (int) $progress->time_spent_minutes;

                    if ($progress->status === 'completed') {
                        $completedSkills++;
                    } elseif ($progress->status === 'in_progress') {
                        $inProgressSkills++;
                    }
                } else {
                    $sumCompletion += 0;
                }
            }

            $completion = $totalSkillsInLevel > 0
                ? round($sumCompletion / $totalSkillsInLevel, 1)
                : 0;

            $mastery = $totalQuestions > 0
                ? round(($questionsMastered / $totalQuestions) * 100, 1)
                : 0;

            return (object) [
                'level_id' => $level->level_id,
                'level_name' => $level->level_name,
                'level_order' => $level->level_order,
                'total_skills' => $totalSkillsInLevel,
                'completed_skills' => $completedSkills,
                'in_progress_skills' => $inProgressSkills,
                'tracked_skills' => $completedSkills + $inProgressSkills,
                'completion' => min(100, $completion),
                'points' => $totalPoints,
                'mastery' => min(100, $mastery),
                'questions_mastered' => $questionsMastered,
                'total_questions' => $totalQuestions,
                'time_spent_minutes' => $totalTimeSpent,
            ];
        });
    }

    private function getRecentActivity($user)
    {
        return UserProgress::where('user_id', $user->id)
            ->where('status', '!=', 'not_started')
            ->with(['skill', 'level'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(function ($progress) {
                $skillName = $progress->skill->skill_name ?? 'Unknown Skill';
                $levelName = $progress->level->level_name ?? 'No Level';

                $totalQuestions = (int) $progress->total_questions_in_skill;
                $questionsMastered = min((int) $progress->questions_answered, $totalQuestions);

                if ($progress->status === 'completed' && $progress->completed_at) {
                    $type = 'completed';
                    $message = "Completed {$skillName}";
                    $date = $progress->completed_at;
                } elseif ($progress->status === 'in_progress' && $progress->started_at) {
                    $type = 'started';
                    $message = "Working on {$skillName}";
                    $date = $progress->started_at;
                } else {
                    $type = 'updated';
                    $message = "Updated progress in {$skillName}";
                    $date = $progress->updated_at;
                }

                return (object) [
                    'type' => $type,
                    'message' => $message,
                    'level_name' => $levelName,
                    'date' => $date,
                    'points' => (int) $progress->points_earned,
                    'completion' => min(100, (float) $progress->completion_percentage),
                    'questions_mastered' => $questionsMastered,
                    'total_questions' => $totalQuestions,
                    'time_spent_minutes' => (int) $progress->time_spent_minutes,
                ];
            });
    }

    private function getPointsHistory($user)
    {
        $pointsData = UserProgress::where('user_id', $user->id)
            ->where('status', '!=', 'not_started')
            ->where('updated_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('SUM(points_earned) as daily_points')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dates = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $points = $pointsData->firstWhere('date', $date);

            $dates->push((object) [
                'date' => $date,
                'points' => $points ? (int) $points->daily_points : 0
            ]);
        }

        return $dates;
    }

    private function getSkillMastery($user)
    {
        return UserProgress::where('user_id', $user->id)
            ->where('status', '!=', 'not_started')
            ->with(['skill', 'level'])
            ->orderByDesc('completion_percentage')
            ->get()
            ->map(function ($progress) {
                $totalQuestions = (int) $progress->total_questions_in_skill;
                $questionsMastered = min((int) $progress->questions_answered, $totalQuestions);

                $mastery = $totalQuestions > 0
                    ? round(($questionsMastered / $totalQuestions) * 100, 1)
                    : 0;

                return (object) [
                    'skill_name' => ($progress->skill->skill_name ?? 'Unknown') . ' - ' . ($progress->level->level_name ?? 'No Level'),
                    'completion' => min(100, (float) $progress->completion_percentage),
                    'points' => (int) $progress->points_earned,
                    'status' => $progress->status,
                    'mastery' => min(100, $mastery)
                ];
            });
    }

    private function getRecommendations($user)
    {
        $recommendations = [];

        $incompleteSkill = UserProgress::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->where('completion_percentage', '<', 100)
            ->with(['skill', 'level'])
            ->orderByDesc('completion_percentage')
            ->first();

        if ($incompleteSkill) {
            $recommendations[] = (object) [
                'type' => 'continue',
                'title' => 'Continue Learning',
                'message' => 'Continue ' . ($incompleteSkill->skill->skill_name ?? 'this skill') . ' in ' . ($incompleteSkill->level->level_name ?? 'this level') . '.',
                'action_url' => route('user.skills.show', $incompleteSkill->skill_id),
                'icon' => 'play-circle'
            ];
        }

        $latestCompleted = UserProgress::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['skill', 'level'])
            ->orderByDesc('completed_at')
            ->first();

        if ($latestCompleted) {
            $recommendations[] = (object) [
                'type' => 'review',
                'title' => 'Review Your Progress',
                'message' => 'Great job in ' . ($latestCompleted->skill->skill_name ?? 'your last skill') . ' (' . ($latestCompleted->level->level_name ?? 'No Level') . '). Keep going.',
                'action_url' => route('user.skills.show', $latestCompleted->skill_id),
                'icon' => 'chart-line'
            ];
        }

        return collect($recommendations);
    }
}