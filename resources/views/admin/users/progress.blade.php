@extends('layouts.admin')

@section('title', $user->name . ' - Learning Progress')
@section('header', 'Learning Progress')

@section('breadcrumbs')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">Users</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900">{{ $user->name }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-900">Progress</span>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- User Profile Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="relative px-6 py-8 sm:px-8 lg:px-10">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="absolute left-0 top-0 h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 0 L100 100 M100 0 L0 100" stroke="white" stroke-width="1"
                            vector-effect="non-scaling-stroke" />
                    </svg>
                </div>

                <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <!-- User Avatar -->
                        @if ($user->profile)
                            <img src="{{ Storage::url($user->profile) }}" alt="Profile Picture"
                                class="h-16 w-16 rounded-full object-cover ring-4 ring-white/30">
                        @else
                            <div
                                class="h-16 w-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center ring-4 ring-white/30">
                                <span class="text-2xl font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <!-- User Info -->
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $user->name }}</h1>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-blue-100">{{ $user->email }}</span>
                                <span class="text-blue-300">•</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                                    {{ $user->role == 1 ? 'Admin' : 'Student' }}
                                </span>
                                @if ($user->level)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400/20 text-yellow-100">
                                        Level: {{ $user->level->level_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.show', $user) }}"
                            class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur hover:bg-white/30 text-white rounded-xl transition-all duration-200 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Profile
                        </a>
                        <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur hover:bg-white/30 text-white rounded-xl transition-all duration-200 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Tabs -->
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-blue-500/30 border-t border-blue-500/30">
                <div class="px-6 py-4">
                    <p class="text-blue-200 text-xs uppercase tracking-wider">Joined</p>
                    <p class="text-white font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-blue-200 text-xs uppercase tracking-wider">Last Active</p>
                    <p class="text-white font-semibold">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-blue-200 text-xs uppercase tracking-wider">Total Activities</p>
                    <p class="text-white font-semibold">{{ $stats['videos_watched'] + $stats['questions_answered'] }}</p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-blue-200 text-xs uppercase tracking-wider">Time Spent</p>
                    <p class="text-white font-semibold">{{ floor($stats['total_time'] / 60) }}h
                        {{ $stats['total_time'] % 60 }}m</p>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Completed Skills Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['completed_skills'] }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Completed Skills</h3>
                <p class="mt-2 text-xs text-gray-400">Out of {{ $stats['total_skills'] ?? 0 }} total skills</p>
            </div>

            <!-- In Progress Skills Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['in_progress_skills'] }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">In Progress</h3>
                <p class="mt-2 text-xs text-gray-400">Skills being learned</p>
            </div>

            <!-- Videos Watched Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['videos_watched'] }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Videos Watched</h3>
                <p class="mt-2 text-xs text-gray-400">{{ $stats['total_videos'] ?? 0 }} total videos available</p>
            </div>

            <!-- Questions Answered Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['questions_answered'] }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Questions Answered</h3>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-gray-400">{{ $stats['correct_answers'] ?? 0 }} correct</span>
                    <span class="text-green-600 font-medium">{{ $stats['accuracy_rate'] }}% accuracy</span>
                </div>
            </div>
        </div>

        <!-- Points and Progress Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Total Points Card -->
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total Points</h3>
                    <svg class="w-8 h-8 text-yellow-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-4xl font-bold mb-2">{{ number_format($stats['total_points']) }}</p>
                <p class="text-yellow-100 text-sm">Lifetime points earned</p>
                <div class="mt-4 pt-4 border-t border-yellow-500/30">
                    <div class="flex justify-between text-sm">
                        <span>Points per hour</span>
                        <span
                            class="font-semibold">{{ $stats['total_time'] > 0 ? round($stats['total_points'] / max($stats['total_time'] / 60, 1)) : 0 }}
                            pts</span>
                    </div>
                </div>
            </div>

            <!-- Time Spent Card -->
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Time Invested</h3>
                    <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-4xl font-bold mb-2">{{ floor($stats['total_time'] / 60) }}h
                    {{ $stats['total_time'] % 60 }}m</p>
                <p class="text-blue-100 text-sm">Total learning time</p>
                <div class="mt-4 pt-4 border-t border-blue-500/30">
                    <div class="flex justify-between text-sm">
                        <span>Daily average</span>
                        <span
                            class="font-semibold">{{ round($stats['total_time'] / max($user->created_at->diffInDays(now()), 1), 1) }}
                            min</span>
                    </div>
                </div>
            </div>

            <!-- Accuracy Rate Card -->
            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Accuracy Rate</h3>
                    <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-4xl font-bold mb-2">{{ $stats['accuracy_rate'] }}%</p>
                <p class="text-green-100 text-sm">Questions answered correctly</p>
                <div class="mt-4 pt-4 border-t border-green-500/30">
                    <div class="flex justify-between text-sm">
                        <span>{{ $stats['correct_answers'] ?? 0 }} correct</span>
                        <span>{{ $stats['questions_answered'] - ($stats['correct_answers'] ?? 0) }} incorrect</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Progress Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Detailed Skill Progress</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Skill</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Level</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Progress</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Videos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Questions</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Points</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Started</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($progress as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->skill->skill_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->level)
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $item->level->level_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($item->status == 'completed')
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @elseif($item->status == 'in_progress')
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            In Progress
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Not Started
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="text-sm font-medium text-gray-700 mr-2">{{ $item->completion_percentage }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                style="width: {{ $item->completion_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                    {{ $item->videos_watched }}/{{ $item->total_videos_in_skill }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                    {{ $item->questions_answered }}/{{ $item->total_questions_in_skill }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $item->points_earned }} pts
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    {{ $item->started_at ? $item->started_at->format('M d, Y') : 'Not started' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No progress data</h3>
                                    <p class="text-gray-600">This user hasn't started learning yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (method_exists($progress, 'links'))
                <div class="px-6 py-4 bg-gray-50 border-t">
                    {{ $progress->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Activity Timeline -->
        @if ($recentProgress->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                    <span class="text-sm text-gray-500">{{ $recentProgress->count() }} activities</span>
                </div>
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach ($recentProgress as $index => $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if (!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span
                                                class="h-8 w-8 rounded-full bg-{{ ['blue', 'green', 'purple', 'yellow'][$index % 4] }}-100 flex items-center justify-center ring-8 ring-white">
                                                @if ($activity->skill)
                                                    <span
                                                        class="h-4 w-4 text-{{ ['blue', 'green', 'purple', 'yellow'][$index % 4] }}-600 font-bold text-xs">
                                                        {{ substr($activity->skill->skill_name ?? 'S', 0, 1) }}
                                                    </span>
                                                @else
                                                    <svg class="h-4 w-4 text-{{ ['blue', 'green', 'purple', 'yellow'][$index % 4] }}-600"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <span
                                                        class="font-medium text-gray-900">{{ $activity->skill->skill_name ?? 'Unknown Skill' }}</span>
                                                    @if ($activity->status == 'completed')
                                                        <span class="text-green-600">completed</span>
                                                    @else
                                                        <span class="text-blue-600">progress updated</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Videos:
                                                    {{ $activity->videos_watched }}/{{ $activity->total_videos_in_skill }}
                                                    •
                                                    Questions:
                                                    {{ $activity->questions_answered }}/{{ $activity->total_questions_in_skill }}
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap">
                                                <span
                                                    class="text-gray-500">{{ $activity->updated_at->diffForHumans() }}</span>
                                                <p class="text-xs text-purple-600 font-medium mt-1">
                                                    +{{ $activity->points_earned }} pts</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .progress-bar {
                transition: width 1s ease-in-out;
            }
        </style>
    @endpush
@endsection
