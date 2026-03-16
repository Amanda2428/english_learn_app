@extends('layouts.admin')

@section('title', 'Videos')
@section('header', 'Video Management')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-700">Videos</span>
    </div>
@endsection


@section('content')
<style>
.color-blue{
    color: ;
}

</style>

         <!-- Top Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 color-blue">
            <div>
                <h2 class="text-2xl font-bold">Videos Management</h2>
                <p class="text-emerald-100 mt-2">
                    Create and manage videos, organize by skills, and track related learning content.
                </p>
            </div>
            <a href="{{ route('admin.videos.create') }}"
               class="inline-flex items-center bg-white text-blue-700 px-4 py-2 rounded-xl font-medium hover:bg-blue-50 transition shadow-sm">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
            </svg>
              Upload New Video
            </a>
        </div>
    </div>

        

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.videos.index') }}" class="flex gap-4">
            <select name="skill_id" class="px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">All Skills</option>
                @foreach($skills as $skill)
                    <option value="{{ $skill->skill_id }}" {{ request('skill_id') == $skill->skill_id ? 'selected' : '' }}>
                        {{ $skill->skill_name }}
                    </option>
                @endforeach
            </select>

            <input type="text" 
                   name="search" 
                   placeholder="Search videos..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg">

            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Videos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($videos as $video)
        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Video Thumbnail -->
            <div class="relative aspect-video bg-gray-900">
                <video class="w-full h-full object-cover" preload="metadata">
                    <source src="{{ Storage::url($video->video_file) }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 hover:opacity-100 transition-opacity">
                    <a href="{{ route('admin.videos.show', $video->video_id) }}" class="p-2 bg-white rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </a>
                </div>
                <span class="absolute bottom-2 right-2 px-2 py-1 bg-black bg-opacity-75 text-white text-xs rounded">
                    {{ $video->duration }}
                </span>
            </div>

            <!-- Video Info -->
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-1">{{ $video->title }}</h3>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                        {{ $video->skill->skill_name ?? 'No Skill' }}
                    </span>
                    @if($video->level_name != 'No Level')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">
                            {{ $video->level_name }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                    {{ $video->clean_description }}
                </p>
                
                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <a href="{{ route('admin.videos.edit', $video->video_id) }}" 
                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('admin.videos.show', $video->video_id) }}" 
                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('admin.videos.destroy', $video->video_id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this video?')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-lg">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No videos found</h3>
            <p class="text-gray-500">Get started by uploading your first video.</p>
            <a href="{{ route('admin.videos.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Upload Video
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $videos->links() }}
    </div>

@endsection