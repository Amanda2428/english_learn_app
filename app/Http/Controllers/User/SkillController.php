<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Question;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all active skills with counts
        $skills = Skill::where('status', true)
            ->withCount(['videos', 'questions'])
            ->get();
        
        // Get user's progress for all skills
        $userProgress = UserProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('skill_id');
        
        return view('user.skills.index', compact('skills', 'userProgress'));
    }
    
    public function show(Skill $skill)
    {
        $user = Auth::user();
        
        if (!$skill->status) {
            abort(404);
        }
        
        // Load skill relationships
        $skill->load([
            'videos' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'questions' => function($query) {
                $query->with('answers')
                      ->orderBy('difficulty')
                      ->orderBy('created_at');
            },
            'levels'
        ]);
        
        // Get or create user progress for this skill
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
                'level_id' => $skill->levels->first()?->level_id
            ],
            [
                'status' => 'not_started',
                'points_earned' => 0,
                'completion_percentage' => 0,
                'videos_watched' => 0,
                'total_videos_in_skill' => $skill->videos->count(),
                'questions_answered' => 0,
                'correct_answers' => 0,
                'total_questions_in_skill' => $skill->questions->count(),
                'time_spent_minutes' => 0
            ]
        );
        
        return view('user.skills.show', compact('skill', 'progress'));
    }
    
    public function practice(Skill $skill)
    {
        $user = Auth::user();
        
        // Get questions for this skill
        $questions = $skill->questions()
            ->with('answers')
            ->inRandomOrder()
            ->limit(10)
            ->get();
        
        if ($questions->isEmpty()) {
            return redirect()->route('skills.show', $skill)
                ->with('error', 'No questions available for this skill yet.');
        }
        
        // Get or create progress
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
                'total_questions_in_skill' => $skill->questions()->count()
            ]
        );
        
        // Update status to in_progress if not started
        if ($progress->status === 'not_started') {
            $progress->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);
        }
        
        return view('user.skills.practice', compact('skill', 'questions', 'progress'));
    }
    
    public function submitPractice(Request $request, Skill $skill)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);
        $timeSpent = $request->input('time_spent', 0);
        
        // Calculate results
        $correctCount = 0;
        $totalQuestions = count($answers);
        $questionDetails = [];
        
        foreach ($answers as $questionId => $answerData) {
            $question = Question::with('answers')->find($questionId);
            if (!$question) continue;
            
            $selectedAnswerId = $answerData['selected'] ?? null;
            $isCorrect = false;
            
            if ($selectedAnswerId) {
                $selectedAnswer = $question->answers->find($selectedAnswerId);
                $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
                if ($isCorrect) $correctCount++;
            }
            
            $questionDetails[] = [
                'question' => $question,
                'selected' => $selectedAnswerId,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->answers->firstWhere('is_correct', true)
            ];
        }
        
        // Update progress
        $progress = UserProgress::where('user_id', $user->id)
            ->where('skill_id', $skill->skill_id)
            ->first();
        
        if ($progress) {
            $progress->questions_answered += $totalQuestions;
            $progress->correct_answers += $correctCount;
            $progress->time_spent_minutes += ceil($timeSpent / 60);
            
            // Check if skill is completed
            if ($progress->questions_answered >= $progress->total_questions_in_skill) {
                $progress->status = 'completed';
                $progress->completed_at = now();
                $progress->completion_percentage = 100;
            } else {
                $progress->completion_percentage = min(
                    100,
                    round(($progress->questions_answered / $progress->total_questions_in_skill) * 100)
                );
            }
            
            $progress->save();
        }
        
        // Calculate score
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
        
        return view('user.skills.results', compact(
            'skill', 
            'questionDetails', 
            'correctCount', 
            'totalQuestions', 
            'score',
            'progress'
        ));
    }
    
    public function watchVideo(Skill $skill, Video $video)
    {
        $user = Auth::user();
        
        // Verify video belongs to skill
        if ($video->skill_id != $skill->skill_id) {
            abort(404);
        }
        
        // Update progress
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $skill->skill_id,
                'level_id' => $skill->levels->first()?->level_id
            ],
            [
                'total_videos_in_skill' => $skill->videos->count()
            ]
        );
        
        // Check if this video hasn't been watched yet
        $watchedVideos = session()->get('watched_videos_' . $skill->skill_id, []);
        if (!in_array($video->video_id, $watchedVideos)) {
            $watchedVideos[] = $video->video_id;
            session()->put('watched_videos_' . $skill->skill_id, $watchedVideos);
            
            $progress->videos_watched = count($watchedVideos);
            $progress->save();
        }
        
        return view('user.skills.video', compact('skill', 'video', 'progress'));
    }
}