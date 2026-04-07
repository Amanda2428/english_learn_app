@extends('layouts.user')

@section('title', 'All Skills')
@section('subtitle', 'Choose a skill and start practicing by level')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 translate-x-20"></div>
        <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/10 rounded-full translate-y-14 -translate-x-14"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-3">Explore English Skills</h1>
                <p class="text-blue-100 max-w-2xl">
                    Choose a skill, select your level, and practice with videos and questions designed to improve your English step by step.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 min-w-[220px]">
                <div class="bg-white/15 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold">{{ $skills->count() }}</div>
                    <div class="text-sm text-blue-100">Total Skills</div>
                </div>
                <div class="bg-white/15 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold">{{ $skills->sum('questions_count') }}</div>
                    <div class="text-sm text-blue-100">Questions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Core English Skills</h2>
            <p class="text-gray-600 mt-1">Master all aspects of the English language</p>
        </div>
        <div class="text-sm text-gray-500">
            {{ $skills->count() }} skills available
        </div>
    </div>

    <!-- Skills Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($skills as $skill)
            @php
                $icons = [
                    'speaking' => 'microphone-alt',
                    'listening' => 'headphones-alt',
                    'reading' => 'book-open',
                    'writing' => 'pencil-alt',
                    'grammar' => 'spell-check',
                    'vocabulary' => 'book',
                    'pronunciation' => 'volume-up',
                    'conversation' => 'comments',
                ];

                $icon = $icons[strtolower($skill->skill_name)] ?? 'circle-check';

                $colors = ['green', 'blue', 'purple', 'orange', 'red', 'indigo', 'pink', 'teal'];
                $color = $colors[$loop->index % count($colors)];

                $badgeClass = match($color) {
                    'green' => 'bg-green-100 text-green-700',
                    'blue' => 'bg-blue-100 text-blue-700',
                    'purple' => 'bg-purple-100 text-purple-700',
                    'orange' => 'bg-orange-100 text-orange-700',
                    'red' => 'bg-red-100 text-red-700',
                    'indigo' => 'bg-indigo-100 text-indigo-700',
                    'pink' => 'bg-pink-100 text-pink-700',
                    default => 'bg-teal-100 text-teal-700',
                };

                $iconClass = match($color) {
                    'green' => 'bg-green-100 text-green-600',
                    'blue' => 'bg-blue-100 text-blue-600',
                    'purple' => 'bg-purple-100 text-purple-600',
                    'orange' => 'bg-orange-100 text-orange-600',
                    'red' => 'bg-red-100 text-red-600',
                    'indigo' => 'bg-indigo-100 text-indigo-600',
                    'pink' => 'bg-pink-100 text-pink-600',
                    default => 'bg-teal-100 text-teal-600',
                };
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $iconClass }}">
                            <i class="fas fa-{{ $icon }} text-xl"></i>
                        </div>

                        @if($skill->has_progress)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                {{ $skill->avg_completion }}% progress
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                New
                            </span>
                        @endif
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $skill->skill_name }}</h3>

                    <p class="text-sm text-gray-600 mb-4 min-h-[40px]">
                        {{ $skill->description ?? 'Master this essential English skill.' }}
                    </p>

                    <!-- Skill levels -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($skill->levels->take(3) as $level)
                            <span class="px-2.5 py-1 rounded-full text-xs {{ $badgeClass }}">
                                {{ $level->level_name }}
                            </span>
                        @endforeach

                        @if($skill->levels->count() > 3)
                            <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                +{{ $skill->levels->count() - 3 }} more
                            </span>
                        @endif
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-lg font-bold text-gray-900">{{ $skill->videos_count }}</div>
                            <div class="text-xs text-gray-500">Videos</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-lg font-bold text-gray-900">{{ $skill->questions_count }}</div>
                            <div class="text-xs text-gray-500">Questions</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-lg font-bold text-gray-900">{{ $skill->total_points }}</div>
                            <div class="text-xs text-gray-500">Points Earned</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-lg font-bold text-gray-900">{{ $skill->mastery }}%</div>
                            <div class="text-xs text-gray-500">Mastery</div>
                        </div>
                    </div>

                    <!-- Progress -->
                    @if($skill->has_progress)
                        <div class="mb-5">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Your Progress</span>
                                <span class="font-semibold text-gray-900">{{ $skill->avg_completion }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600" style="width: {{ $skill->avg_completion }}%"></div>
                            </div>
                            <div class="flex justify-between mt-2 text-xs text-gray-500">
                                <span>{{ $skill->completed_levels_count }} completed level(s)</span>
                                <span>{{ $skill->in_progress_levels_count }} in progress</span>
                            </div>
                        </div>
                    @endif

                    <!-- Action -->
                    <a href="{{ route('user.skills.select-level', $skill) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all">
                        {{ $skill->has_progress ? 'Continue Skill' : 'Start Practicing' }}
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-50 rounded-2xl py-16 text-center">
                <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No skills available yet</h3>
                <p class="text-gray-500">Please check back later.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection