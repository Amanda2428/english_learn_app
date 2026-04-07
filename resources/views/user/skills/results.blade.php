@extends('layouts.user')

@section('title', 'Practice Results - ' . $skill->skill_name)

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Results Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white mb-8 text-center">
        <h1 class="text-3xl font-bold mb-2">Practice Complete! 🎉</h1>
        <p class="text-blue-100">Here's how you did on {{ $skill->skill_name }}</p>
    </div>

    <!-- Score Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 mb-8">
        <div class="text-center mb-8">
            <div class="relative inline-block">
                <svg class="w-32 h-32">
                    <circle class="text-gray-200" stroke-width="8" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"/>
                    <circle class="text-blue-600" stroke-width="8" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"
                            stroke-dasharray="{{ 2 * pi() * 56 }}"
                            stroke-dashoffset="{{ 2 * pi() * 56 * (1 - $score / 100) }}"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div>
                        <div class="text-3xl font-bold text-gray-900">{{ $score }}%</div>
                        <div class="text-xs text-gray-500">Score</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 text-center">
            <div class="p-4 bg-green-50 rounded-xl">
                <div class="text-2xl font-bold text-green-600">{{ $correctCount }}</div>
                <div class="text-sm text-gray-600">Correct</div>
            </div>
            <div class="p-4 bg-red-50 rounded-xl">
                <div class="text-2xl font-bold text-red-600">{{ $totalQuestions - $correctCount }}</div>
                <div class="text-sm text-gray-600">Incorrect</div>
            </div>
        </div>
    </div>

    <!-- Question Review -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Question Review</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($questionDetails as $index => $detail)
                <div class="p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($detail['is_correct'])
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-red-600 text-xs"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium mb-3">Question {{ $index + 1 }}: {{ $detail['question']->question_text }}</p>
                            
                            @if(!$detail['is_correct'] && isset($detail['correct_answer']))
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <p class="text-sm text-green-800">
                                        <span class="font-semibold">Correct answer:</span> {{ $detail['correct_answer']->answer_text }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('user.skills.practice', $skill) }}" 
           class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg text-center">
            <i class="fas fa-redo-alt mr-2"></i>
            Practice Again
        </a>
        <a href="{{ route('user.skills.show', $skill) }}" 
           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all text-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Skill
        </a>
    </div>
</div>
@endsection