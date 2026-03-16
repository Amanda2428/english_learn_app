@extends('layouts.admin')

@section('title', $skill->skill_name)
@section('header', 'Editing the ' . $skill->skill_name)

@section('breadcrumbs')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.skills.index') }}" class="text-gray-500 hover:text-gray-700">Skills</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">View</span>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ $skill->skill_name }}</h2>
                    <p class="text-emerald-100 mt-2">{{ $skill->description ?? 'No description provided' }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-sm {{ $skill->status ? 'bg-green-500' : 'bg-gray-500' }}">
                        {{ $skill->status ? 'Active' : 'Inactive' }}
                    </span>
                    <a href="{{ route('admin.skills.edit', $skill->skill_id) }}"
                        class="bg-white text-emerald-700 px-4 py-2 rounded-xl hover:bg-emerald-50 transition">
                        Edit Skill
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Levels</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalLevels }}</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Videos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalVideos }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Questions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalQuestions }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Points</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $skill->questions->sum('points') }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Levels -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Assigned Levels</h3>
            </div>
            <div class="p-6">
                @if ($skill->levels->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($skill->levels as $level)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $level->level_name }}</h4>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">Order
                                        {{ $level->level_order }}</span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $level->description ?? 'No description' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No levels assigned to this skill.</p>
                @endif
            </div>
        </div>

        <!-- Videos List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Videos</h3>
                <a href="{{ route('admin.videos.create', ['skill_id' => $skill->skill_id]) }}"
                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                    Add Video
                </a>
            </div>
            <div class="p-6">
                @if ($skill->videos->count() > 0)
                    <div class="space-y-3">
                        @foreach ($skill->videos as $video)
                            <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 group">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $video->title }}</h4>
                                    <p class="text-sm text-gray-600">Duration: {{ $video->duration }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.videos.edit', $video->video_id) }}"
                                        class="text-gray-600 hover:text-emerald-600 transition-colors duration-200"
                                        title="Edit video">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No videos for this skill.</p>
                @endif
            </div>
        </div>

        <!-- Questions List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Questions</h3>
                <a href="{{ route('admin.questions.create', ['skill_id' => $skill->skill_id]) }}"
                    class="text-sm bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700">
                    Add Question
                </a>
            </div>
            <div class="p-6">
                @if ($skill->questions->count() > 0)
                    <div class="space-y-3">
                        @foreach ($skill->questions as $question)
                            <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 group">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ Str::limit($question->question_text, 100) }}
                                    </p>
                                    <div class="flex items-center mt-1 space-x-3 text-sm">
                                        <span
                                            class="px-2 py-0.5 rounded-full {{ $question->difficulty == 'easy' ? 'bg-green-100 text-green-700' : ($question->difficulty == 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                        <span class="text-gray-600">{{ $question->answers_count ?? 0 }} answers</span>
                                        <span class="text-gray-600">{{ $question->points }} points</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.questions.edit', $question->question_id) }}"
                                        class="text-gray-600 hover:text-emerald-600 transition-colors duration-200"
                                        title="Edit question">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No questions for this skill.</p>
                @endif
            </div>
        </div>


    </div>
@endsection
