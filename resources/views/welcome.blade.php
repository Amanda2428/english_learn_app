@extends('layouts.user')

@section('title', 'Welcome to FluentEdge')
@section('subtitle', 'Master English Skills with Personalized Learning')



@section('content')
    <!-- Hero Section with Auto-sliding Images -->
    <div x-data="{
        images: [
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80',
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80',
            'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1546&q=80'
        ],
        current: 0,
        init() {
            setInterval(() => {
                this.current = (this.current + 1) % this.images.length;
            }, 5000);
        }
    }"
        class="relative rounded-2xl overflow-hidden mb-8 sm:mb-12 lg:mb-16 h-[400px] sm:h-[500px] lg:h-[600px]">

        <!-- Background Images -->
        <template x-for="(image, index) in images" :key="index">
            <div x-show="current === index" x-transition:enter="transition-opacity duration-1000"
                x-transition:leave="transition-opacity duration-1000"
                class="absolute inset-0 bg-cover bg-center scale-105 animate-[zoom_18s_ease-in-out_infinite]"
                :style="`background-image: url('${image}')`"></div>
        </template>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-700/80 via-blue-600/70 to-indigo-700/80"></div>

        <!-- Content -->
        <div class="relative z-10 flex items-center justify-center h-full px-4 sm:px-6 text-center">
            <div class="max-w-4xl">
                <span
                    class="inline-block mb-4 sm:mb-6 px-3 sm:px-4 py-1 sm:py-2 rounded-full bg-white/20 text-white text-xs sm:text-sm tracking-wide backdrop-blur-sm">
                    🇬🇧 Your Complete English Learning Platform
                </span>

                <h1
                    class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white mb-4 sm:mb-6 leading-tight">
                    Master English,<br class="hidden sm:block"> One Skill at a Time
                </h1>

                <p
                    class="text-base sm:text-lg lg:text-xl text-white/90 mb-6 sm:mb-8 lg:mb-12 leading-relaxed max-w-3xl mx-auto px-4">
                    From Elementary to Advanced levels. Practice Speaking, Listening, Reading, and Writing
                    with interactive lessons and real-world resources.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 lg:gap-5 justify-center px-4">
                    @auth
                        @if (auth()->user()->isUser())
                            <a href="{{ route('welcome') }}"
                                class="group px-6 sm:px-8 py-2 sm:py-3 bg-white text-blue-600 rounded-xl sm:rounded-2xl font-semibold
                                      shadow-xl hover:shadow-2xl hover:-translate-y-1
                                      transition-all duration-300 text-sm sm:text-base">
                                <i class="fas fa-chart-line mr-2"></i>
                                Continue Learning
                            </a>
                            <a href="{{ route('user.skills.index') }}"
                                class="group px-6 sm:px-8 py-2 sm:py-3 border-2 border-white text-white rounded-xl sm:rounded-2xl font-semibold
                                      hover:bg-white hover:text-blue-600 hover:-translate-y-1 transition-all duration-300 text-sm sm:text-base">
                                <i class="fas fa-book-open mr-2"></i>
                                Browse Skills
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                            class="group px-6 sm:px-8 py-2 sm:py-3 bg-white text-blue-600 rounded-xl sm:rounded-2xl font-semibold
                                  shadow-xl hover:shadow-2xl hover:-translate-y-1
                                  transition-all duration-300 text-sm sm:text-base">
                            <i class="fas fa-user-plus mr-2"></i>
                            Start Learning Free
                        </a>
                        <a href="{{ route('user.levels.index') }}"
                            class="group px-6 sm:px-8 py-2 sm:py-3 border-2 border-white text-white rounded-xl sm:rounded-2xl font-semibold
                                  hover:bg-white hover:text-blue-600 hover:-translate-y-1 transition-all duration-300 text-sm sm:text-base">
                            <i class="fas fa-layer-group mr-2"></i>
                            Explore Levels
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 text-white/80 animate-bounce">
            <i class="fas fa-chevron-down text-lg sm:text-xl"></i>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="mb-12 sm:mb-16">

        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 text-center mb-6 sm:mb-8">Our Platform at a Glance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border text-center hover:shadow-md transition-all group">
                <div
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-1 sm:mb-2 group-hover:scale-110 transition-transform">
                    {{ $stats['total_skills'] }}
                </div>
                <div class="font-medium text-gray-900 text-sm sm:text-base">English Skills</div>
                <div class="text-xs sm:text-sm text-gray-500">To master</div>
            </div>

            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border text-center hover:shadow-md transition-all group">
                <div
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-1 sm:mb-2 group-hover:scale-110 transition-transform">
                    {{ $stats['total_videos'] }}+
                </div>
                <div class="font-medium text-gray-900 text-sm sm:text-base">Video Lessons</div>
                <div class="text-xs sm:text-sm text-gray-500">YouTube resources</div>
            </div>

            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border text-center hover:shadow-md transition-all group">
                <div
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-1 sm:mb-2 group-hover:scale-110 transition-transform">
                    {{ $stats['total_questions'] }}+
                </div>
                <div class="font-medium text-gray-900 text-sm sm:text-base">Practice Questions</div>
                <div class="text-xs sm:text-sm text-gray-500">Test your knowledge</div>
            </div>

            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border text-center hover:shadow-md transition-all group">
                <div
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-1 sm:mb-2 group-hover:scale-110 transition-transform">
                    {{ number_format($stats['active_users']) }}+
                </div>
                <div class="font-medium text-gray-900 text-sm sm:text-base">Active Learners</div>
                <div class="text-xs sm:text-sm text-gray-500">Growing daily</div>
            </div>
        </div>
    </div>

    <!-- English Levels Section - Dynamic from Database -->
    <div class="mb-12 sm:mb-16">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">English Levels</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Choose your level and start your journey</p>
            </div>
            <a href="{{ route('user.levels.index') }}"
                class="text-blue-600 hover:text-blue-800 font-medium group text-sm sm:text-base">
                View All Levels <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse($levels->take(3) as $level)
                @php
                    $colors = ['green', 'blue', 'red'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp
                <div
                    class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-xl transition-all hover:-translate-y-1">
                    <div class="h-2 bg-{{ $color }}-500"></div>
                    <div class="p-4 sm:p-6">
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 sm:mb-4 gap-2">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">{{ $level->level_name }}</h3>
                            <span
                                class="px-2 sm:px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-700 rounded-full text-xs font-medium">
                                {{ $level->skills_count ?? $level->skills->count() }} Skills
                            </span>
                        </div>
                        <p class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4">
                            {{ $level->description ?? 'Master English at this level with our comprehensive curriculum.' }}
                        </p>
                        <div class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
                            @foreach ($level->skills->take(3) as $skill)
                                <div class="flex items-center text-xs sm:text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-{{ $color }}-500 mr-2"></i>
                                    <span>{{ $skill->skill_name }}</span>
                                </div>
                            @endforeach
                            @if ($level->skills->count() > 3)
                                <div class="text-xs sm:text-sm text-gray-500">
                                    +{{ $level->skills->count() - 3 }} more skills
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('user.levels.show', $level) }}"
                            class="text-{{ $color }}-600 hover:text-{{ $color }}-700 font-medium text-xs sm:text-sm">
                            Explore {{ $level->level_name }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 sm:py-12 bg-gray-50 rounded-lg">
                    <p class="text-sm sm:text-base text-gray-500">No levels available yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Core English Skills Section - Dynamic from Database -->
    <div class="mb-12 sm:mb-16">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Core English Skills</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Master all aspects of the English language</p>
            </div>
            <a href="{{ route('user.skills.index') }}"
                class="text-blue-600 hover:text-blue-800 font-medium group text-sm sm:text-base">
                All Skills <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($popularSkills->take(4) as $skill)
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
                @endphp
                <div
                    class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border hover:shadow-lg transition-all hover:-translate-y-1 group">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-{{ $color }}-100 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-{{ $icon }} text-{{ $color }}-600 text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1 sm:mb-2">{{ $skill->skill_name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3">
                        {{ $skill->description ?? 'Master this essential English skill' }}</p>
                    <div class="flex flex-wrap gap-1 sm:gap-2 mb-3 sm:mb-4">
                        <span
                            class="px-2 py-1 bg-{{ $color }}-50 text-{{ $color }}-700 rounded-full text-xs">
                            {{ $skill->videos_count }} Videos
                        </span>
                        <span
                            class="px-2 py-1 bg-{{ $color }}-50 text-{{ $color }}-700 rounded-full text-xs">
                            {{ $skill->questions_count }} Questions
                        </span>
                    </div>
                    <a href="{{ route('user.skills.select-level', $skill) }}"
                        class="text-{{ $color }}-600 hover:text-{{ $color }}-700 font-medium text-xs sm:text-sm inline-flex items-center">
                        Start Practicing <i class="fas fa-arrow-right ml-1 sm:ml-2 text-xs"></i>
                    </a>
                </div>
            @empty
                <div class="col-span-4 text-center py-8 sm:py-12 bg-gray-50 rounded-lg">
                    <p class="text-sm sm:text-base text-gray-500">No skills available yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- How It Works Section with Chatbot Preview (Shown to everyone) -->
    <section id="how-it-works" class="py-12 sm:py-16 lg:py-20 bg-gray-50 rounded-2xl sm:rounded-3xl mb-12 sm:mb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 lg:mb-16">
                <span
                    class="inline-block px-3 sm:px-4 py-1 sm:py-2 bg-blue-100 text-blue-600 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4">
                    🚀 Your Learning Journey
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-4">
                    How FluentEdge Works
                </h2>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto px-4">
                    Your personalized English learning journey in four simple steps
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mb-8 sm:mb-12 lg:mb-16">
                <!-- Step 1 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg hover:shadow-xl transition-shadow relative z-10 border border-gray-100">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg sm:text-xl font-bold mb-3 sm:mb-4">
                            1
                        </div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Assess Your Level
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600">Take our quick placement test to determine your current
                            English level.</p>
                    </div>
                    <div
                        class="hidden lg:block absolute top-1/2 left-full w-full h-0.5 bg-blue-200 -translate-y-1/2 -translate-x-8">
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg hover:shadow-xl transition-shadow relative z-10 border border-gray-100">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg sm:text-xl font-bold mb-3 sm:mb-4">
                            2
                        </div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Choose Your Skills
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600">Select the specific skills you want to improve -
                            Speaking, Listening, Reading, or Writing.</p>
                    </div>
                    <div
                        class="hidden lg:block absolute top-1/2 left-full w-full h-0.5 bg-blue-200 -translate-y-1/2 -translate-x-8">
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg hover:shadow-xl transition-shadow relative z-10 border border-gray-100">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg sm:text-xl font-bold mb-3 sm:mb-4">
                            3
                        </div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Learn & Practice
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600">Access curated YouTube lessons, take quizzes, and
                            practice with interactive exercises.</p>
                    </div>
                    <div
                        class="hidden lg:block absolute top-1/2 left-full w-full h-0.5 bg-blue-200 -translate-y-1/2 -translate-x-8">
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg hover:shadow-xl transition-shadow relative z-10 border border-gray-100">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-lg sm:text-xl font-bold mb-3 sm:mb-4">
                            4
                        </div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-1 sm:mb-2">Track Progress
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600">Monitor your improvement, earn points, and get
                            personalized recommendations.</p>
                    </div>
                </div>
            </div>

            <!-- Chatbot Preview - This will show for all users -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 sm:px-6 py-3 sm:py-4 flex items-center">
                    <div class="flex space-x-1.5 sm:space-x-2 mr-3 sm:mr-4">
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-red-400 rounded-full"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-yellow-400 rounded-full"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-400 rounded-full"></div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-white text-xs sm:text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm sm:text-base">English Tutor Bot</h3>
                            <p class="text-blue-100 text-xs">@guest For registered users only
                                @else
                                Online • Ready to help @endguest
                            </p>
                        </div>
                    </div>
                </div>

               <div class="p-4 sm:p-6 bg-gray-50">
    <div class="space-y-4 max-w-3xl mx-auto">
        <div class="flex justify-start">
            <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-md">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-800 whitespace-pre-line">{!! $botIntro !!}</p>
                </div>
            </div>
        </div>

        <div class="text-xs text-gray-500 mt-4 mb-2">Available in our database:</div>
        <div class="flex flex-wrap gap-2 justify-center">
            @php
                $btnLevels = \App\Models\Level::take(3)->get();
                $btnSkills = \App\Models\Skill::where('status', true)->take(3)->get();
            @endphp

            @foreach($btnLevels as $l)
                @foreach($btnSkills as $s)
                    <button onclick="sendQuickMessageFromPreview('I want to study {{ $l->level_name }} {{ $s->skill_name }}')"
                        class="px-3 py-1 bg-white text-blue-600 border border-blue-200 rounded-full text-xs hover:bg-blue-50 transition shadow-sm">
                        {{ $l->level_name }} {{ $s->skill_name }}
                    </button>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
            </div>
        </div>
    </section>

    <!-- For logged-in users, show personalized section -->
    @auth
        @if (auth()->user()->isUser())
            <div
                class="mb-12 sm:mb-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl sm:rounded-2xl p-6 sm:p-8 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-32 sm:w-48 h-32 sm:h-48 bg-white/10 rounded-full translate-y-24 -translate-x-24">
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6">
                    <div class="text-white text-center sm:text-left">
                        <h3 class="text-xl sm:text-2xl font-bold mb-1 sm:mb-2">🎯 Continue Your English Journey</h3>
                        <p class="text-sm sm:text-base text-blue-100">You've made great progress. Keep practicing!</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <a href="{{ route('user.progress.index') }}"
                            class="px-5 sm:px-6 py-2 sm:py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-medium shadow-lg transition-all group text-sm sm:text-base text-center">
                            <i class="fas fa-chart-line mr-2"></i> My Dashboard
                        </a>
                        <a href="{{ route('user.skills.index') }}"
                            class="px-5 sm:px-6 py-2 sm:py-3 border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 font-medium transition-all group text-sm sm:text-base text-center">
                            <i class="fas fa-book-open mr-2"></i> Browse Skills
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Leaderboard Section -->
    <div id="leaderboard" class="mb-12 sm:mb-16">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>Leaderboard
                </h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Top learners with the highest points</p>
            </div>
            @auth
                @if (auth()->user()->isUser())
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                        Your points: {{ number_format($userPoints ?? 0) }}
                    </div>
                @endif
            @endauth
        </div>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <!-- Leaderboard Header -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-4 sm:px-6 py-3 sm:py-4">
                <div class="grid grid-cols-12 gap-2 sm:gap-4">
                    <div class="col-span-1 text-center">
                        <span class="text-white font-bold text-sm sm:text-base">#</span>
                    </div>
                    <div class="col-span-6 sm:col-span-5">
                        <span class="text-white font-bold text-sm sm:text-base">Learner</span>
                    </div>
                    <div class="col-span-3 sm:col-span-3 text-center">
                        <span class="text-white font-bold text-sm sm:text-base">Skills</span>
                    </div>
                    <div class="col-span-2 sm:col-span-3 text-right">
                        <span class="text-white font-bold text-sm sm:text-base">
                            <i class="fas fa-star mr-1"></i>Points
                        </span>
                    </div>
                </div>
            </div>

            <!-- Leaderboard Body -->
            <div class="divide-y divide-gray-100">
                @forelse($leaderboard as $index => $learner)
                    @php
                        $medalColors = ['gold', 'silver', '#cd7f32'];
                        $medalIcon = '';
                        $rowBg = '';

                        if ($index == 0) {
                            $medalIcon = '<i class="fas fa-crown text-yellow-500"></i>';
                            $rowBg = 'bg-yellow-50';
                        } elseif ($index == 1) {
                            $medalIcon = '<i class="fas fa-medal text-gray-400"></i>';
                            $rowBg = 'bg-gray-50';
                        } elseif ($index == 2) {
                            $medalIcon = '<i class="fas fa-medal text-orange-600"></i>';
                            $rowBg = 'bg-orange-50';
                        }

                        $isCurrentUser = auth()->check() && $learner->user_id == auth()->id();
                        if ($isCurrentUser) {
                            $rowBg = 'bg-blue-50';
                        }

                        // Get user profile picture if exists
                        $userModel = \App\Models\User::find($learner->user_id);
                        $hasProfilePicture = $userModel && $userModel->profile;
                    @endphp
                    <div class="{{ $rowBg }} transition-colors hover:bg-gray-50">
                        <div class="grid grid-cols-12 gap-2 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 items-center">
                            <!-- Rank -->
                            <div class="col-span-1 text-center">
                                @if ($index == 0)
                                    <div class="flex justify-center">
                                        <div
                                            class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-xs sm:text-sm">1</span>
                                        </div>
                                    </div>
                                @elseif($index == 1)
                                    <div class="flex justify-center">
                                        <div
                                            class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-400 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-xs sm:text-sm">2</span>
                                        </div>
                                    </div>
                                @elseif($index == 2)
                                    <div class="flex justify-center">
                                        <div
                                            class="w-6 h-6 sm:w-8 sm:h-8 bg-orange-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-xs sm:text-sm">3</span>
                                        </div>
                                    </div>
                                @else
                                    <span
                                        class="text-gray-500 font-semibold text-sm sm:text-base">{{ $index + 1 }}</span>
                                @endif
                            </div>

                            <!-- User Info with Profile Picture -->
                            <div class="col-span-6 sm:col-span-5">
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <!-- Profile Picture or Initial Avatar -->
                                    @if ($hasProfilePicture)
                                        <img src="{{ Storage::url($userModel->profile) }}" alt="{{ $learner->name }}"
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover shadow-md">
                                    @else
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
                                            {{ substr($learner->name, 0, 1) }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                                            {{ $learner->name }}
                                            @if ($isCurrentUser)
                                                <span
                                                    class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">You</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ $learner->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Count -->
                            <div class="col-span-3 sm:col-span-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs sm:text-sm">
                                    <i class="fas fa-book mr-1"></i>
                                    {{ $learner->skills_count }}
                                </span>
                            </div>

                            <!-- Points -->
                            <div class="col-span-2 sm:col-span-3 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <span class="font-bold text-yellow-600 text-sm sm:text-base">
                                        {{ number_format($learner->total_points) }}
                                    </span>
                                    <i class="fas fa-star text-yellow-400 text-xs sm:text-sm"></i>
                                </div>
                                @if ($index == 0 && $learner->total_points > 0)
                                    <div class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-chart-line"></i> Top Learner
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 sm:py-12">
                        <i class="fas fa-chart-line text-4xl sm:text-5xl text-gray-300 mb-3 sm:mb-4"></i>
                        <p class="text-sm sm:text-base text-gray-500">No data available yet. Start learning to get on the
                            leaderboard!</p>
                        @auth
                            @if (auth()->user()->isUser())
                                <a href="{{ route('user.skills.index') }}"
                                    class="inline-flex items-center mt-3 sm:mt-4 text-blue-600 hover:text-blue-700">
                                    Start Learning <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- Footer with call to action -->
            @if ($leaderboard->count() > 0)
                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100">
                    <div class="flex justify-between items-center text-xs sm:text-sm text-gray-600">
                        <span>
                            <i class="fas fa-users mr-1"></i>
                            Top {{ $leaderboard->count() }} learners
                        </span>
                        <span>
                            <i class="fas fa-chart-line mr-1"></i>
                            Keep practicing to climb the ranks!
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- CTA Section -->
    <div
        class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl sm:rounded-2xl p-6 sm:p-8 text-center relative overflow-hidden">
        <div
            class="absolute top-0 left-0 w-24 sm:w-32 h-24 sm:h-32 bg-white/10 rounded-full -translate-x-12 sm:-translate-x-16 -translate-y-12 sm:-translate-y-16">
        </div>
        <div
            class="absolute bottom-0 right-0 w-24 sm:w-32 h-24 sm:h-32 bg-white/10 rounded-full translate-x-12 sm:translate-x-16 translate-y-12 sm:translate-y-16">
        </div>

        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-3 sm:mb-4">
                @auth
                    @if (auth()->user()->isUser())
                        Ready to Master English?
                    @else
                        Start Your English Journey Today
                    @endif
                @else
                    Start Your English Journey Today
                @endauth
            </h2>
            <p class="text-sm sm:text-base lg:text-xl text-blue-100 mb-6 sm:mb-8 max-w-2xl mx-auto px-4">
                @auth
                    @if (auth()->user()->isUser())
                        Continue practicing and track your progress to fluency.
                    @else
                        Join thousands of learners mastering English skills every day.
                    @endif
                @else
                    Join thousands of learners and start speaking English with confidence.
                @endauth
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4">
                @auth
                    @if (auth()->user()->isUser())
                        <a href="{{ route('user.skills.index') }}"
                            class="px-5 sm:px-6 py-2 sm:py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-medium shadow-lg transition-all group text-sm sm:text-base">
                            <i class="fas fa-book-open mr-2"></i> Browse All Skills
                        </a>
                        <button onclick="document.getElementById('chatbot-toggle')?.click()"
                            class="px-5 sm:px-6 py-2 sm:py-3 border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 font-medium transition-all group text-sm sm:text-base">
                            <i class="fas fa-robot mr-2"></i> Chat with Tutor Bot
                        </button>
                    @endif
                @else
                    <a href="{{ route('register') }}"
                        class="px-5 sm:px-6 py-2 sm:py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-medium shadow-lg transition-all group text-sm sm:text-base">
                        <i class="fas fa-user-plus mr-2"></i> Create Free Account
                    </a>
                    <a href="{{ route('user.levels.index') }}"
                        class="px-5 sm:px-6 py-2 sm:py-3 border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 font-medium transition-all group text-sm sm:text-base">
                        <i class="fas fa-compass mr-2"></i> Explore Levels
                    </a>
                @endauth
            </div>
            @guest
                <p class="mt-3 sm:mt-4 text-xs sm:text-sm text-blue-200">
                    <i class="fas fa-check-circle mr-1"></i> No credit card required. Free forever.
                </p>
            @endguest
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes zoom {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function sendQuickMessageFromPreview(message) {
            const chatbotToggle = document.getElementById('chatbot-toggle');
            if (chatbotToggle) {
                chatbotToggle.click();
                setTimeout(() => {
                    const input = document.getElementById('chat-input');
                    if (input) {
                        input.value = message;
                        if (typeof sendMessage === 'function') {
                            setTimeout(() => sendMessage(), 500);
                        }
                    }
                }, 500);
            }
        }
        if (window.location.hash) {
            const element = document.querySelector(window.location.hash);
            if (element) {
                setTimeout(() => {
                    const offset = 80;
                    const elementPosition = element.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        }
    </script>
@endpush
