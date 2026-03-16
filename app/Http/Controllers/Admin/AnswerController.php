<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    /**
     * Show the form for creating a new answer.
     */
    public function create(Question $question)
    {
        return view('admin.answers.form', compact('question'));
    }

    /**
     * Store a newly created answer in storage.
     */
    public function store(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'answer_text' => 'required|string',
            'is_correct' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($question->question_type === 'multiple_choice' && $request->has('is_correct')) {
            }
            
            if ($question->question_type === 'true_false' && $request->has('is_correct')) {
                $question->answers()->update(['is_correct' => false]);
            }
            
            if ($question->question_type === 'fill_blank' && $request->has('is_correct')) {
                $question->answers()->update(['is_correct' => false]);
            }

            $answer = $question->answers()->create([
                'answer_text' => $request->answer_text,
                'is_correct' => $request->has('is_correct') ? true : false,
            ]);

            DB::commit();

            return redirect()->route('admin.questions.edit', $question)
                ->with('success', 'Answer added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to add answer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified answer.
     */
    public function edit(Question $question, Answer $answer)
    {
        if ($answer->question_id !== $question->question_id) {
            abort(404);
        }

        return view('admin.answers.form', compact('question', 'answer'));
    }

    /**
     * Update the specified answer in storage.
     */
    public function update(Request $request, Question $question, Answer $answer)
    {
        if ($answer->question_id !== $question->question_id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'answer_text' => 'required|string',
            'is_correct' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $isCorrect = $request->has('is_correct') ? true : false;

            if ($question->question_type === 'true_false' && $isCorrect) {
                $question->answers()
                    ->where('answer_id', '!=', $answer->answer_id)
                    ->update(['is_correct' => false]);
            }
            
            if ($question->question_type === 'fill_blank' && $isCorrect) {
                $question->answers()
                    ->where('answer_id', '!=', $answer->answer_id)
                    ->update(['is_correct' => false]);
            }

            $answer->update([
                'answer_text' => $request->answer_text,
                'is_correct' => $isCorrect,
            ]);

            DB::commit();

            return redirect()->route('admin.questions.edit', $question)
                ->with('success', 'Answer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to update answer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified answer from storage.
     */
    public function destroy(Question $question, Answer $answer)
    {
        if ($answer->question_id !== $question->question_id) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            if ($answer->is_correct) {
                $correctCount = $question->answers()->where('is_correct', true)->count();
                if ($correctCount <= 1) {
                    session()->flash('warning', 'You are deleting the only correct answer. The question will have no correct answers.');
                }
            }

            $answer->delete();

            DB::commit();

            return redirect()->route('admin.questions.edit', $question)
                ->with('success', 'Answer deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete answer. Please try again.');
        }
    }

    /**
     * Bulk delete answers.
     */
    public function bulkDelete(Request $request, Question $question)
    {
        $request->validate([
            'answer_ids' => 'required|array',
            'answer_ids.*' => 'exists:answers,answer_id'
        ]);

        try {
            DB::beginTransaction();

            $answers = Answer::whereIn('answer_id', $request->answer_ids)
                ->where('question_id', $question->question_id)
                ->get();

            $correctToDelete = $answers->where('is_correct', true)->count();
            if ($correctToDelete > 0) {
                $remainingCorrect = $question->answers()
                    ->where('is_correct', true)
                    ->whereNotIn('answer_id', $request->answer_ids)
                    ->count();
                    
                if ($remainingCorrect === 0) {
                    session()->flash('warning', 'You are deleting all correct answers. The question will have no correct answers.');
                }
            }

            Answer::whereIn('answer_id', $request->answer_ids)
                ->where('question_id', $question->question_id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->answer_ids) . ' answers deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete answers.'
            ], 500);
        }
    }

    /**
     * Set correct answer for true/false or fill_blank questions.
     */
    public function setCorrect(Question $question, Answer $answer)
    {
        if ($answer->question_id !== $question->question_id) {
            abort(404);
        }

        if (!in_array($question->question_type, ['true_false', 'fill_blank'])) {
            return response()->json([
                'success' => false,
                'message' => 'This operation is only for true/false and fill in blank questions.'
            ], 400);
        }

        try {
            DB::beginTransaction();


            $question->answers()->update(['is_correct' => false]);
            
            $answer->update(['is_correct' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Correct answer set successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to set correct answer.'
            ], 500);
        }
    }

/**
 * Reorder answers (for multiple choice).
 */
public function reorder(Request $request, Question $question)
{
    $request->validate([
        'answers' => 'required|array',
        'answers.*.answer_id' => 'required|exists:answers,answer_id',
        'answers.*.order' => 'required|integer|min:0'
    ]);

    try {
        DB::beginTransaction();

        foreach ($request->answers as $item) {
    
            Answer::where('answer_id', $item['answer_id'])
                ->where('question_id', $question->question_id)
                ->update(['sort_order' => $item['order']]); 
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Answers reordered successfully.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to reorder answers.'
        ], 500);
    }
}

/**
 * Get answers for a question (API endpoint).
 */
public function index(Question $question)
{
   $answers = $question->answers()->get();

    return view('admin.answers.form', compact('question', 'answers'));
}
}