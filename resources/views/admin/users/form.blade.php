@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Create User')
@section('header', isset($user) ? 'Edit User' : 'Create User')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.users.index') }}" class="hover:text-gray-900">Users</a>
        <span>/</span>
        <span class="text-gray-700">{{ isset($user) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Intro -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h2>
        <p class="text-blue-100 mt-2">
            {{ isset($user) ? 'Update user account details, role, and level.' : 'Add a new user to the platform and assign initial settings.' }}
        </p>
    </div>

    <!-- Validation / Flash -->
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200">
            <p class="font-semibold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" 
              method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $user->name ?? '') }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @else border-gray-300 @enderror"
                        placeholder="Enter full name"
                        required
                        autofocus
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $user->email ?? '') }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @else border-gray-300 @enderror"
                        placeholder="user@example.com"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password @if(!isset($user))<span class="text-red-500">*</span>@endif
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @else border-gray-300 @enderror"
                        placeholder="{{ isset($user) ? 'Leave blank to keep current password' : 'Enter password' }}"
                        {{ !isset($user) ? 'required' : '' }}
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-gray-300"
                        placeholder="Confirm password"
                    >
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                        User Role <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @else border-gray-300 @enderror"
                        required
                    >
                        <option value="">Select role</option>
                        <option value="0" {{ old('role', isset($user) ? $user->role : '') == 0 ? 'selected' : '' }}>Regular User</option>
                        <option value="1" {{ old('role', isset($user) ? $user->role : '') == 1 ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Level -->
                <div>
                    <label for="level_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Current Level
                    </label>
                    <select
                        name="level_id"
                        id="level_id"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level_id') border-red-500 @else border-gray-300 @enderror"
                    >
                        <option value="">Select level (optional)</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->level_id }}" 
                                {{ old('level_id', isset($user) ? $user->level_id : '') == $level->level_id ? 'selected' : '' }}>
                                {{ $level->level_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('level_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tips -->
                <div class="md:col-span-2 bg-gray-50 rounded-xl p-4 border">
                    <p class="text-sm font-semibold text-gray-700 mb-2">User Management Tips</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Leave password blank when editing to keep current password</li>
                        <li>• Assign admin role carefully - they get full access</li>
                        <li>• Level assignment is optional, users can progress naturally</li>
                    </ul>
                </div>
            </div>

            @if(isset($user))
                <!-- User Statistics Section -->
                <div class="mt-6 p-4 bg-purple-50 rounded-xl border border-purple-100">
                    <h3 class="text-lg font-semibold text-purple-800 mb-3">User Statistics</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-purple-600 font-semibold uppercase tracking-wider">Total Points</p>
                            <p class="text-2xl font-bold text-purple-800">{{ number_format($totalPoints ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">earned from activities</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Completed Skills</p>
                            <p class="text-2xl font-bold text-green-800">{{ $completedSkills ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">skills fully mastered</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-yellow-600 font-semibold uppercase tracking-wider">In Progress</p>
                            <p class="text-2xl font-bold text-yellow-800">{{ $inProgressSkills ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">skills being learned</p>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-blue-600 font-semibold">Created</p>
                            <p class="text-sm text-blue-800">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-semibold">Last Updated</p>
                            <p class="text-sm text-blue-800">{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ isset($user) ? 'Update User' : 'Create User' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection