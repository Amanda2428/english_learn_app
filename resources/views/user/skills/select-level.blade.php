@extends('layouts.user')

@section('title', 'Select Level - ' . $skill->skill_name)
@section('subtitle', 'Choose your proficiency level for ' . $skill->skill_name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('user.skills.index') }}" class="text-gray-700 hover:text-blue-600">Skills</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ $skill->skill_name }}</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Select Level</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Skill Header -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $skill->skill_name }}</h1>
                    <p class="text-gray-600">
                        {{ $skill->description ?? 'Master this essential English skill with our structured curriculum.' }}
                    </p>
                </div>
                <div class="hidden sm:block">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
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
                        @endphp
                        <i class="fas fa-{{ $icon }} text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level Selection Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Choose Your Level</h2>
            <p class="text-gray-600 mb-6">Select the proficiency level you want to practice. Each level has tailored
                questions and resources.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($levels as $level)
                    @php
                        $levelColors = [
                            'Elementary' => 'green',
                            'Beginner' => 'green',
                            'Pre-intermediate' => 'teal',
                            'Intermediate' => 'blue',
                            'Upper-intermediate' => 'indigo',
                            'Advanced' => 'purple',
                            'Expert' => 'purple',
                            'Master' => 'pink',
                        ];
                        $color = $levelColors[$level->level_name] ?? 'blue';

                        $progressColor = '';
                        if ($level->completion_percentage >= 100) {
                            $progressColor = 'bg-green-500';
                        } elseif ($level->completion_percentage > 0) {
                            $progressColor = 'bg-blue-500';
                        } else {
                            $progressColor = 'bg-gray-300';
                        }
                    @endphp

                    <div
                        class="bg-white rounded-xl shadow-sm border hover:shadow-lg transition-all hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-{{ $color }}-500"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-{{ $color }}-100 flex items-center justify-center">
                                    <i class="fas fa-layer-group text-{{ $color }}-600 text-xl"></i>
                                </div>
                                @if ($level->status === 'completed')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                    </span>
                                @elseif($level->status === 'in_progress')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-chart-line mr-1"></i> In Progress
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-clock mr-1"></i> Not Started
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $level->level_name }}</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ $level->description ?? "Practice $skill->skill_name at $level->level_name level." }}</p>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Questions:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $level->total_questions ?? ($level->questions_count ?? 0) }}</span>
                                </div>
                                @if ($level->points_earned > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Points Earned:</span>
                                        <span
                                            class="font-medium text-{{ $color }}-600">{{ $level->points_earned }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($level->completion_percentage > 0)
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ round($level->completion_percentage) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $color }}-500 h-2 rounded-full"
                                            style="width: {{ $level->completion_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('user.skills.start-practice', $skill) }}" method="POST">
                                @csrf
                                <input type="hidden" name="level_id" value="{{ $level->level_id }}">
                                <button type="submit"
                                    class="w-full mt-2 px-4 py-2 bg-{{ $color }}-600 text-white rounded-lg hover:bg-{{ $color }}-700 transition text-sm font-medium">
                                    @if ($level->status === 'completed')
                                        <i class="fas fa-redo-alt mr-2"></i> Review Again
                                    @elseif($level->status === 'in_progress')
                                        <i class="fas fa-play mr-2"></i> Continue
                                    @else
                                        <i class="fas fa-play mr-2"></i> Start Practicing
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-info-circle text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">No levels available for this skill yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recommended Level Section -->
        @php
            $recommendedLevel =
                $levels->first(function ($level) {
                    return $level->status === 'in_progress';
                }) ?? $levels->first();
        @endphp

        @if ($recommendedLevel)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Recommended for You</h3>
                        <p class="text-gray-700 mb-3">Based on your progress, we recommend starting with:</p>
                        <div class="inline-flex items-center gap-2 bg-white rounded-lg px-4 py-2 border border-blue-200">
                            <i class="fas fa-layer-group text-blue-600"></i>
                            <span class="font-medium text-gray-900">{{ $recommendedLevel->level_name }}</span>
                            <span class="text-gray-500">Level</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>
@endpush
