<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Skill;
use App\Models\Level;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions.
     */
public function index(Request $request)
{
    $query = Question::with(['skill', 'level', 'video', 'answers']); 

    if ($request->filled('skill_id')) {
        $query->where('skill_id', $request->skill_id);
    }

    if ($request->filled('level_id')) {
        $query->where('level_id', $request->level_id);
    }

    if ($request->filled('difficulty')) {
        $query->where('difficulty', $request->difficulty);
    }

    if ($request->filled('has_video')) {
        if ($request->has_video === '1') {
            $query->whereNotNull('video_id');
        } elseif ($request->has_video === '0') {
            $query->whereNull('video_id');
        }
    }

    $questions = $query->orderBy('question_id', 'desc')->paginate(15);
    $skills = Skill::orderBy('skill_name')->get();
    $levels = Level::orderBy('level_order')->get();

    return view('admin.questions.index', compact('questions', 'skills', 'levels'));
}

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        $skills = Skill::with('levels')->orderBy('skill_name')->get();
        $levels = Level::with('skills')->orderBy('level_order')->get();

        return view('admin.questions.form', compact('skills', 'levels'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,skill_id',
            'level_id' => 'nullable|exists:levels,level_id',
            'question_text' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'points' => 'required|integer|min:0',
            'question_type' => 'required|in:multiple_choice,true_false,choose_correct',
            'allow_multiple_correct' => 'nullable|boolean',
            'video_id' => 'nullable|exists:videos,video_id',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $questionText = $this->cleanQuestionText($validated['question_text']);

            $allowMultipleCorrect = $this->determineMultipleCorrectAllowed($validated);

            $answers = $this->prepareAnswers($validated['answers'], $allowMultipleCorrect);

            $question = Question::create([
                'skill_id' => $validated['skill_id'],
                'level_id' => $validated['level_id'] ?? null,
                'video_id' => $validated['video_id'] ?? null,
                'question_text' => $questionText,
                'difficulty' => $validated['difficulty'],
                'points' => $validated['points'],
                'question_type' => $validated['question_type'],
                'allow_multiple_correct' => $allowMultipleCorrect,
            ]);

            $this->createAnswers($question, $answers);
        });

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question)
    {
        $question->load(['answers', 'skill.levels', 'video', 'level']);

        $skills = Skill::with('levels')->orderBy('skill_name')->get();
        $levels = Level::with('skills')->orderBy('level_order')->get();

        return view('admin.questions.form', compact('question', 'skills', 'levels'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,skill_id',
            'level_id' => 'nullable|exists:levels,level_id',
            'question_text' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'points' => 'required|integer|min:0',
            'question_type' => 'required|in:multiple_choice,true_false,choose_correct',
            'allow_multiple_correct' => 'nullable|boolean',
            'video_id' => 'nullable|exists:videos,video_id',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'nullable|boolean',
            'answers.*.answer_id' => 'nullable|exists:answers,answer_id',
        ]);

        DB::transaction(function () use ($validated, $question) {
            $questionText = $this->cleanQuestionText($validated['question_text']);

            $allowMultipleCorrect = $this->determineMultipleCorrectAllowed($validated);

            $answers = $this->prepareAnswers($validated['answers'], $allowMultipleCorrect);


            $question->update([
                'skill_id' => $validated['skill_id'],
                'level_id' => $validated['level_id'] ?? null,
                'video_id' => $validated['video_id'] ?? null,
                'question_text' => $questionText, 
                'difficulty' => $validated['difficulty'],
                'points' => $validated['points'],
                'question_type' => $validated['question_type'],
                'allow_multiple_correct' => $allowMultipleCorrect,
            ]);

            $this->syncAnswers($question, $answers);
        });

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * Get videos by skill ID (AJAX).
     */
    public function getVideosBySkill(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,skill_id'
        ]);

        $videos = Video::where('skill_id', $request->skill_id)
            ->select('video_id', 'title', 'description', 'duration')
            ->orderBy('title')
            ->get()
            ->map(function ($video) {
                return [
                    'video_id' => $video->video_id,
                    'title' => $video->title,
                    'description' => $this->cleanVideoDescription($video->description),
                    'duration' => $this->formatDuration($video->duration),
                    'duration_raw' => $video->duration,
                ];
            });

        return response()->json($videos);
    }

    /**
     * Get levels by skill ID (AJAX).
     */
    public function getLevelsBySkill($skillId)
    {
        $skill = Skill::with(['levels' => function ($query) {
            $query->orderBy('level_order');
        }])->findOrFail($skillId);

        return response()->json(
            $skill->levels->map(function ($level) {
                return [
                    'level_id' => $level->level_id,
                    'level_name' => $level->level_name,
                    'level_order' => $level->level_order,
                ];
            })
        );
    }

    /**
     * Get all levels (AJAX).
     */
    public function getAllLevels()
    {
        return response()->json(
            Level::orderBy('level_order')
                ->select('level_id', 'level_name', 'level_order')
                ->get()
        );
    }

    /**
     * Get all videos (AJAX) with optional skill filter.
     */
    public function getAllVideos(Request $request)
    {
        $query = Video::with('skill');

        if ($request->filled('skill_id')) {
            $query->where('skill_id', $request->skill_id);
        }

        $videos = $query->orderBy('title')
            ->select('video_id', 'title', 'description', 'duration', 'skill_id')
            ->get()
            ->map(function ($video) {
                return [
                    'video_id' => $video->video_id,
                    'title' => $video->title,
                    'description' => $this->cleanVideoDescription($video->description),
                    'duration' => $this->formatDuration($video->duration),
                    'duration_raw' => $video->duration,
                    'skill_id' => $video->skill_id,
                    'skill_name' => $video->skill->skill_name ?? null,
                ];
            });

        return response()->json($videos);
    }

    /**
     * Clean question text by removing any level markers.
     */
    private function cleanQuestionText($text)
    {
        if (empty($text)) {
            return '';
        }
        $cleanText = preg_replace('/<!-- LEVEL:\d+ -->\s*/', '', $text);
        
        // Trim any extra whitespace
        return trim($cleanText);
    }

    /**
     * Clean video description by removing any level markers.
     */
    private function cleanVideoDescription($description)
    {
        if (empty($description)) {
            return '';
        }
        
        return preg_replace('/<!-- LEVEL:\d+ -->\s*/', '', $description);
    }

    /**
     * Determine if multiple correct answers are allowed.
     */
    private function determineMultipleCorrectAllowed($validated)
    {
        return $validated['question_type'] === 'multiple_choice' 
            && !empty($validated['allow_multiple_correct']);
    }

    /**
     * Prepare answers array and ensure correct answer logic.
     */
    private function prepareAnswers($answersData, $allowMultipleCorrect)
    {
        $answers = collect($answersData)->map(function ($answer) {
            return [
                'answer_id' => $answer['answer_id'] ?? null,
                'answer_text' => $answer['answer_text'],
                'is_correct' => !empty($answer['is_correct']),
            ];
        })->toArray();

        if (!$allowMultipleCorrect) {
            $this->ensureSingleCorrectAnswer($answers);
        }

        return $answers;
    }

    /**
     * Ensure only one correct answer exists in the answers array.
     */
    private function ensureSingleCorrectAnswer(array &$answers)
    {
        $firstCorrectFound = false;
        
        foreach ($answers as &$answer) {
            if ($answer['is_correct'] && !$firstCorrectFound) {
                $firstCorrectFound = true;
            } elseif ($answer['is_correct']) {
                $answer['is_correct'] = false;
            }
        }
        
        // If no correct answer found, set the first answer as correct
        if (!$firstCorrectFound && count($answers) > 0) {
            $answers[0]['is_correct'] = true;
        }
    }

    /**
     * Create answers for a new question.
     */
    private function createAnswers($question, array $answers)
    {
        foreach ($answers as $answerData) {
            $question->answers()->create([
                'answer_text' => $answerData['answer_text'],
                'is_correct' => $answerData['is_correct'],
            ]);
        }
    }

    /**
     * Sync answers for an existing question.
     */
    private function syncAnswers($question, array $answers)
    {
        $existingAnswerIds = $question->answers()->pluck('answer_id')->toArray();
        $submittedAnswerIds = collect($answers)
            ->pluck('answer_id')
            ->filter()
            ->toArray();

        // Delete answers that are no longer present
        $answersToDelete = array_diff($existingAnswerIds, $submittedAnswerIds);
        if (!empty($answersToDelete)) {
            $question->answers()->whereIn('answer_id', $answersToDelete)->delete();
        }

        // Update or create answers
        foreach ($answers as $answerData) {
            if (!empty($answerData['answer_id']) && in_array($answerData['answer_id'], $existingAnswerIds)) {
                // Update existing answer
                $question->answers()
                    ->where('answer_id', $answerData['answer_id'])
                    ->update([
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
            } else {
                // Create new answer
                $question->answers()->create([
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'],
                ]);
            }
        }
    }

    /**
     * Format duration from seconds to H:i:s or i:s format.
     */
    private function formatDuration($duration)
    {
        if (empty($duration)) {
            return '00:00';
        }
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $duration)) {
            return $duration;
        }
        if (is_numeric($duration)) {
            $seconds = (int) $duration;
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secs = $seconds % 60;

            if ($hours > 0) {
                return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
            } else {
                return sprintf('%d:%02d', $minutes, $secs);
            }
        }

        return '00:00';
    }
}