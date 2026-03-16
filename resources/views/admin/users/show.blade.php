@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Profile</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <div class="h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                        <span class="text-white text-3xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="mt-4 sm:mt-0 sm:ml-6 flex-1">
                    <div class="flex items-center">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        @if($user->isAdmin())
                            <span class="ml-3 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Admin</span>
                        @else
                            <span class="ml-3 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">User</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500">Member since {{ $user->created_at->format('F d, Y') }}</span>
                        @if($user->email_verified_at)
                            <span class="ml-3 flex items-center text-sm text-green-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verified
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-4 sm:mt-0 flex space-x-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($user->total_points ?? $totalPoints) }}</p>
                        <p class="text-xs text-gray-500">Total Points</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $chatSessionsCount }}</p>
                        <p class="text-xs text-gray-500">Chat Sessions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Tabs -->
        <div class="border-t border-gray-200">
            <div class="px-6 py-3 bg-gray-50">
                <nav class="flex space-x-8">
                    <button onclick="showTab('overview')" class="tab-btn active px-3 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                        Overview
                    </button>
                    <button onclick="showTab('progress')" class="tab-btn px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Learning Progress
                    </button>
                    <button onclick="showTab('chat')" class="tab-btn px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Chat History
                    </button>
                    <button onclick="showTab('activity')" class="tab-btn px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Activity Log
                    </button>
                </nav>
            </div>

            <!-- Overview Tab -->
            <div id="overview-tab" class="tab-content p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">User ID</span>
                                <span class="text-sm font-medium text-gray-900">#{{ $user->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current Level</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $user->level->level_name ?? 'Not assigned' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Points</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($user->total_points ?? $totalPoints) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Last Active</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $user->last_activity ? $user->last_activity->diffForHumans() : 'Never' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Bio</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700">{{ $user->bio ?: 'No bio provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Tab -->
            <div id="progress-tab" class="tab-content hidden p-6">
                <div class="space-y-6">
                    <!-- Level Progress Overview -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Level Progress Overview</h3>
                        
                        <!-- Level Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($allLevels as $level)
                                @php
                                    // Get user's progress for this level
                                    $levelProgress = $user->progress->where('level_id', $level->level_id);
                                    
                                    // Calculate level statistics
                                    $totalSkillsInLevel = $level->skills->count();
                                    $completedSkillsInLevel = $levelProgress->where('status', 'completed')->count();
                                    $inProgressSkillsInLevel = $levelProgress->where('status', 'in_progress')->count();
                                    
                                    // Calculate points earned in this level
                                    $pointsInLevel = $levelProgress->sum('points_earned');
                                    
                                    // Calculate overall level progress percentage
                                    $levelProgressPercentage = $totalSkillsInLevel > 0 
                                        ? round(($completedSkillsInLevel / $totalSkillsInLevel) * 100, 1)
                                        : 0;
                                        
                                    // Determine status class
                                    if ($completedSkillsInLevel == $totalSkillsInLevel && $totalSkillsInLevel > 0) {
                                        $statusClass = 'border-green-500 bg-green-50';
                                        $statusText = 'Completed';
                                        $statusColor = 'text-green-600';
                                        $progressColor = 'bg-green-500';
                                    } elseif ($inProgressSkillsInLevel > 0) {
                                        $statusClass = 'border-yellow-500 bg-yellow-50';
                                        $statusText = 'In Progress';
                                        $statusColor = 'text-yellow-600';
                                        $progressColor = 'bg-yellow-500';
                                    } else {
                                        $statusClass = 'border-gray-200 bg-gray-50';
                                        $statusText = 'Not Started';
                                        $statusColor = 'text-gray-500';
                                        $progressColor = 'bg-gray-400';
                                    }
                                @endphp
                                
                                <div class="border-2 rounded-xl p-5 {{ $statusClass }} hover:shadow-lg transition-all duration-300">
                                    <!-- Level Header -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Level {{ $level->level_order }}
                                            </span>
                                            <h4 class="text-lg font-bold text-gray-900 mt-1">{{ $level->level_name }}</h4>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }} bg-white border {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    
                                    <!-- Level Description -->
                                    @if($level->description)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $level->description }}</p>
                                    @endif
                                    
                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-600">Overall Progress</span>
                                            <span class="font-semibold {{ $statusColor }}">{{ $levelProgressPercentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="{{ $progressColor }} h-2.5 rounded-full transition-all duration-500" 
                                                 style="width: {{ $levelProgressPercentage }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Level Stats Grid -->
                                    <div class="grid grid-cols-3 gap-2 text-center mb-4">
                                        <div class="bg-white rounded-lg p-2">
                                            <p class="text-lg font-bold text-indigo-600">{{ $totalSkillsInLevel }}</p>
                                            <p class="text-xs text-gray-500">Total Skills</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2">
                                            <p class="text-lg font-bold text-green-600">{{ $completedSkillsInLevel }}</p>
                                            <p class="text-xs text-gray-500">Completed</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-2">
                                            <p class="text-lg font-bold text-yellow-600">{{ $inProgressSkillsInLevel }}</p>
                                            <p class="text-xs text-gray-500">In Progress</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Points Earned -->
                                    <div class="flex items-center justify-between border-t border-gray-200 pt-3">
                                        <span class="text-sm text-gray-600">Points Earned</span>
                                        <span class="font-bold text-purple-600">{{ number_format($pointsInLevel) }}</span>
                                    </div>
                                    
                                    <!-- View Details Button -->
                                    @if($totalSkillsInLevel > 0)
                                        <button onclick="toggleLevelDetails({{ $level->level_id }})" 
                                                class="w-full mt-3 text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                            <span>View Skills Details</span>
                                            <svg class="w-4 h-4 ml-1 transition-transform duration-300" id="arrow-{{ $level->level_id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Skills Details (Hidden by default) -->
                                        <div id="level-{{ $level->level_id }}-details" class="hidden mt-4 space-y-3">
                                            @foreach($level->skills as $skill)
                                                @php
                                                    $skillProgress = $user->progress->where('skill_id', $skill->skill_id)->first();
                                                    $skillStatus = $skillProgress ? $skillProgress->status : 'not_started';
                                                    $skillPercentage = $skillProgress ? $skillProgress->completion_percentage : 0;
                                                    $skillPoints = $skillProgress ? $skillProgress->points_earned : 0;
                                                    
                                                    $skillStatusClass = $skillStatus == 'completed' ? 'text-green-600' : 
                                                                       ($skillStatus == 'in_progress' ? 'text-yellow-600' : 'text-gray-400');
                                                @endphp
                                                
                                                <div class="bg-white rounded-lg p-3 border border-gray-100">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm font-medium text-gray-800">{{ $skill->skill_name }}</span>
                                                        <span class="text-xs {{ $skillStatusClass }} font-medium">
                                                            {{ ucfirst(str_replace('_', ' ', $skillStatus)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-xs">
                                                        <div class="flex-1 mr-3">
                                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $skillPercentage }}%"></div>
                                                            </div>
                                                        </div>
                                                        <span class="text-purple-600 font-medium">{{ $skillPoints }} pts</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Skills Progress Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- In Progress Skills -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills In Progress</h3>
                            <div class="bg-gray-50 rounded-lg p-4 max-h-80 overflow-y-auto">
                                @php
                                    $inProgress = $user->progress->where('status', 'in_progress');
                                @endphp
                                
                                @forelse($inProgress as $progress)
                                    <div class="mb-4 last:mb-0 p-3 bg-white rounded-lg border border-yellow-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $progress->skill->skill_name ?? 'Unknown Skill' }}</span>
                                                @if($progress->level)
                                                    <span class="ml-2 text-xs text-gray-500">({{ $progress->level->level_name }})</span>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">In Progress</span>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="mb-2">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-600">Progress</span>
                                                <span class="font-medium text-yellow-600">{{ $progress->completion_percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $progress->completion_percentage }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Stats -->
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>Videos: {{ $progress->videos_watched }}/{{ $progress->total_videos_in_skill }}</span>
                                            <span>Questions: {{ $progress->questions_answered }}/{{ $progress->total_questions_in_skill }}</span>
                                            <span class="text-purple-600 font-medium">{{ $progress->points_earned }} pts</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-4">No skills in progress</p>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Completed Skills -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Completed Skills</h3>
                            <div class="bg-gray-50 rounded-lg p-4 max-h-80 overflow-y-auto">
                                @php
                                    $completed = $user->progress->where('status', 'completed');
                                @endphp
                                
                                @forelse($completed as $progress)
                                    <div class="mb-4 last:mb-0 p-3 bg-white rounded-lg border border-green-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $progress->skill->skill_name ?? 'Unknown Skill' }}</span>
                                                @if($progress->level)
                                                    <span class="ml-2 text-xs text-gray-500">({{ $progress->level->level_name }})</span>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Completed</span>
                                        </div>
                                        
                                        <!-- Stats -->
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>Videos: {{ $progress->videos_watched }}/{{ $progress->total_videos_in_skill }}</span>
                                            <span>Accuracy: {{ $progress->questions_answered > 0 ? round(($progress->correct_answers / $progress->questions_answered) * 100) : 0 }}%</span>
                                            <span class="text-purple-600 font-medium">{{ $progress->points_earned }} pts</span>
                                        </div>
                                        
                                        @if($progress->completed_at)
                                            <p class="text-xs text-gray-400 mt-2">Completed {{ $progress->completed_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-4">No skills completed yet</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overall Stats Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <p class="text-2xl font-bold">{{ $allLevels->count() }}</p>
                            <p class="text-xs text-blue-100">Total Levels</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <p class="text-2xl font-bold">{{ $user->progress->where('status', 'completed')->count() }}</p>
                            <p class="text-xs text-green-100">Completed Skills</p>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                            <p class="text-2xl font-bold">{{ $user->progress->where('status', 'in_progress')->count() }}</p>
                            <p class="text-xs text-yellow-100">In Progress</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                            <p class="text-2xl font-bold">{{ number_format($totalPoints) }}</p>
                            <p class="text-xs text-purple-100">Total Points</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat History Tab -->
            <div id="chat-tab" class="tab-content hidden p-6">
                <div class="space-y-4">
                    @forelse($recentSessions as $session)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900">
                                Session #{{ $session->session_id }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $session->started_at->format('M d, Y H:i A') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $session->messages_count }} messages • 
                            Last message {{ $session->last_msg_at ? $session->last_msg_at->diffForHumans() : 'No messages' }}
                        </p>
                        <a href="{{ route('admin.chatbot.sessions.show', $session) }}" 
                           class="text-sm text-blue-600 hover:text-blue-900">
                            View Conversation →
                        </a>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No chat sessions found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Activity Log Tab -->
            <div id="activity-tab" class="tab-content hidden p-6">
                <div class="space-y-4">
                    @forelse($activities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No activity recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to clicked button
    event.target.classList.add('active', 'text-blue-600', 'border-blue-600');
    event.target.classList.remove('text-gray-500');
}

// Toggle level details
function toggleLevelDetails(levelId) {
    const detailsDiv = document.getElementById(`level-${levelId}-details`);
    const arrow = document.getElementById(`arrow-${levelId}`);
    
    if (detailsDiv.classList.contains('hidden')) {
        detailsDiv.classList.remove('hidden');
        arrow.style.transform = 'rotate(180deg)';
    } else {
        detailsDiv.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
@endpush
@endsection