<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\UserProgress;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Video;

class SkillController extends Controller
{
    /**
     * Display all skills in index page
     */
    public function index()
    {
        $user = Auth::user();

        $skills = Skill::with(['levels'])
            ->withCount(['videos', 'questions'])
            ->orderBy('skill_name')
            ->get()
            ->map(function ($skill) use ($user) {
                $progressRecords = UserProgress::where('user_id', $user->id)
                    ->where('skill_id', $skill->skill_id)
                    ->where('status', '!=', 'not_started')
                    ->get();

                $completedLevels = $progressRecords->where('status', 'completed')->count();
                $inProgressLevels = $progressRecords->where('status', 'in_progress')->count();
                $totalPoints = (int) $progressRecords->sum('points_earned');
                $avgCompletion = $progressRecords->count() > 0
                    ? round($progressRecords->avg('completion_percentage'), 1)
                    : 0;

                $totalQuestions = 0;
                $questionsMastered = 0;

                foreach ($progressRecords as $progress) {
                    $safeTotalQuestions = max(0, (int) $progress->total_questions_in_skill);
                    $safeQuestionsAnswered = min(
                        max(0, (int) $progress->questions_answered),
                        $safeTotalQuestions
                    );

                    $totalQuestions += $safeTotalQuestions;
                    $questionsMastered += $safeQuestionsAnswered;
                }

                $mastery = $totalQuestions > 0
                    ? round(($questionsMastered / $totalQuestions) * 100, 1)
                    : 0;

                $skill->completed_levels_count = $completedLevels;
                $skill->in_progress_levels_count = $inProgressLevels;
                $skill->total_points = $totalPoints;
                $skill->avg_completion = min(100, max(0, $avgCompletion));
                $skill->questions_mastered = $questionsMastered;
                $skill->mastery = min(100, max(0, $mastery));
                $skill->has_progress = $progressRecords->count() > 0;

                return $skill;
            });

        return view('user.skills.index', compact('skills'));
    }

    /**
     * Display the skill show page with questions filtered by level
     */
    public function show(Skill $skill, Request $request)
    {
        $user = Auth::user();
        $levelId = $request->query('level', $user->level_id);

        $skill->load(['videos', 'levels']);

        $selectedLevel = null;
        if ($levelId) {
            $selectedLevel = Level::find($levelId);
            $levelExists = $skill->levels->contains('level_id', $levelId);

            if (!$levelExists) {
                $levelId = null;
                $selectedLevel = null;
            }
        }

        $questions = $skill->questions()
            ->with(['answers', 'video', 'level'])
            ->when($levelId, function ($query) use ($levelId) {
                return $query->where('level_id', $levelId);
            })
            ->orderBy('question_id')
            ->get();

        $progress = null;

        if ($levelId) {
            $questionCount = $questions->count();
            $videoCount = $skill->videos->count();

            $progress = UserProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'skill_id' => $skill->skill_id,
                    'level_id' => $levelId,
                ],
                [
                    'status' => 'in_progress',
                    'total_videos_in_skill' => $videoCount,
                    'total_questions_in_skill' => $questionCount,
                    'completed_questions' => [],
                    'points_earned' => 0,
                    'questions_answered' => 0,
                    'videos_watched' => 0,
                    'completion_percentage' => 0,
                    'started_at' => now(),
                ]
            );

            // Always sync totals with real counts
            $progress->total_videos_in_skill = $videoCount;
            $progress->total_questions_in_skill = $questionCount;

            // Clamp stored values
            $progress->videos_watched = min(
                max(0, (int) $progress->videos_watched),
                $videoCount
            );

            $progress->questions_answered = min(
                max(0, (int) $progress->questions_answered),
                $questionCount
            );

            $progress->completion_percentage = min(
                100,
                max(0, (float) $progress->completion_percentage)
            );

            $progress->save();

