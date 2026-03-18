@extends('layouts.user')

@section('title', 'Learning Levels')
@section('subtitle', 'Choose your level and start your English journey')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-8 text-white">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-10 -mt-10 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-5 rounded-full -ml-10 -mb-10 animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/3 w-20 h-20 bg-white opacity-5 rounded-full animate-float"></div>
        
        <div class="relative z-10">
            <div class="flex items-center space-x-3 mb-3">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                    🎯 {{ $levels->count() }} Levels Available
                </span>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                    ⚡ CEFR Standards
                </span>
            </div>
            <h2 class="text-3xl font-bold mb-2 flex items-center">
                <span class="bg-white/30 p-2 rounded-xl mr-3">📚</span>
                English Learning Levels
            </h2>
            <p class="text-blue-100 text-lg max-w-2xl">Select a level that matches your current English proficiency. Each level is designed to take you to the next stage of your learning journey.</p>
            
            <!-- Quick stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $totalLessons ?? 0 }}</div>
                    <div class="text-xs text-blue-200">Total Lessons</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $completedLevels ?? 0 }}</div>
                    <div class="text-xs text-blue-200">Completed</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $inProgress ?? 0 }}</div>
                    <div class="text-xs text-blue-200">In Progress</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <div class="text-2xl font-bold">{{ $totalSkills ?? 0 }}</div>
                    <div class="text-xs text-blue-200">Skills to Master</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Levels Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($levels as $level)
            @php
                $colors = [
                    'emerald', 'blue', 'violet', 'orange', 'rose', 'indigo'
                ];
                $color = $colors[$loop->index % count($colors)];
                
                $completedCount = $userProgress
                    ->filter(function($progress) use ($level) {
                        return ($progress->level_id ?? null) == $level->level_id && $progress->status === 'completed';
                    })->count();
                $totalSkills = $level->skills->count();
                $progressPercent = $totalSkills > 0 ? round(($completedCount / $totalSkills) * 100) : 0;
                
                // Determine level badge
                $levelBadge = $level->cefr_level ?? 
                    ($loop->index < 2 ? 'A1-A2' : ($loop->index < 4 ? 'B1-B2' : 'C1-C2'));
                    
                $isCurrentLevel = Auth::user()->level_id == $level->level_id;
            @endphp
            
            <div class="group focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $color }}-500 rounded-xl">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1 h-full flex flex-col">
                    <!-- Color Bar with gradient -->
                    <div class="h-2 bg-gradient-to-r from-{{ $color }}-500 to-{{ $color }}-400"></div>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <!-- Header with level badge -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium px-2 py-1 bg-{{ $color }}-50 text-{{ $color }}-700 rounded-full">
                                        {{ $levelBadge }}
                                    </span>
                                    @if($progressPercent == 100)
                                        <span class="text-xs font-medium px-2 py-1 bg-green-50 text-green-700 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>Completed
                                        </span>
                                    @elseif($progressPercent > 0)
                                        <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-700 rounded-full">
                                            <i class="fas fa-spinner mr-1"></i>In Progress
                                        </span>
                                    @endif
                                    @if($isCurrentLevel)
                                        <span class="text-xs font-medium px-2 py-1 bg-purple-50 text-purple-700 rounded-full">
                                            <i class="fas fa-arrow-right mr-1"></i>Current
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-{{ $color }}-600 transition-colors">
                                    {{ $level->level_name }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-tasks mr-1 text-{{ $color }}-400"></i>
                                    {{ $totalSkills }} skills to master
                                </p>
                            </div>
                            <div class="w-14 h-14 rounded-xl bg-{{ $color }}-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                @php
                                    $icons = ['fa-book-open', 'fa-graduation-cap', 'fa-brain', 'fa-message', 'fa-pen-fancy', 'fa-microphone'];
                                    $icon = $icons[$loop->index % count($icons)];
                                @endphp
                                <i class="fas {{ $icon }} text-{{ $color }}-500 text-2xl"></i>
                            </div>
                        </div>
                        
                        <!-- Enhanced Progress Bar -->
                        @if($progressPercent > 0)
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Overall Progress</span>
                                    <span class="font-bold text-{{ $color }}-600">{{ $progressPercent }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-{{ $color }}-500 to-{{ $color }}-400 rounded-full h-2.5 transition-all duration-500 ease-out transform group-hover:scale-x-105 origin-left" 
                                         style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1.5">
                                    <span>{{ $completedCount }} completed</span>
                                    <span>{{ $totalSkills - $completedCount }} remaining</span>
                                </div>
                            </div>
                        @else
                            <div class="mb-4 h-12 flex items-center">
                                <div class="w-full bg-gray-50 rounded-lg p-2 text-center">
                                    <span class="text-sm text-gray-500">Not started yet</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Skills Preview -->
                        <div class="space-y-2.5 mb-4 flex-1">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span class="font-medium">Key skills</span>
                                <span>{{ $level->skills->count() }} total</span>
                            </div>
                            @foreach($level->skills->take(3) as $skill)
                                @php
                                    $progress = $userProgress[$level->level_id . '-' . $skill->skill_id] ?? null;
                                    $isCompleted = $progress && $progress->status === 'completed';
                                @endphp
                                <div class="flex items-center text-sm group/skill">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center mr-2
                                        {{ $isCompleted ? 'bg-' . $color . '-100' : 'bg-gray-100' }}">
                                        <i class="fas fa-{{ $isCompleted ? 'check' : 'circle' }} 
                                            text-{{ $isCompleted ? $color : 'gray' }}-500 
                                            text-{{ $isCompleted ? 'xs' : '2xs' }}"></i>
                                    </div>
                                    <span class="text-gray-700 {{ $isCompleted ? 'line-through text-gray-400' : '' }} flex-1">
                                        {{ $skill->skill_name }}
                                    </span>
                                    @if($isCompleted)
                                        <span class="text-xs text-green-600 ml-2">Done!</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($level->skills->count() > 3)
                                <div class="text-sm text-gray-500 pl-7 italic">
                                    +{{ $level->skills->count() - 3 }} more skills
                                </div>
                            @endif
                        </div>
                        
                        <!-- Action Button with Level Selection -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            @if($isCurrentLevel)
                                <span class="text-sm font-semibold text-{{ $color }}-600 flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                    Current Level
                                </span>
                                <a href="{{ route('user.levels.show', $level) }}" 
                                   class="text-sm font-semibold text-{{ $color }}-600 hover:underline flex items-center group">
                                    Continue Learning
                                    <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @else
                                <form action="{{ route('user.levels.select', $level) }}" method="POST" class="flex-1 flex items-center justify-between">
                                    @csrf
                                    <span class="text-sm text-gray-600">
                                        {{ $progressPercent > 0 ? 'Progress: ' . $progressPercent . '%' : 'New level' }}
                                    </span>
                                    <button type="submit" 
                                            class="text-sm font-semibold text-{{ $color }}-600 hover:underline flex items-center group">
                                        {{ $progressPercent > 0 ? 'Continue Learning' : 'Start Learning' }}
                                        <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </form>
                            @endif
                            <div class="flex items-center space-x-1 text-{{ $color }}-400 ml-2">
                                <i class="fas fa-clock text-xs"></i>
                                <span class="text-xs">{{ $level->estimated_hours ?? '8-10' }} hours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-layer-group text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Levels Available</h3>
                    <p class="text-gray-500 mb-6">We're working hard to add new learning content. Check back soon!</p>
                    <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-bell mr-2"></i>
                        Notify me when available
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Motivational Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row items-center justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-trophy text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg">Ready to challenge yourself?</h4>
                    <p class="text-purple-100">Complete all levels to earn the "English Master" badge</p>
                </div>
            </div>
            <a href="#" class="px-6 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                View Leaderboard
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .delay-1000 {
        animation-delay: 1000ms;
    }
</style>
@endpush
@endsection