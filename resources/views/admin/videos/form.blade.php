@extends('layouts.admin')

@section('title', isset($video) ? 'Edit Video' : 'Create Video')
@section('header', isset($video) ? 'Edit Video' : 'Create Video')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.videos.index') }}" class="hover:text-gray-900">Videos</a>
        <span>/</span>
        <span class="text-gray-700">{{ isset($video) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Intro -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold">{{ isset($video) ? 'Edit Video' : 'Upload New Video' }}</h2>
        <p class="text-blue-100 mt-2">
            {{ isset($video) ? 'Update the video details and information.' : 'Upload a new video and set its details.' }}
        </p>
    </div>

    <!-- Validation / Flash Messages -->
    @if(session('success'))
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 border border-green-200 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200">
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ isset($video) ? route('admin.videos.update', $video->video_id) : route('admin.videos.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              id="videoForm">
            @csrf
            @if(isset($video))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Video Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $video->title ?? '') }}"
                           required
                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @else border-gray-300 @enderror"
                           placeholder="Enter video title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Skill Selection -->
                <div>
                    <label for="skill_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Associated Skill
                    </label>
                    <select name="skill_id" 
                            id="skill_id" 
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('skill_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Select Skill (Optional)</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->skill_id }}" 
                                {{ (old('skill_id', $video->skill_id ?? '')) == $skill->skill_id ? 'selected' : '' }}>
                                {{ $skill->skill_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('skill_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Level Selection -->
                <div>
                    <label for="level_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Associated Level
                    </label>
                    <select name="level_id" 
                            id="level_id" 
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level_id') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Select Level (Optional)</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->level_id }}" 
                                {{ (old('level_id', $video->level_id ?? '')) == $level->level_id ? 'selected' : '' }}>
                                {{ $level->level_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('level_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Level will be stored in the description as a marker</p>
                </div>

                <!-- Video File Upload -->
                <div class="md:col-span-2">
                    <label for="video_file" class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ isset($video) ? 'Update Video File (Optional)' : 'Video File *' }}
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-500 transition-colors">
                        <input type="file" 
                               name="video_file" 
                               id="video_file" 
                               accept="video/*"
                               {{ isset($video) ? '' : 'required' }}
                               class="hidden"
                               onchange="handleVideoSelect(this)">
                        
                        <button type="button" 
                                onclick="document.getElementById('video_file').click()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Choose Video File
                        </button>
                        
                        <p class="text-sm text-gray-500 mt-2" id="file-name">
                            @if(isset($video) && $video->video_file)
                                Current file: {{ basename($video->video_file) }}
                            @else
                                No file chosen
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Supported formats: MP4, MOV, AVI, WMV, FLV, MKV (Max 100MB)</p>
                    </div>
                    @error('video_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration (Auto-detected) -->
                <div>
                    <label for="duration" class="block text-sm font-semibold text-gray-700 mb-2">
                        Duration <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="duration" 
                               id="duration" 
                               value="{{ old('duration', $video->duration ?? '00:00:00') }}"
                               required
                               readonly
                               class="w-full px-4 py-3 border rounded-xl bg-gray-50 text-gray-500 cursor-not-allowed @error('duration') border-red-500 @else border-gray-300 @enderror"
                               placeholder="Will be auto-detected">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="duration-status">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Duration will be automatically detected when you select a video file
                        </span>
                    </p>
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Info -->
                <div class="bg-gray-50 rounded-xl p-4 border">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Video Tips</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• MP4 format is recommended for best compatibility</li>
                        <li>• Keep video titles clear and descriptive</li>
                        <li>• Add relevant skills and levels for better organization</li>
                    </ul>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @else border-gray-300 @enderror"
                              placeholder="Enter video description...">{{ old('description', $video->clean_description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.videos.index') }}"
                   class="px-5 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                    {{ isset($video) ? 'Update Video' : 'Upload Video' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleVideoSelect(input) {
    const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
    document.getElementById('file-name').textContent = 'Selected: ' + fileName;
    document.getElementById('duration-status').innerHTML = `
        <span class="inline-flex items-center text-blue-600">
            <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Detecting video duration...
        </span>
    `;
    
    if (input.files && input.files[0]) {
        const video = document.createElement('video');
        video.preload = 'metadata';
        
        video.onloadedmetadata = function() {
            window.URL.revokeObjectURL(video.src);
            const duration = video.duration;
            const hours = Math.floor(duration / 3600);
            const minutes = Math.floor((duration % 3600) / 60);
            const seconds = Math.floor(duration % 60);
            
            const durationStr = [
                hours.toString().padStart(2, '0'),
                minutes.toString().padStart(2, '0'),
                seconds.toString().padStart(2, '0')
            ].join(':');
            
            document.getElementById('duration').value = durationStr;
            document.getElementById('duration-status').innerHTML = `
                <span class="inline-flex items-center text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Duration detected: ${durationStr}
                </span>
            `;
        };
        
        video.onerror = function() {
            document.getElementById('duration-status').innerHTML = `
                <span class="inline-flex items-center text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Could not detect duration. Please enter manually.
                </span>
            `;
            document.getElementById('duration').readOnly = false;
            document.getElementById('duration').classList.remove('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
        };
        
        video.src = URL.createObjectURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if(isset($video) && $video->video_file)
        document.getElementById('duration-status').innerHTML = `
            <span class="inline-flex items-center text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Current duration: {{ $video->duration }}
            </span>
        `;
    @endif
});
</script>
@endsection