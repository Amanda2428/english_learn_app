@extends('layouts.user')

@section('title', $level->level_name)
@section('subtitle', 'Master all skills at this level')

@section('content')
@php
    // Calculate additional stats
    $completedSkills = $userProgress->where('status', 'completed')->count();
    $inProgressSkills = $userProgress->where('status', 'in_progress')->count();
    $totalLessons = $level->skills->sum('videos_count') ?? 0;
    
    // Define level icons
    $levelIcons = ['fa-rocket', 'fa-graduation-cap', 'fa-brain', 'fa-chart-line', 'fa-globe', 'fa-microphone'];
    $levelIcon = $levelIcons[$level->level_order ?? 0 % count($levelIcons)];
@endphp

<div class="space-y-8">
    <!-- Enhanced Level Header with Hero Design -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl">
        <!-- Animated background patterns -->
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-16 -mb-16 animate-pulse delay-1000"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
            <div class="absolute bottom-1/3 right-1/4 w-40 h-40 bg-white/5 rounded-full animate-float delay-700"></div>
        </div>
        
        <div class="relative z-10 p-8 lg:p-12">
            <!-- Navigation breadcrumb -->
            <div class="flex items-center space-x-2 text-sm text-blue-100 mb-6">
                <a href="{{ route('user.levels.index') }}" class="hover:text-white transition-colors flex items-center">
                    <i class="fas fa-home mr-1"></i> Levels
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-white font-medium">{{ $level->level_name }}</span>
            </div>
            
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <!-- Level badge with CEFR level -->
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold inline-flex items-center">
                            <i class="fas fa-flag-checkered mr-2"></i>
                            {{ $level->cefr_level ?? 'Level ' . ($level->level_order ?? 1) }}
                        </span>
                        @if($levelProgress == 100)
                            <span class="px-4 py-1.5 bg-green-500/30 backdrop-blur-sm rounded-full text-sm font-semibold inline-flex items-center">
                                <i class="fas fa-crown mr-2"></i>
                                Mastered
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                        {{ $level->level_name }}
                    </h1>
                    
                    <p class="text-blue-100 text-lg max-w-2xl leading-relaxed">
                        {{ $level->description ?? 'Master English at this level with our comprehensive curriculum designed by language experts.' }}
                    </p>
                    
                    <!-- Quick stats cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">{{ $level->skills->count() }}</div>
                            <div class="text-xs text-blue-200">Total Skills</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">{{ $completedSkills }}</div>
                            <div class="text-xs text-blue-200">Completed</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">{{ $inProgressSkills }}</div>
                            <div class="text-xs text-blue-200">In Progress</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">{{ $totalLessons }}</div>
                            <div class="text-xs text-blue-200">Lessons</div>
                        </div>
                    </div>
                </div>
                
                <!-- Level illustration/image -->
                <div class="hidden lg:block w-64 h-64 bg-white/10 backdrop-blur-sm rounded-2xl p-6">
                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-white/20 to-white/5 flex items-center justify-center">
                        <i class="fas {{ $levelIcon }} text-6xl text-white/30"></i>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Level Progress -->
            @if($levelProgress > 0)
                <div class="mt-8 max-w-2xl">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-white">Overall Progress</span>
                            <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full text-white">
                                {{ $completedSkills }}/{{ $level->skills->count() }} skills
                            </span>
                        </div>
                        <span class="text-lg font-bold text-white">{{ $levelProgress }}%</span>
                    </div>
                    <div class="relative h-4 bg-white/20 rounded-full overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 via-orange-400 to-pink-400 rounded-full transition-all duration-1000 ease-out"
                             style="width: {{ $levelProgress }}%">
                        </div>
                        <!-- Animated sparkle effect -->
                        <div class="absolute top-0 bottom-0 w-20 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 animate-shimmer"
                             style="left: {{ $levelProgress }}%;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Skills Section with Tabs/Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-layer-group text-blue-600 mr-3"></i>
                    Skills to Master
                </h2>
                <p class="text-sm text-gray-500 mt-1">Complete all skills to master this level</p>
            </div>
            
            <!-- Filter tabs -->
            <div class="flex space-x-2 bg-gray-100 p-1 rounded-lg">
                <button class="filter-btn px-4 py-2 text-sm font-medium rounded-md bg-white text-gray-900 shadow-sm transition-all" data-filter="all">
                    All Skills
                </button>
                <button class="filter-btn px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900 transition-colors" data-filter="in_progress">
                    In Progress
                </button>
                <button class="filter-btn px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900 transition-colors" data-filter="not_started">
                    Not Started
                </button>
            </div>
        </div>
        
        <!-- Skills Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="skills-grid">
            @forelse($level->skills as $skill)
                @php
                    $progress = $userProgress[$skill->skill_id] ?? null;
                    $status = $progress ? $progress->status : 'not_started';
                    $completionPercentage = $progress ? $progress->completion_percentage : 0;
                    
                    $statusConfig = [
                        'completed' => [
                            'color' => 'emerald',
                            'bg' => 'emerald-50',
                            'text' => 'emerald-600',
                            'icon' => 'fa-check-circle',
                            'label' => 'Completed',
                            'action' => 'Review Skill'
                        ],
                        'in_progress' => [
                            'color' => 'blue',
                            'bg' => 'blue-50',
                            'text' => 'blue-600',
                            'icon' => 'fa-spinner',
                            'label' => 'In Progress',
                            'action' => 'Continue Learning'
                        ],
                        'not_started' => [
                            'color' => 'gray',
                            'bg' => 'gray-50',
                            'text' => 'gray-600',
                            'icon' => 'fa-circle',
                            'label' => 'Not Started',
                            'action' => 'Start Learning'
                        ]
                    ];
                    
                    $config = $statusConfig[$status];
                    
                    $videosCount = $skill->videos_count ?? rand(3, 8);
                    $questionsCount = $skill->questions_count ?? rand(5, 15);
                    $estimatedTime = $skill->estimated_minutes ?? 30;
                    
                    // Skill icons based on type
                    $skillType = $skill->type ?? 'grammar';
                    $skillIcons = [
                        'grammar' => 'fa-spell-check',
                        'vocabulary' => 'fa-book-open',
                        'listening' => 'fa-headphones',
                        'speaking' => 'fa-microphone',
                        'reading' => 'fa-book-reader',
                        'writing' => 'fa-pen-fancy'
                    ];
                    $skillIcon = $skillIcons[$skillType] ?? 'fa-circle-check';
                @endphp
                
                <a href="{{ route('user.skills.show', $skill) }}" 
                   class="skill-card group focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $config['color'] }}-500 rounded-xl"
                   data-status="{{ $status }}">
                    <div class="bg-white border-2 border-gray-100 rounded-xl overflow-hidden hover:border-{{ $config['color'] }}-200 hover:shadow-xl transition-all hover:-translate-y-1">
                        <div class="p-6">
                            <!-- Status bar -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $config['bg'] }} text-{{ $config['text'] }}">
                                        <i class="fas {{ $config['icon'] }} mr-1.5 text-xs"></i>
                                        {{ $config['label'] }}
                                    </span>
                                    @if($status === 'completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-600">
                                            <i class="fas fa-star mr-1.5 text-xs"></i>
                                            Mastered
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $estimatedTime }} min
                                </span>
                            </div>
                            
                            <!-- Skill header -->
                            <div class="flex items-start space-x-4 mb-4">
                                <div class="w-14 h-14 rounded-xl bg-{{ $config['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas {{ $skillIcon }} text-{{ $config['text'] }} text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-{{ $config['color'] }}-600 transition-colors mb-1">
                                        {{ $skill->skill_name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 line-clamp-2">
                                        {{ $skill->description ?? 'Master this essential skill with our comprehensive lessons.' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Stats with visual indicators -->
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="text-center p-2 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-bold text-gray-900">{{ $videosCount }}</div>
                                    <div class="text-xs text-gray-500 flex items-center justify-center">
                                        <i class="fas fa-video mr-1 text-xs"></i> Videos
                                    </div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-bold text-gray-900">{{ $questionsCount }}</div>
                                    <div class="text-xs text-gray-500 flex items-center justify-center">
                                        <i class="fas fa-question-circle mr-1 text-xs"></i> Questions
                                    </div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-bold text-gray-900">{{ $skill->xp_points ?? 100 }}</div>
                                    <div class="text-xs text-gray-500 flex items-center justify-center">
                                        <i class="fas fa-star mr-1 text-xs"></i> XP
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress for in-progress skills -->
                            @if($status === 'in_progress' && $completionPercentage > 0)
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-gray-600 font-medium">Lesson Progress</span>
                                        <span class="font-bold text-{{ $config['color'] }}-600">{{ $completionPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-{{ $config['color'] }}-500 to-{{ $config['color'] }}-400 rounded-full h-2.5 transition-all duration-500"
                                             style="width: {{ $completionPercentage }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Action button with hover effect -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-sm font-semibold text-{{ $config['color'] }}-600 group-hover:underline flex items-center">
                                    {{ $config['action'] }}
                                    <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                                </span>
                                @if($status === 'completed')
                                    <span class="text-xs text-emerald-600">
                                        <i class="fas fa-check-circle mr-1"></i>Mastered
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Color bar at bottom -->
                        <div class="h-1.5 bg-gradient-to-r from-{{ $config['color'] }}-500 to-{{ $config['color'] }}-400 w-0 group-hover:w-full transition-all duration-500"></div>
                    </div>
                </a>
            @empty
                <div class="col-span-2">
                    <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tools text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Skills Coming Soon</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">We're crafting engaging lessons for this level. Check back soon to start learning!</p>
                        <a href="{{ route('user.levels.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Browse Other Levels
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Learning Path Visualization -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div>
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-road mr-3 text-blue-400"></i>
                    Your Learning Path
                </h3>
                <p class="text-gray-400 mt-1">Follow the recommended order for best results</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-emerald-400 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-300">Completed</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-400 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-300">In Progress</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-300">Available</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-gray-600 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-300">Locked</span>
                </div>
            </div>
        </div>
        
        <div class="relative">
            <!-- Path line -->
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-700 -translate-y-1/2"></div>
            
            <!-- Path nodes -->
            <div class="relative flex justify-between">
                @foreach($level->skills->take(5) as $index => $skill)
                    @php
                        $skillProgress = $userProgress[$skill->skill_id] ?? null;
                        
                        // Determine if previous skill is completed
                        $isPreviousCompleted = false;
                        if ($index > 0) {
                            $prevSkill = $level->skills[$index - 1];
                            $prevProgress = $userProgress[$prevSkill->skill_id] ?? null;
                            $isPreviousCompleted = $prevProgress && $prevProgress->status === 'completed';
                        }
                        
                        // Determine node status
                        if ($skillProgress && $skillProgress->status === 'completed') {
                            $nodeStatus = 'completed';
                        } elseif ($skillProgress && $skillProgress->status === 'in_progress') {
                            $nodeStatus = 'in_progress';
                        } elseif ($index === 0 || $isPreviousCompleted) {
                            $nodeStatus = 'available';
                        } else {
                            $nodeStatus = 'locked';
                        }
                    @endphp
                    <div class="relative flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3
                            @if($nodeStatus == 'completed') bg-emerald-500
                            @elseif($nodeStatus == 'in_progress') bg-blue-500
                            @elseif($nodeStatus == 'available') bg-yellow-500
                            @else bg-gray-700
                            @endif
                            border-4 border-gray-800 z-10">
                            @if($nodeStatus == 'completed')
                                <i class="fas fa-check text-white"></i>
                            @elseif($nodeStatus == 'in_progress')
                                <i class="fas fa-spinner text-white"></i>
                            @elseif($nodeStatus == 'available')
                                <i class="fas fa-play text-white"></i>
                            @else
                                <i class="fas fa-lock text-gray-500"></i>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-center max-w-[100px] {{ $nodeStatus != 'locked' ? 'text-white' : 'text-gray-500' }}">
                            {{ $skill->skill_name }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        
        @if($level->skills->count() > 5)
            <div class="text-center mt-8">
                <span class="text-sm text-gray-400">
                    +{{ $level->skills->count() - 5 }} more skills available
                </span>
            </div>
        @endif
    </div>
    
    <!-- Enhanced Back Button -->
    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('user.levels.index') }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            Back to All Levels
        </a>
        
        @if($levelProgress == 100)
            <div class="flex items-center space-x-3">
                <span class="text-gray-600">🏆 You've mastered this level!</span>
                <a href="{{ route('user.levels.next', $level) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-colors group">
                    Next Level
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        @endif
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
    .delay-700 {
        animation-delay: 700ms;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%) skewX(-12deg); }
        100% { transform: translateX(200%) skewX(-12deg); }
    }
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                btn.classList.add('text-gray-600');
            });
            
            // Add active class to clicked button
            this.classList.remove('text-gray-600');
            this.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            
            // Get filter value
            const filter = this.dataset.filter;
            const cards = document.querySelectorAll('.skill-card');
            
            // Filter cards
            cards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
@endsection