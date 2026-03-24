@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Management</h1>
                <p class="text-sm text-gray-600 mt-1">Manage all users, their roles, and progress</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.users.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add New User
                </a>
                <button onclick="exportUsers()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeUsers }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Admins</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $adminCount }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">New This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $newThisMonth }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-6">
                <!-- Search Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🔍 Search Users
                    </label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Search by user's full name or email address</p>
                </div>

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Role Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            👤 User Role
                        </label>
                        <select name="role"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Users</option>
                            <option value="0" {{ request('role') === '0' ? 'selected' : '' }}>📘 Regular Users</option>
                            <option value="1" {{ request('role') === '1' ? 'selected' : '' }}>👑 Administrators
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Filter by user permission level</p>
                    </div>

                    <!-- Level Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📊 Learning Level
                        </label>
                        <select name="level_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Levels</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->level_id }}"
                                    {{ request('level_id') == $level->level_id ? 'selected' : '' }}>
                                    {{ $level->level_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Filter by user's current skill level</p>
                    </div>

                    <!-- Date From Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📅 Registered From
                        </label>
                        <div class="relative">
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5 pointer-events-none" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Show users registered on or after this date</p>
                    </div>

                    <!-- Date To Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📅 Registered Until
                        </label>
                        <div class="relative">
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5 pointer-events-none" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Show users registered on or before this date</p>
                    </div>
                </div>

                <!-- Sort Options Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🔄 Sort By
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <select name="sort"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>🕒 Latest First
                                (Newest users)</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>🕒 Oldest First
                                (Earliest users)</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>📝 Name A-Z
                                (Alphabetical)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>📝 Name Z-A
                                (Reverse alphabetical)</option>
                            <option value="points_high" {{ request('sort') == 'points_high' ? 'selected' : '' }}>⭐ Highest
                                Points (Top performers)</option>
                            <option value="points_low" {{ request('sort') == 'points_low' ? 'selected' : '' }}>⭐ Lowest
                                Points (Needs improvement)</option>
                        </select>

                        <!-- Active Filters Indicator -->
                        <div class="flex items-center space-x-2">
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                🔍 Search
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Clear All
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @php
                    $activeFilters = [];
                    if (request('search')) {
                        $activeFilters[] = 'Search: ' . request('search');
                    }
                    if (request('role') === '0') {
                        $activeFilters[] = 'Role: Regular Users';
                    }
                    if (request('role') === '1') {
                        $activeFilters[] = 'Role: Administrators';
                    }
                    if (request('level_id')) {
                        $activeFilters[] = 'Level: ' . ($levels->find(request('level_id'))?->level_name ?? 'Unknown');
                    }
                    if (request('from_date')) {
                        $activeFilters[] = 'From: ' . date('M d, Y', strtotime(request('from_date')));
                    }
                    if (request('to_date')) {
                        $activeFilters[] = 'Until: ' . date('M d, Y', strtotime(request('to_date')));
                    }
                    if (request('sort') && request('sort') != 'latest') {
                        $sortLabels = [
                            'oldest' => 'Oldest First',
                            'name_asc' => 'Name A-Z',
                            'name_desc' => 'Name Z-A',
                            'points_high' => 'Highest Points',
                            'points_low' => 'Lowest Points',
                        ];
                        $activeFilters[] = 'Sort: ' . ($sortLabels[request('sort')] ?? request('sort'));
                    }
                @endphp

                @if (count($activeFilters) > 0)
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                    </path>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($activeFilters as $filter)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white text-blue-700 border border-blue-300">
                                        {{ $filter }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Points</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last
                                Active</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                        class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if ($user->profile)
                                                <img src="{{ Storage::url($user->profile) }}" alt="Profile Picture"
                                                    class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span
                                                        class="text-gray-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->isAdmin())
                                        <span
                                            class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Admin</span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">User</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->level)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ $user->level->level_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ number_format($user->total_points) }}</span>
                                        <svg class="w-4 h-4 text-yellow-500 ml-1" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->last_activity && \Carbon\Carbon::parse($user->last_activity)->diffInHours(now()) < 24)
                                        <span class="flex items-center">
                                            <span class="h-2 w-2 bg-green-400 rounded-full mr-2"></span>
                                            <span class="text-xs text-green-600">Online</span>
                                        </span>
                                    @else
                                        <span class="flex items-center">
                                            <span class="h-2 w-2 bg-gray-400 rounded-full mr-2"></span>
                                            <span class="text-xs text-gray-600">Offline</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($user->last_activity)
                                        {{ \Carbon\Carbon::parse($user->last_activity)->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="text-blue-600 hover:text-blue-900 p-1" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="text-green-600 hover:text-green-900 p-1" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.progress', $user) }}"
                                            class="text-purple-600 hover:text-purple-900 p-1" title="Progress">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </a>
                                        @if (auth()->id() !== $user->id)
                                            <button
                                                onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->progress->count() }})"
                                                class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                    <p class="text-gray-600 mb-4">Try adjusting your filters or create a new user</p>
                                    <a href="{{ route('admin.users.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                        Add New User
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <select id="bulkAction"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Bulk Actions</option>
                        <option value="delete">Delete Selected</option>
                        <option value="export">Export Selected</option>
                        <option value="make_admin">Make Admin</option>
                        <option value="make_user">Make Regular User</option>
                    </select>
                    <button onclick="executeBulkAction()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Apply
                    </button>
                    <span id="selectedCount" class="text-sm text-gray-600">0 selected</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal (Styled like questions blade) -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg w-full max-w-md shadow-2xl transform transition-all">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Delete User</h3>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div id="deleteModalMessage" class="text-center">
                        <!-- Dynamic message will be inserted here -->
                    </div>

                    <div id="warningMessage" class="mt-3 hidden">
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-amber-700" id="warningText"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Cancel
                    </button>

                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        #deleteModal {
            transition: opacity 0.3s ease;
        }

        #deleteModal .bg-white {
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @push('scripts')
        <script>
            // Select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                updateSelectedCount();
            });

            // Update selected count
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.user-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = selected + ' selected';
                document.getElementById('selectAll').checked =
                    selected === document.querySelectorAll('.user-checkbox').length;
            }

            // Delete Modal Functions
            let deleteModal = document.getElementById('deleteModal');
            let deleteForm = document.getElementById('deleteForm');
            let deleteModalMessage = document.getElementById('deleteModalMessage');
            let warningMessage = document.getElementById('warningMessage');
            let warningText = document.getElementById('warningText');

            function openDeleteModal(userId, userName, progressCount) {
                // Set the form action
                deleteForm.action = `/admin/users/${userId}`;

                // Set the message
                let message = `Are you sure you want to delete user <span class="font-bold text-red-600">"${userName}"</span>?`;
                deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;

                // Show warning if user has progress data
                if (parseInt(progressCount) > 0) {
                    warningMessage.classList.remove('hidden');
                    warningText.textContent =
                        `This user has ${progressCount} progress record(s). Deleting will also delete all associated progress data.`;
                } else {
                    warningMessage.classList.add('hidden');
                }

                // Show the modal
                deleteModal.classList.remove('hidden');

                // Prevent body scrolling
                document.body.style.overflow = 'hidden';
            }

            function closeDeleteModal() {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            });

            // Bulk actions
            function executeBulkAction() {
                const action = document.getElementById('bulkAction').value;
                const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select users first.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                switch (action) {
                    case 'delete':
                        showBulkDeleteModal(selectedIds);
                        break;
                    case 'export':
                        exportSelected(selectedIds);
                        break;
                    case 'make_admin':
                        updateUserRole(selectedIds, 1);
                        break;
                    case 'make_user':
                        updateUserRole(selectedIds, 0);
                        break;
                    default:
                        Swal.fire({
                            icon: 'info',
                            title: 'No Action Selected',
                            text: 'Please select an action.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                }
            }

            function showBulkDeleteModal(ids) {
                Swal.fire({
                    title: 'Delete Users?',
                    text: `Are you sure you want to delete ${ids.length} selected user(s)? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkDelete(ids);
                    }
                });
            }

            function bulkDelete(ids) {
                fetch('{{ route('admin.users.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            user_ids: ids
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Failed to delete users.'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while deleting users.'
                        });
                    });
            }

            function updateUserRole(ids, role) {
                const roleText = role === 1 ? 'Admin' : 'Regular User';

                Swal.fire({
                    title: `Make ${roleText}?`,
                    text: `Are you sure you want to make ${ids.length} selected user(s) ${roleText}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, update them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('admin.users.bulk-update-role') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    user_ids: ids,
                                    role: role
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: data.message || 'Failed to update user roles.'
                                    });
                                }
                            });
                    }
                });
            }

            function exportSelected(ids) {
                const url = '{{ route('admin.users.export') }}?' + ids.map(id => 'user_ids[]=' + id).join('&');
                window.location.href = url;
            }

            function exportUsers() {
                window.location.href = '{{ route('admin.users.export') }}';
            }

            // Flash messages with SweetAlert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    showConfirmButton: true,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        </script>

        <!-- Add SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
@endsection
