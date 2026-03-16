@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ isset($answer) ? 'Edit' : 'Add' }} Answer</h1>
        <p class="text-sm text-gray-600 mt-1">For question: {{ $question->question_text }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ isset($answer) ? route('admin.questions.answers.update', [$question, $answer]) : route('admin.questions.answers.store', $question) }}" 
              method="POST" 
              class="space-y-6">
            @csrf
            @if(isset($answer))
                @method('PUT')
            @endif

            <!-- Answer Text -->
            <div>
                <label for="answer_text" class="block text-sm font-medium text-gray-700 mb-2">Answer Text <span class="text-red-500">*</span></label>
                <textarea name="answer_text" id="answer_text" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('answer_text') border-red-500 @enderror" 
                          placeholder="Enter the answer text..." required>{{ old('answer_text', $answer->answer_text ?? '') }}</textarea>
                @error('answer_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Correct -->
            <div class="flex items-center">
                <input type="checkbox" name="is_correct" id="is_correct" value="1" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       {{ old('is_correct', $answer->is_correct ?? false) ? 'checked' : '' }}>
                <label for="is_correct" class="ml-2 block text-sm text-gray-900">
                    This is the correct answer
                </label>
            </div>

            <!-- Question Type Hint -->
            @if($question->question_type === 'multiple_choice')
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <span class="font-semibold">Note:</span> For multiple choice questions, you can have multiple correct answers. Make sure to mark all correct ones.
                    </p>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.questions.edit', $question) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    {{ isset($answer) ? 'Update' : 'Add' }} Answer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection