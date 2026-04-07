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
                @if($progress && $progress->completion_percentage > 0)
                <div class="flex-shrink-0">
                    <div class="relative w-24 h-24">
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle class="text-white/20" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48" />
                            <circle class="text-white" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"
                                stroke-dasharray="{{ 2 * pi() * 40 }}"
                                stroke-dashoffset="{{ 2 * pi() * 40 * (1 - $progress->completion_percentage / 100) }}" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-white">{{ round($progress->completion_percentage) }}%</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Level Selector Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                @if(isset($selectedLevel))
                    <h2 class="text-lg font-semibold text-gray-900">Current Level: <span class="text-blue-600">{{ $selectedLevel->level_name }}</span></h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $selectedLevel->description ?? "Practice $skill->skill_name at this level." }}</p>
                @else
                    <h2 class="text-lg font-semibold text-gray-900">Select a Level</h2>
                    <p class="text-sm text-gray-600 mt-1">Choose your proficiency level to start practicing</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Switch Level:</label>
                <select id="levelSelect" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Choose Level --</option>
                    @foreach($availableLevels as $level)
                        <option value="{{ $level->level_id }}" {{ isset($selectedLevel) && $selectedLevel->level_id == $level->level_id ? 'selected' : '' }}>
                            {{ $level->level_name }} ({{ $level->question_count }} questions)
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Progress Stats - Only show if level is selected and progress exists -->
    @if(isset($selectedLevel) && $progress && ($progress->videos_watched > 0 || $progress->questions_answered > 0))
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
            <h3 class="text-sm font-medium text-gray-600">Questions Mastered</h3>
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
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                @php
                    $masteryRate = $progress->total_questions_in_skill > 0 
                        ? round(($progress->questions_answered / $progress->total_questions_in_skill) * 100, 1)
                        : 0;
                @endphp
                <span class="text-2xl font-bold text-gray-900">{{ $masteryRate }}%</span>
            </div>
            <h3 class="text-sm font-medium text-gray-600">Mastery Rate</h3>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Questions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Practice Questions Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">
                        @if(isset($selectedLevel))
                            📝 Practice Questions - {{ $selectedLevel->level_name }} Level
                        @else
                            📝 Practice Questions
                        @endif
                    </h2>
                    @if(isset($selectedLevel))
                        <p class="text-sm text-gray-600 mt-1">Showing questions for {{ $selectedLevel->level_name }} level</p>
                    @endif
                </div>

                @if(isset($selectedLevel))
                    @php
                        // Group questions by type
                        $chooseCorrectOneQuestions = $questions->where('question_type', 'choose_correct_one');
                        $multipleChoiceQuestions = $questions->where('question_type', 'multiple_choice');
                        $trueFalseQuestions = $questions->where('question_type', 'true_false');

                        $hasAnyQuestions = $chooseCorrectOneQuestions->count() > 0 ||
                        $multipleChoiceQuestions->count() > 0 ||
                        $trueFalseQuestions->count() > 0;
                        
                        // Difficulty badge configuration
                        $difficultyConfig = [
                            'easy' => ['color' => 'green', 'icon' => 'seedling', 'label' => 'Easy'],
                            'medium' => ['color' => 'yellow', 'icon' => 'chart-line', 'label' => 'Medium'],
                            'hard' => ['color' => 'red', 'icon' => 'fire', 'label' => 'Hard'],
                            'expert' => ['color' => 'purple', 'icon' => 'crown', 'label' => 'Expert']
                        ];
                    @endphp

                    @if($hasAnyQuestions)
                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px overflow-x-auto" aria-label="Question types">
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

                                @if($chooseCorrectOneQuestions->count() > 0)
                                <button type="button"
                                    onclick="switchTab('choose-correct-one')"
                                    class="tab-button text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                                    data-tab="choose-correct-one">
                                    Choose Correct One
                                    <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                                        {{ $chooseCorrectOneQuestions->count() }}
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
                                    @php
                                        $difficulty = strtolower($question->difficulty ?? 'easy');
                                        $difficultyInfo = $difficultyConfig[$difficulty] ?? $difficultyConfig['easy'];
                                    @endphp
                                    <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id, 'level' => $selectedLevel->level_id]) }}"
                                        class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-2 flex-wrap gap-2">
                                                <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                                    Multiple Choice
                                                </span>
                                                <!-- Difficulty Badge -->
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $difficultyInfo['color'] }}-100 text-{{ $difficultyInfo['color'] }}-700">
                                                    <i class="fas fa-{{ $difficultyInfo['icon'] }} text-xs mr-1"></i>
                                                    {{ $difficultyInfo['label'] }}
                                                </span>
                                                @if($question->video_id)
                                                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                                    <i class="fas fa-video mr-1"></i> Has Video
                                                </span>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ $question->points }} pts
                                            </span>
                                        </div>
                                        <p class="text-gray-800 mb-3">{{ $question->clean_question_text }}</p>
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
                                        <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'multiple_choice', 'level' => $selectedLevel->level_id]) }}"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            View all {{ $multipleChoiceQuestions->count() }} multiple choice questions
                                            <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Choose Correct One Tab -->
                            @if($chooseCorrectOneQuestions->count() > 0)
                            <div id="choose-correct-one-tab" class="tab-content hidden">
                                <div class="space-y-4">
                                    @foreach($chooseCorrectOneQuestions->take(5) as $question)
                                    @php
                                        $difficulty = strtolower($question->difficulty ?? 'easy');
                                        $difficultyInfo = $difficultyConfig[$difficulty] ?? $difficultyConfig['easy'];
                                    @endphp
                                    <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id, 'level' => $selectedLevel->level_id]) }}"
                                        class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-2 flex-wrap gap-2">
                                                <span class="text-sm font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full">
                                                    Choose Correct One
                                                </span>
                                                <!-- Difficulty Badge -->
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $difficultyInfo['color'] }}-100 text-{{ $difficultyInfo['color'] }}-700">
                                                    <i class="fas fa-{{ $difficultyInfo['icon'] }} text-xs mr-1"></i>
                                                    {{ $difficultyInfo['label'] }}
                                                </span>
                                                @if($question->video_id)
                                                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                                    <i class="fas fa-video mr-1"></i> Has Video
                                                </span>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ $question->points }} pts
                                            </span>
                                        </div>
                                        <p class="text-gray-800 mb-3">{{ $question->clean_question_text }}</p>
                                        <div class="space-y-2">
                                            @foreach($question->answers->take(3) as $answer)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                                {{ Str::limit($answer->answer_text, 50) }}
                                            </div>
                                            @endforeach
                                            @if($question->answers->count() > 3)
                                            <div class="text-sm text-gray-400">
                                                + {{ $question->answers->count() - 3 }} more options
                                            </div>
                                            @endif
                                        </div>
                                    </a>
                                    @endforeach

                                    @if($chooseCorrectOneQuestions->count() > 5)
                                    <div class="text-center mt-4">
                                        <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'choose_correct_one', 'level' => $selectedLevel->level_id]) }}"
                                            class="inline-flex items-center text-green-600 hover:text-green-800">
                                            View all {{ $chooseCorrectOneQuestions->count() }} choose correct one questions
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
                                    @php
                                        $difficulty = strtolower($question->difficulty ?? 'easy');
                                        $difficultyInfo = $difficultyConfig[$difficulty] ?? $difficultyConfig['easy'];
                                    @endphp
                                    <a href="{{ route('user.skills.practice', ['skill' => $skill, 'question' => $question->question_id, 'level' => $selectedLevel->level_id]) }}"
                                        class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center space-x-2 flex-wrap gap-2">
                                                <span class="text-sm font-medium text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                                                    True or False
                                                </span>
                                                <!-- Difficulty Badge -->
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $difficultyInfo['color'] }}-100 text-{{ $difficultyInfo['color'] }}-700">
                                                    <i class="fas fa-{{ $difficultyInfo['icon'] }} text-xs mr-1"></i>
                                                    {{ $difficultyInfo['label'] }}
                                                </span>
                                                @if($question->video_id)
                                                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                                    <i class="fas fa-video mr-1"></i> Has Video
                                                </span>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ $question->points }} pts
                                            </span>
                                        </div>
                                        <p class="text-gray-800 mb-3">{{ $question->clean_question_text }}</p>
                                        <div class="flex space-x-4">
                                            <span class="px-4 py-1 bg-green-100 text-green-700 rounded-full text-sm">True</span>
                                            <span class="px-4 py-1 bg-red-100 text-red-700 rounded-full text-sm">False</span>
                                        </div>
                                    </a>
                                    @endforeach

                                    @if($trueFalseQuestions->count() > 5)
                                    <div class="text-center mt-4">
                                        <a href="{{ route('user.skills.practice', ['skill' => $skill, 'type' => 'true_false', 'level' => $selectedLevel->level_id]) }}"
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
                            <p class="text-gray-500">No practice questions available for this level yet.</p>
                            <p class="text-sm text-gray-400 mt-2">Please try another level or check back soon!</p>
                            <a href="{{ route('user.skills.select-level', $skill) }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-arrow-left mr-2"></i> Choose Another Level
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <i class="fas fa-layer-group text-4xl text-gray-400 mb-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Level to Start</h3>
                        <p class="text-gray-500 mb-4">Choose your proficiency level to begin practicing {{ $skill->skill_name }}.</p>
                        <a href="{{ route('user.skills.select-level', $skill) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-play mr-2"></i> Choose Level
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Practice & Progress -->
        <div class="space-y-6">
            <!-- Practice Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-xl font-bold mb-2">Ready to Practice?</h3>
                <p class="text-blue-100 text-sm mb-6">
                    @if(isset($selectedLevel))
                        Test your knowledge with {{ $questions->count() }} questions at {{ $selectedLevel->level_name }} level
                    @else
                        Select a level to start practicing
                    @endif
                </p>

                @if(isset($selectedLevel))
                    <a href="{{ route('user.skills.practice', ['skill' => $skill, 'level' => $selectedLevel->level_id]) }}"
                        class="block w-full py-3 bg-white text-blue-600 rounded-lg text-center font-semibold hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl">
                        <i class="fas fa-play-circle mr-2"></i>
                        Start Practice
                    </a>
                @else
                    <button disabled
                        class="block w-full py-3 bg-white/50 text-white rounded-lg text-center font-semibold cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        Select a Level First
                    </button>
                @endif

                <!-- Mastery Rate instead of Accuracy -->
                @if($progress && $progress->questions_answered > 0)
                <div class="mt-4 pt-4 border-t border-white/20">
                    @php
                        $masteryRate = $progress->total_questions_in_skill > 0 
                            ? round(($progress->questions_answered / $progress->total_questions_in_skill) * 100, 1)
                            : 0;
                    @endphp
                    <div class="flex justify-between text-sm mb-2">
                        <span>Mastery Rate</span>
                        <span class="font-bold">{{ $masteryRate }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2" style="width: {{ $masteryRate }}%"></div>
                    </div>
                    <p class="text-xs text-blue-100 mt-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ $progress->questions_answered }}/{{ $progress->total_questions_in_skill }} questions mastered
                    </p>
                </div>
                @endif
            </div>

            <!-- Quick Stats - Show only if level selected -->
            @if(isset($selectedLevel) && $progress)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Quick Stats - {{ $selectedLevel->level_name }}</h3>
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
                        <span class="text-gray-600">Questions Mastered</span>
                        <span class="font-medium text-gray-900">
                            {{ $progress->questions_answered }}/{{ $progress->total_questions_in_skill }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 rounded-full h-2"
                            style="width: {{ $progress->total_questions_in_skill > 0 ? ($progress->questions_answered / $progress->total_questions_in_skill) * 100 : 0 }}%">
                        </div>
                    </div>

                    @php
                        $masteryRate = $progress->total_questions_in_skill > 0 
                            ? round(($progress->questions_answered / $progress->total_questions_in_skill) * 100, 1)
                            : 0;
                    @endphp
                    <div class="flex justify-between text-sm mt-4">
                        <span class="text-gray-600">Mastery Rate</span>
                        <span class="font-medium text-green-600">{{ $masteryRate }}%</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Available Levels -->
            @if($availableLevels && $availableLevels->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Available Levels</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($availableLevels as $level)
                    <a href="{{ route('user.skills.show', ['skill' => $skill, 'level' => $level->level_id]) }}" 
                       class="px-3 py-1 rounded-full text-sm transition-colors {{ isset($selectedLevel) && $selectedLevel->level_id == $level->level_id ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                        {{ $level->level_name }}
                        <span class="ml-1 text-xs">({{ $level->question_count }})</span>
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
    // Level selector change handler
    document.getElementById('levelSelect')?.addEventListener('change', function() {
        if (this.value) {
            window.location.href = '{{ route("user.skills.show", $skill) }}?level=' + this.value;
        }
    });

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
        let tabId = '';
        if (tabName === 'multiple-choice') {
            tabId = 'multiple-choice-tab';
        } else if (tabName === 'choose-correct-one') {
            tabId = 'choose-correct-one-tab';
        } else if (tabName === 'true-false') {
            tabId = 'true-false-tab';
        }
        
        const selectedTab = document.getElementById(tabId);
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