            // Add computed safe mastery for blade
            $progress->mastery_rate = $questionCount > 0
                ? round(($progress->questions_answered / $questionCount) * 100, 1)
                : 0;
        }

        $availableLevels = $skill->levels()
            ->orderBy('level_order')
            ->get()
            ->map(function ($level) use ($skill) {
                $level->question_count = $skill->questions()
                    ->where('level_id', $level->level_id)
                    ->count();

                return $level;
            });

        return view('user.skills.show', compact(
            'skill',
            'questions',
            'progress',
            'selectedLevel',
            'availableLevels',
            'levelId'
        ));
    }

    public function selectLevel(Skill $skill)
    {
        $user = Auth::user();

        $levels = $skill->levels()
            ->orderBy('level_order')
            ->get();

        $userProgress = UserProgress::where('user_id', $user->id)
            ->where('skill_id', $skill->skill_id)
            ->get()
            ->keyBy('level_id');

        foreach ($levels as $level) {
            $progress = $userProgress->get($level->level_id);

            $questionsCount = $skill->questions()
                ->where('level_id', $level->level_id)
                ->count();

            $videosCount = $skill->videos->count();

            if ($progress) {
                $safeTotalQuestions = max(0, (int) ($progress->total_questions_in_skill ?? $questionsCount));
                $safeTotalVideos = max(0, (int) ($progress->total_videos_in_skill ?? $videosCount));

                $level->completion_percentage = min(100, max(0, (float) ($progress->completion_percentage ?? 0)));
                $level->status = $progress->status;
                $level->points_earned = max(0, (int) ($progress->points_earned ?? 0));
                $level->questions_answered = min(max(0, (int) ($progress->questions_answered ?? 0)), $safeTotalQuestions);
                $level->total_questions = $safeTotalQuestions;
                $level->videos_watched = min(max(0, (int) ($progress->videos_watched ?? 0)), $safeTotalVideos);
                $level->total_videos = $safeTotalVideos;
            } else {
                $level->completion_percentage = 0;
                $level->status = 'not_started';
                $level->points_earned = 0;
                $level->questions_answered = 0;
                $level->total_questions = $questionsCount;
                $level->videos_watched = 0;
                $level->total_videos = $videosCount;
            }
        }

        return view('user.skills.select-level', compact('skill', 'levels'));
    }

    public function startPractice(Request $request, Skill $skill)
    {
        $user = Auth::user();
        $levelId = $request->level_id;

        $skill->load('levels', 'videos');

        $levelExists = $skill->levels->contains('level_id', $levelId);

        if (!$levelExists) {
            return redirect()->route('user.skills.select-level', $skill)
                ->with('error', 'Invalid level selected.');
        }

        User::where('id', $user->id)->update(['level_id' => $levelId]);
        $user = User::find($user->id);

        $questionsCount = $skill->questions()
            ->where('level_id', $levelId)
            ->count();

        $level = Level::find($levelId);

        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
                'level_id' => $levelId,
            ],
            [
                'status' => 'in_progress',
                'total_videos_in_skill' => $skill->videos->count(),
                'total_questions_in_skill' => $questionsCount,
                'completed_questions' => [],
                'points_earned' => 0,
                'questions_answered' => 0,
                'videos_watched' => 0,
                'completion_percentage' => 0,
                'started_at' => now(),
            ]
        );

        if ($progress->status === 'not_started') {
            $progress->status = 'in_progress';
            $progress->started_at = now();
            $progress->save();
        }

        return redirect()->route('user.skills.show', [
            'skill' => $skill->skill_id,
            'level' => $levelId
        ])->with('success', "Starting {$level->level_name} level practice for {$skill->skill_name}!");
    }

    public function practice(Skill $skill, Request $request)
    {
        $levelId = $request->query('level');
        $questionId = $request->query('question');
        $type = $request->query('type');

        $user = Auth::user();

        $skill->load('levels', 'videos');

        if ($levelId) {
            $levelExists = $skill->levels->contains('level_id', $levelId);

            if (!$levelExists) {
                return redirect()->route('user.skills.select-level', $skill)
                    ->with('error', 'Invalid level selected.');
            }

            User::where('id', $user->id)->update(['level_id' => $levelId]);
            $user = User::find($user->id);

            $questionsCount = $skill->questions()
                ->where('level_id', $levelId)
                ->count();

            $progress = UserProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'skill_id' => $skill->skill_id,
                    'level_id' => $levelId,
                ],
                [
                    'status' => 'in_progress',
                    'total_videos_in_skill' => $skill->videos->count(),
                    'total_questions_in_skill' => $questionsCount,
                    'completed_questions' => [],
                    'points_earned' => 0,
                    'questions_answered' => 0,
                    'videos_watched' => 0,
                    'completion_percentage' => 0,
                    'started_at' => now(),
                ]
            );
        } else {
            $progress = UserProgress::where('user_id', $user->id)
                ->where('skill_id', $skill->skill_id)
                ->where('level_id', $user->level_id)
                ->first();

            if (!$progress) {
                return redirect()->route('user.skills.select-level', $skill)
                    ->with('info', 'Please select a level to start practicing.');
            }

            $levelId = $user->level_id;
        }

        $query = $skill->questions()
            ->with(['answers', 'video'])
            ->where('level_id', $levelId);

        if ($questionId) {
            $questions = $query->where('question_id', $questionId)->get();
        } elseif ($type) {
            $questions = $query->where('question_type', $type)->get();
        } else {
            $questions = $query->get();
        }

        if ($questions->isEmpty()) {
            return redirect()->route('user.skills.select-level', $skill)
                ->with('error', 'No questions found for this level. Please try another level.');
        }

        $level = Level::find($levelId);

        return view('user.skills.practice', compact(
            'skill',
            'questions',
            'progress',
            'level',
            'user',
            'levelId'
        ));
    }

    public function submitPractice(Request $request, Skill $skill)
    {
        $user = Auth::user();
        $levelId = $request->input('level_id', $user->level_id);
        $submittedAnswers = $request->input('answers', []);
        $incorrectQuestionIds = [];
        $totalNewPoints = 0;
        $newlyWatchedVideos = [];

        $skill->load(['levels', 'videos']);

        $levelExists = $skill->levels->contains('level_id', $levelId);

        if (!$levelExists) {
            return redirect()->route('user.skills.select-level', $skill)
                ->with('error', 'Invalid level selected.');
        }

        $realQuestionCount = $skill->questions()->where('level_id', $levelId)->count();
        $realVideoCount = $skill->videos->count();

        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
                'level_id' => $levelId,
            ],
            [
                'status' => 'in_progress',
                'total_videos_in_skill' => $realVideoCount,
                'total_questions_in_skill' => $realQuestionCount,
                'completed_questions' => [],
                'points_earned' => 0,
                'questions_answered' => 0,
                'videos_watched' => 0,
                'completion_percentage' => 0,
                'started_at' => now(),
            ]
        );

        $completedIds = is_array($progress->completed_questions)
            ? array_map('intval', $progress->completed_questions)
            : [];

        $questions = $skill->questions()->where('level_id', $levelId)->get();
        $videoQuestionMap = [];

        foreach ($questions as $question) {
            if ($question->video_id) {
                $videoQuestionMap[$question->question_id] = $question->video_id;
            }
        }

        foreach ($submittedAnswers as $questionId => $answerData) {
            $question = Question::find($questionId);

            if (!$question || $question->skill_id != $skill->skill_id || $question->level_id != $levelId) {
                continue;
            }

            $correctAnswerIds = Answer::where('question_id', $questionId)
                ->where('is_correct', true)
                ->pluck('answer_id')
                ->toArray();

            $userAnswerIds = is_array($answerData)
                ? array_map('intval', $answerData)
                : [(int) $answerData];

            sort($correctAnswerIds);
            sort($userAnswerIds);

            if ($correctAnswerIds !== $userAnswerIds) {
                $incorrectQuestionIds[] = $questionId;
            } else {
                if (!in_array((int) $questionId, $completedIds)) {
                    $completedIds[] = (int) $questionId;
                    $totalNewPoints += (int) ($question->points ?? 0);

                    if (isset($videoQuestionMap[$questionId])) {
                        $newlyWatchedVideos[] = $videoQuestionMap[$questionId];
                    }
                }
            }
        }

        if (!empty($incorrectQuestionIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Incorrect answers! Please try again.')
                ->with('incorrect_questions', $incorrectQuestionIds);
        }

        $sessionKey = "watched_video_ids_{$skill->skill_id}_{$levelId}";
        $watchedVideoIds = session()->get($sessionKey, []);
        $videosToAdd = 0;

        foreach ($newlyWatchedVideos as $videoId) {
            if (!in_array($videoId, $watchedVideoIds)) {
                $watchedVideoIds[] = $videoId;
                $videosToAdd++;
            }
        }

        if ($videosToAdd > 0) {
            session()->put($sessionKey, $watchedVideoIds);
        }

        $timeSpentSeconds = (int) $request->input('time_spent', 0);

        $progress->completed_questions = array_values(array_unique($completedIds));
        $progress->points_earned = max(0, (int) $progress->points_earned + $totalNewPoints);

        $progress->total_videos_in_skill = $realVideoCount;
        $progress->total_questions_in_skill = $realQuestionCount;

        $progress->questions_answered = min(
            count($progress->completed_questions),
            $realQuestionCount
        );

        $progress->videos_watched = min(
            max(0, (int) $progress->videos_watched + $videosToAdd),
            $realVideoCount
        );

        $progress->time_spent_minutes = max(
            0,
            (int) $progress->time_spent_minutes + max(1, round($timeSpentSeconds / 60))
        );

        $videoPercentage = $realVideoCount > 0
            ? ($progress->videos_watched / $realVideoCount) * 100
            : 0;

        $questionPercentage = $realQuestionCount > 0
            ? ($progress->questions_answered / $realQuestionCount) * 100
            : 0;

        if ($realVideoCount > 0 && $realQuestionCount > 0) {
            $progress->completion_percentage = round(($videoPercentage + $questionPercentage) / 2, 1);
        } elseif ($realQuestionCount > 0) {
            $progress->completion_percentage = round($questionPercentage, 1);
        } elseif ($realVideoCount > 0) {
            $progress->completion_percentage = round($videoPercentage, 1);
        } else {
            $progress->completion_percentage = 0;
        }

        $progress->completion_percentage = min(100, max(0, $progress->completion_percentage));
        $progress->status = ($progress->completion_percentage >= 100) ? 'completed' : 'in_progress';

        if ($progress->status === 'completed' && !$progress->completed_at) {
            $progress->completed_at = now();
        }

        $progress->save();

        $level = Level::find($levelId);

        return redirect()->route('user.skills.show', [
            'skill' => $skill->skill_id,
            'level' => $levelId
        ])->with('success', "Perfect! You earned {$totalNewPoints} points for the {$level->level_name} level!");
    }

    public function getLevelsForSkill(Skill $skill)
    {
        try {
            $skill->load('videos');

            $levels = $skill->levels()
                ->orderBy('level_order')
                ->get();

            $user = Auth::user();

            $userProgress = UserProgress::where('user_id', $user->id)
                ->where('skill_id', $skill->skill_id)
                ->get()
                ->keyBy('level_id');

            $levelsWithProgress = $levels->map(function ($level) use ($userProgress, $skill) {
                $progress = $userProgress->get($level->level_id);

                $questionsCount = $skill->questions()
                    ->where('level_id', $level->level_id)
                    ->count();

                $videosCount = $skill->videos->count();
                $completionPercentage = 0;

                if ($progress) {
                    $safeVideosWatched = min(max(0, (int) $progress->videos_watched), $videosCount);
                    $safeQuestionsAnswered = min(max(0, (int) $progress->questions_answered), $questionsCount);

                    $videoPercentage = $videosCount > 0
                        ? ($safeVideosWatched / $videosCount) * 100
                        : 0;

                    $questionPercentage = $questionsCount > 0
                        ? ($safeQuestionsAnswered / $questionsCount) * 100
                        : 0;

                    if ($videosCount > 0 && $questionsCount > 0) {
                        $completionPercentage = round(($videoPercentage + $questionPercentage) / 2, 1);
                    } elseif ($questionsCount > 0) {
                        $completionPercentage = round($questionPercentage, 1);
                    } elseif ($videosCount > 0) {
                        $completionPercentage = round($videoPercentage, 1);
                    }
                }

                return [
                    'level_id' => $level->level_id,
                    'level_name' => $level->level_name,
                    'level_order' => $level->level_order,
                    'questions_count' => $questionsCount,
                    'progress' => min(100, max(0, $completionPercentage)),
                    'description' => $level->description,
                    'status' => $progress ? $progress->status : 'not_started',
                    'points_earned' => $progress ? max(0, (int) $progress->points_earned) : 0,
                    'videos_watched' => $progress ? min(max(0, (int) $progress->videos_watched), $videosCount) : 0,
                    'total_videos' => $videosCount,
                ];
            });

            return response()->json([
                'success' => true,
                'levels' => $levelsWithProgress
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching levels for skill: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load levels: ' . $e->getMessage()
            ], 500);
        }
    }

    public function trackVideoProgress(Request $request, Skill $skill, Video $video)
    {
        $user = Auth::user();
        $levelId = $request->input('level_id', $user->level_id);

        $skill->load('videos', 'levels');

        if ($video->skill_id != $skill->skill_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid video for this skill'
            ], 400);
        }

        $levelExists = $skill->levels->contains('level_id', $levelId);

        if (!$levelExists) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid level selected'
            ], 400);
        }

        $realVideoCount = $skill->videos->count();
        $realQuestionCount = $skill->questions()->where('level_id', $levelId)->count();

        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
                'level_id' => $levelId,
            ],
            [
                'status' => 'in_progress',
                'total_videos_in_skill' => $realVideoCount,
                'total_questions_in_skill' => $realQuestionCount,
                'completed_questions' => [],
                'points_earned' => 0,
                'questions_answered' => 0,
                'videos_watched' => 0,
                'completion_percentage' => 0,
                'started_at' => now(),
            ]
        );

        $progress->total_videos_in_skill = $realVideoCount;
        $progress->total_questions_in_skill = $realQuestionCount;
        $progress->videos_watched = min(
            max(0, (int) $progress->videos_watched + 1),
            $realVideoCount
        );
        $progress->questions_answered = min(
            max(0, (int) $progress->questions_answered),
            $realQuestionCount
        );

        $videoPercentage = $realVideoCount > 0
            ? ($progress->videos_watched / $realVideoCount) * 100
            : 0;

        $questionPercentage = $realQuestionCount > 0
            ? ($progress->questions_answered / $realQuestionCount) * 100
            : 0;

        if ($realVideoCount > 0 && $realQuestionCount > 0) {
            $progress->completion_percentage = round(($videoPercentage + $questionPercentage) / 2, 1);
        } elseif ($realQuestionCount > 0) {
            $progress->completion_percentage = round($questionPercentage, 1);
        } elseif ($realVideoCount > 0) {
            $progress->completion_percentage = round($videoPercentage, 1);
        } else {
            $progress->completion_percentage = 0;
        }

        $progress->completion_percentage = min(100, max(0, $progress->completion_percentage));
        $progress->save();

        return response()->json([
            'success' => true,
            'message' => 'Video confirmed as watched!',
            'videos_watched' => $progress->videos_watched,
            'total_videos' => $progress->total_videos_in_skill,
            'completion_percentage' => $progress->completion_percentage
        ]);
    }

    public function updateLevel(Request $request)
    {
        try {
            $user = Auth::user();
            $levelId = $request->level_id;

            User::where('id', $user->id)->update(['level_id' => $levelId]);

            return response()->json([
                'success' => true,
                'message' => 'Level updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user level: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update level: ' . $e->getMessage()
            ], 500);
        }
    }

    public function results(Skill $skill, Request $request)
    {
        $user = Auth::user();
        $levelId = $request->query('level', $user->level_id);

        $progress = UserProgress::where('user_id', $user->id)
            ->where('skill_id', $skill->skill_id)
            ->where('level_id', $levelId)
            ->first();

        if (!$progress) {
            return redirect()->route('user.skills.show', [
                'skill' => $skill->skill_id,
                'level' => $levelId
            ])->with('error', 'No progress found for this skill and level.');
        }

        $questions = $skill->questions()
            ->with('answers')
            ->where('level_id', $levelId)
            ->orderBy('question_id')
            ->get();

        $completedIds = is_array($progress->completed_questions)
            ? $progress->completed_questions
            : [];

        $questionDetails = [];
        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $question) {
            $isCorrect = in_array($question->question_id, $completedIds);

            if ($isCorrect) {
                $correctCount++;
            }

            $correctAnswer = $question->answers->firstWhere('is_correct', true);

            $questionDetails[] = [
                'question' => $question,
                'is_correct' => $isCorrect,
                'correct_answer' => $correctAnswer,
            ];
        }

        $score = $totalQuestions > 0
            ? round(($correctCount / $totalQuestions) * 100)
            : 0;

        return view('user.skills.results', compact(
            'skill',
            'score',
            'correctCount',
            'totalQuestions',
            'questionDetails'
        ));
    }
}