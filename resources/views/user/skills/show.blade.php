@extends('layouts.user')

@section('title', $skill->skill_name)
@section('subtitle', 'Master this skill with videos and practice questions')

@section('content')
<div class="space-y-8">
    <!-- Skill Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>

        <div class="relative z-10">
            <div class="flex items-center space-x-2 mb-4">
                @foreach($skill->levels as $level)
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">{{ $level->level_name }}</span>
                @endforeach
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold mb-4">{{ $skill->skill_name }}</h1>
                    <p class="text-blue-100 max-w-2xl">{{ $skill->description ?? 'Master this essential English skill.' }}</p>
                </div>

                <!-- Progress Circle -->
                @if($progress->completion_percentage > 0)
                <div class="flex-shrink-0">
                    <div class="relative w-24 h-24">
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle class="text-white/20" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48" />
                            <circle class="text-white" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"
                                stroke-dasharray="{{ 2 * pi() * 40 }}"
                                stroke-dashoffset="{{ 2 * pi() * 40 * (1 - $progress->completion_percentage / 100) }}" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-white">{{ $progress->completion_percentage }}%</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Progress Stats -->
    @if($progress->videos_watched > 0 || $progress->questions_answered > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-blue-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $progress->videos_watched }}/{{ $progress->total_videos_in_skill }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Videos Watched</h3>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-question-circle text-green-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $progress->questions_answered }}/{{ $progress->total_questions_in_skill }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Questions Answered</h3>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $progress->points_earned }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Points Earned</h3>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ floor($progress->time_spent_minutes / 60) }}h {{ $progress->time_spent_minutes % 60 }}m</span>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Time Spent</h3>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Questions (now full width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Practice Questions Section with Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">📝 Practice Questions</h2>
                </div>

                @php
                // Group questions by type
                $fillBlankQuestions = $skill->questions->where('question_type', 'fill_blank');
                $multipleChoiceQuestions = $skill->questions->where('question_type', 'multiple_choice');
                $trueFalseQuestions = $skill->questions->where('question_type', 'true_false');

                $hasAnyQuestions = $fillBlankQuestions->count() > 0 ||
                $multipleChoiceQuestions->count() > 0 ||
                $trueFalseQuestions->count() > 0;
                @endphp

                @if($hasAnyQuestions)
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" aria-label="Question types">
                        @if($multipleChoiceQuestions->count() > 0)
                        <button type="button"
                            onclick="switchTab('multiple-choice')"
                            class="tab-button active text-gray-900 border-blue-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                            data-tab="multiple-choice">
                            Multiple Choice
                            <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $multipleChoiceQuestions->count() }}
                            </span>
                        </button>
                        @endif

                        @if($fillBlankQuestions->count() > 0)
                        <button type="button"
                            onclick="switchTab('choose-correct-answer')"
                            class="tab-button text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                            data-tab="choose-correct-answer">
                            Choose Correct Answer
                            <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $fillBlankQuestions->count() }}
                            </span>
                        </button>
                        @endif

                        @if($trueFalseQuestions->count() > 0)
                        <button type="button"
                            onclick="switchTab('true-false')"
                            class="tab-button text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                            data-tab="true-false">
                            True or False
                            <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $trueFalseQuestions->count() }}
                            </span>
                        </button>
                        @endif
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Multiple Choice Tab -->
                    @if($multipleChoiceQuestions->count() > 0)
                    <div id="multiple-choice-tab" class="tab-content">
                        <div class="space-y-4">
                            @foreach($multipleChoiceQuestions->take(5) as $question)
                            <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id]) }}"
                                class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                            Multiple Choice
                                        </span>
                                        @if($question->video_id)
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                            <i class="fas fa-video mr-1"></i> Has Video
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $question->points }} points
                                    </span>
                                </div>
                                <p class="text-gray-800 mb-3">{{ $question->question_text }}</p>
                                <div class="space-y-2">
                                    @foreach($question->answers->take(2) as $answer)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="far fa-circle mr-2 text-gray-400"></i>
                                        {{ Str::limit($answer->answer_text, 50) }}
                                    </div>
                                    @endforeach
                                    @if($question->answers->count() > 2)
                                    <div class="text-sm text-gray-400">
                                        + {{ $question->answers->count() - 2 }} more options
                                    </div>
                                    @endif
                                </div>
                            </a>
                            @endforeach

                            @if($multipleChoiceQuestions->count() > 5)
                            <div class="text-center mt-4">
                                <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'multiple_choice']) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                    View all {{ $multipleChoiceQuestions->count() }} multiple choice questions
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Fill in the Blank Tab -->
                    @if($fillBlankQuestions->count() > 0)
                    <div id="fill-blank-tab" class="tab-content hidden">
                        <div class="space-y-4">
                            @foreach($fillBlankQuestions->take(5) as $question)
                            <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id]) }}"
                                class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                            Fill in the Blank
                                        </span>
                                        @if($question->video_id)
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                            <i class="fas fa-video mr-1"></i> Has Video
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $question->points }} points
                                    </span>
                                </div>
                                <p class="text-gray-800 mb-3">
                                    {!! preg_replace('/\[blank\]/', '<span class="inline-block w-20 h-6 bg-gray-100 border-b-2 border-dashed border-gray-400 mx-1 align-middle"></span>', $question->question_text) !!}
                                </p>
                            </a>
                            @endforeach

                            @if($fillBlankQuestions->count() > 5)
                            <div class="text-center mt-4">
                                <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'fill_blank']) }}"
                                    class="inline-flex items-center text-green-600 hover:text-green-800">
                                    View all {{ $fillBlankQuestions->count() }} fill-in-the-blank questions
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- True or False Tab -->
                    @if($trueFalseQuestions->count() > 0)
                    <div id="true-false-tab" class="tab-content hidden">
                        <div class="space-y-4">
                            @foreach($trueFalseQuestions->take(5) as $question)
                            <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id]) }}"
                                class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                            True or False
                                        </span>
                                        @if($question->video_id)
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                            <i class="fas fa-video mr-1"></i> Has Video
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $question->points }} points
                                    </span>
                                </div>
                                <p class="text-gray-800 mb-3">{{ $question->question_text }}</p>
                                <div class="flex space-x-4">
                                    <span class="px-4 py-1 bg-green-100 text-green-700 rounded-full text-sm">True</span>
                                    <span class="px-4 py-1 bg-red-100 text-red-700 rounded-full text-sm">False</span>
                                </div>
                            </a>
                            @endforeach

                            @if($trueFalseQuestions->count() > 5)
                            <div class="text-center mt-4">
                                <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'true_false']) }}"
                                    class="inline-flex items-center text-purple-600 hover:text-purple-800">
                                    View all {{ $trueFalseQuestions->count() }} true or false questions
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="p-12 text-center">
                    <i class="fas fa-question-circle text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No practice questions available for this skill yet.</p>
                    <p class="text-sm text-gray-400 mt-2">Check back soon for new questions!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Practice & Progress -->
        <div class="space-y-6">
            <!-- Practice Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-2">Ready to Practice?</h3>
                <p class="text-blue-100 text-sm mb-6">Test your knowledge with practice questions</p>

                <a href="{{ route('user.skills.practice', $skill) }}"
                    class="block w-full py-3 bg-white text-blue-600 rounded-lg text-center font-semibold hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-play-circle mr-2"></i>
                    Start Practice
                </a>

                @if($progress->questions_answered > 0)
                <div class="mt-4 pt-4 border-t border-white/20">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Accuracy</span>
                        <span class="font-bold">{{ $progress->accuracy_rate ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2" style="width: {{ $progress->accuracy_rate ?? 0 }}%"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Videos Progress</span>
                        <span class="font-medium text-gray-900">
                            {{ $progress->videos_watched }}/{{ $progress->total_videos_in_skill }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2"
                            style="width: {{ $progress->total_videos_in_skill > 0 ? ($progress->videos_watched / $progress->total_videos_in_skill) * 100 : 0 }}%">
                        </div>
                    </div>

                    <div class="flex justify-between text-sm mt-4">
                        <span class="text-gray-600">Questions Progress</span>
                        <span class="font-medium text-gray-900">
                            {{ $progress->questions_answered }}/{{ $progress->total_questions_in_skill }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 rounded-full h-2"
                            style="width: {{ $progress->total_questions_in_skill > 0 ? ($progress->questions_answered / $progress->total_questions_in_skill) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Info -->
            @if($skill->levels->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Available in Levels</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($skill->levels as $level)
                    <a href="{{ route('user.levels.show', $level) }}" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-sm hover:bg-blue-100 transition-colors">
                        {{ $level->level_name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'text-gray-900', 'border-blue-600');
            button.classList.add('text-gray-500', 'border-transparent');
        });

        // Show selected tab content
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }

        // Activate clicked button
        const clickedButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (clickedButton) {
            clickedButton.classList.remove('text-gray-500', 'border-transparent');
            clickedButton.classList.add('text-gray-900', 'border-blue-600');
        }
    }

    // Activate first tab by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstTab = document.querySelector('.tab-button');
        if (firstTab) {
            const tabName = firstTab.getAttribute('data-tab');
            switchTab(tabName);
        }
    });
</script>
@endpush
@endsection