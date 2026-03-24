@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', $welcomeTitle)

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
    </div>
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">{{ $welcomeTitle }}</h2>
                <p class="text-blue-100 mt-2">
                    Manage your users, learning content, chatbot activity, and system performance from one place.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.users.index') }}"
                    class="bg-white text-blue-700 px-4 py-2 rounded-lg font-medium hover:bg-blue-50">
                    Manage Users
                </a>
                <a href="{{ route('admin.levels.index') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-400">
                    Manage Levels
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Users</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers }}</p>
            <p class="text-xs text-green-500 mt-2">+{{ $newUsersToday }} today</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Active Users (7d)</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $activeChatUsers }}</p>
            <p class="text-xs text-blue-500 mt-2">{{ $activeSessionsThisWeek }} active sessions</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Content</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalContent }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $totalVideos }} Videos · {{ $totalQuestions }} Questions</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Average Progress</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $averageProgress }}%</p>
            <p class="text-xs text-purple-500 mt-2">{{ $completedContent }} completed items</p>
        </div>
    </div>

    <!-- Charts + Profile -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">User Registrations (Last 7 Days)</h3>
                <span class="text-sm text-gray-500">Total: {{ $totalRegistrations }}</span>
            </div>
            <div class="h-72">
                <canvas id="userChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Management</h3>

            <div class="flex items-center space-x-4 mb-4">
                @if ($profileData['profile'] ?? false)
                    <img src="{{ Storage::url($profileData['profile']) }}" alt="Profile Picture"
                        class="w-14 h-14 rounded-full object-cover">
                @else
                    <div
                        class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl">
                        {{ strtoupper(substr($profileData['name'], 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="font-semibold text-gray-800">{{ $profileData['name'] }}</p>
                    <p class="text-sm text-gray-500">{{ $profileData['email'] }}</p>
                    <p class="text-xs text-blue-600">{{ $profileData['role'] }}</p>
                </div>
            </div>

            <div class="space-y-2 text-sm text-gray-600">
                <p><span class="font-medium">Joined:</span> {{ $profileData['joined'] }}</p>
                <p><span class="font-medium">Bio:</span> {{ $profileData['bio'] }}</p>
            </div>

            <div class="mt-4 grid gap-2">
                <a href="{{ route('profile.edit') }}"
                    class="text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Content + Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Content per Level</h3>
                    <p class="text-sm text-gray-500">
                        Videos need a skill or level relationship to appear here.
                    </p>
                </div>
                <a href="{{ route('admin.levels.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Manage Levels
                    →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Level</th>
                            <th class="text-center py-2">Skills</th>
                            <th class="text-center py-2">Videos</th>
                            <th class="text-center py-2">Questions</th>
                            <th class="text-center py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contentPerLevel as $level)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 font-medium">{{ $level->level_name }}</td>
                                <td class="text-center">{{ $level->skills_count }}</td>
                                <td class="text-center">{{ $level->videos_count }}</td>
                                <td class="text-center">{{ $level->questions_count }}</td>
                                <td class="text-center">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        {{ $level->total_content }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No content available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Storage Usage</span>
                        <span class="font-medium">{{ $storageUsed }} GB / {{ $storageFree + $storageUsed }} GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 rounded-full h-2" style="width: {{ $storagePercentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $storagePercentage }}% used</p>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Admins</span>
                        <span class="font-semibold">{{ $adminCount }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Regular Users</span>
                        <span class="font-semibold">{{ $regularUserCount }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Total Points</span>
                        <span class="font-semibold">{{ $totalPointsEarned }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Avg Video Duration</span>
                        <span class="font-semibold">{{ $avgDurationFormatted }}</span>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-medium mb-2">Top Bot Rules</h4>
                    @forelse($topRules as $rule)
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 truncate max-w-[150px]">{{ $rule->keyword }}</span>
                            <span class="font-semibold">{{ $rule->messages_count }} uses</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No rules used yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All
                    →</a>
            </div>
            <div class="space-y-4">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if ($user->profile)
                                <img src="{{ Storage::url($user->profile) }}" alt="Profile Picture"
                                    class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                            @if ($user->isAdmin())
                                <span class="block text-xs text-red-600">Admin</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent users</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Chat Sessions</h3>
            </div>
            <div class="space-y-4">
                @forelse($recentChatSessions as $session)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $session->user->name ?? 'Unknown User' }}</p>
                            <p class="text-sm text-gray-500">{{ $session->messages_count }} messages</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-xs text-gray-500">{{ $session->last_msg_at ? $session->last_msg_at->diffForHumans() : 'No messages' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent chat sessions</p>
                @endforelse
            </div>
        </div>
    </div>
    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-5">Quick Actions</h3>

        <div class="space-y-3">

            <a href="{{ route('admin.users.create') }}"
                class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 transition rounded-xl group">

                <div class="bg-blue-500 text-white p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v6M15 12h6M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2v13a2 2 0 002 2z" />
                    </svg>
                </div>

                <span class="text-sm font-semibold text-blue-700">
                    Add New User
                </span>
            </a>


            <a href="{{ route('admin.levels.index') }}"
                class="flex items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 transition rounded-xl group">

                <div class="bg-indigo-500 text-white p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                </div>

                <span class="text-sm font-semibold text-indigo-700">
                    Manage Levels
                </span>
            </a>


            <a href="{{ route('admin.chatbot.rules.index') }}"
                class="flex items-center gap-3 p-4 bg-orange-50 hover:bg-orange-100 transition rounded-xl group">

                <div class="bg-orange-500 text-white p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M4 6h16M4 20h16" />
                    </svg>
                </div>

                <span class="text-sm font-semibold text-orange-700">
                    Manage Chatbot Rules
                </span>
            </a>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userCtx = document.getElementById('userChart');
            if (userCtx) {
                new Chart(userCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($userRegistrations->pluck('day')->values()) !!},
                        datasets: [{
                            label: 'New Users',
                            data: {!! json_encode($userRegistrations->pluck('count')->values()) !!},
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
