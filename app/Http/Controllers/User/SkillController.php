<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Answer;


class SkillController extends Controller
{
    public function show(Skill $skill)
    {
        $user = Auth::user();

        // 1. Load relationships needed for the view
        $skill->load(['levels', 'questions.answers', 'videos']);

        // 2. Get or initialize User Progress for this specific skill
        // We use firstOrCreate so the view always has a $progress object to read from
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
            ],
            [
                'level_id' => $skill->levels->first()?->level_id, // Default to first level linked to skill
                'status' => 'not_started',
                'total_videos_in_skill' => $skill->videos->count(),
                'total_questions_in_skill' => $skill->questions->count(),
            ]
        );

        // 3. Optional: Update totals in case you added new questions/videos since last visit
        $progress->update([
            'total_videos_in_skill' => $skill->videos->count(),
            'total_questions_in_skill' => $skill->questions->count(),
        ]);

        return view('user.skills.show', compact('skill', 'progress'));
    }
    public function practice(Skill $skill, Request $request)
    {
        $questionId = $request->query('question');
        $type = $request->query('type');

        $query = $skill->questions()->with(['answers', 'video']);

        if ($questionId) {
            $questions = $query->where('question_id', $questionId)->get();
        } elseif ($type) {
            $questions = $query->where('question_type', $type)->get();
        } else {
            $questions = $query->get();
        }

        if ($questions->isEmpty()) {
            return redirect()->route('user.skills.show', $skill)
                ->with('error', 'No questions found for this selection.');
        }

        $progress = UserProgress::where('user_id', Auth::user()->id)
            ->where('skill_id', $skill->skill_id)
            ->first();

        return view('user.skills.practice', compact('skill', 'questions', 'progress'));
    }

    /**
     * Handle the form submission and update user progress.
     */
    public function submitPractice(Request $request, Skill $skill)
    {
        $user = Auth::user();
        $submittedAnswers = $request->input('answers', []);

        $incorrectQuestionIds = [];
        $totalPointsAvailable = 0;

        // 1. Validate answers
        foreach ($submittedAnswers as $questionId => $answerId) {
            $question = Question::find($questionId);
            $totalPointsAvailable += $question->points ?? 0;

            $isCorrect = Answer::where('answer_id', $answerId)
                ->where('question_id', $questionId)
                ->where('is_correct', true)
                ->exists();

            if (!$isCorrect) {
                $incorrectQuestionIds[] = $questionId;
            }
        }

        // 2. FAIL: If any are wrong, redirect back with incorrect IDs
        if (!empty($incorrectQuestionIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Some answers are incorrect. Please try again!')
                ->with('incorrect_questions', $incorrectQuestionIds);
        }

        // 3. SUCCESS: All correct, update progress
        $timeSpentSeconds = $request->input('time_spent', 0);
        $timeSpentMinutes = max(1, round($timeSpentSeconds / 60));

        $progress = UserProgress::firstOrNew([
            'user_id' => $user->id,
            'skill_id' => $skill->skill_id,
            'level_id' => $skill->level_id,
        ]);

        $progress->total_questions_in_skill = $skill->questions()->count();
        $progress->correct_answers += count($submittedAnswers);
        $progress->questions_answered += count($submittedAnswers);
        $progress->points_earned += $totalPointsAvailable;
        $progress->time_spent_minutes += $timeSpentMinutes;

        $progress->completion_percentage = min(100, ($progress->questions_answered / $progress->total_questions_in_skill) * 100);
        $progress->status = ($progress->completion_percentage >= 100) ? 'completed' : 'in_progress';
        if ($progress->status === 'completed' && !$progress->completed_at) $progress->completed_at = now();

        $progress->save();

        return redirect()->route('user.skills.show', $skill)
            ->with('success', "Perfect! You earned $totalPointsAvailable points.");
    }
}
