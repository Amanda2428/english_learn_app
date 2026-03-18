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
                                <circle class="text-white/20" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"/>
                                <circle class="text-white" stroke-width="4" stroke="currentColor" fill="transparent" r="40" cx="48" cy="48"
                                        stroke-dasharray="{{ 2 * pi() * 40 }}"
                                        stroke-dashoffset="{{ 2 * pi() * 40 * (1 - $progress->completion_percentage / 100) }}"/>
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
        <!-- Left Column - Videos -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Videos Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">📺 Learning Videos</h2>
                </div>
                
                @if($skill->videos->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($skill->videos as $video)
                            @php
                                $isWatched = session()->get('watched_videos_' . $skill->skill_id, []);
                                $watched = in_array($video->video_id, $isWatched);
                            @endphp
                            
                            <a href="{{ route('user.skills.video', [$skill, $video]) }}" class="block p-6 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-start space-x-4">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-32 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                            @php
                                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video->video_file, $matches);
                                                $youtubeId = $matches[1] ?? null;
                                            @endphp
                                            
                                            @if($youtubeId)
                                                <img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg" alt="{{ $video->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                    <i class="fas fa-video text-gray-500"></i>
                                                </div>
                                            @endif
                                        </div>
                                        @if($watched)
                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $video->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($video->description, 100) }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-xs text-gray-500">
                                                <i class="far fa-clock mr-1"></i> {{ $video->duration }}
                                            </span>
                                            @if($watched)
                                                <span class="text-xs text-green-600">
                                                    <i class="fas fa-check-circle mr-1"></i> Watched
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <i class="fas fa-play-circle text-3xl text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <i class="fas fa-video text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">No videos available for this skill yet.</p>
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
@endsection