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

            <div class="flex-1 relative">
                <input type="text" 
                       name="search" 
                       placeholder="Search videos by title or description..." 
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Filter
            </button>
            
            @if(request('search') || request('skill_id'))
            <a href="{{ route('admin.videos.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Clear
            </a>
            @endif
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
                    <button onclick="openDeleteModal({{ $video->video_id }}, '{{ addslashes($video->title) }}', {{ $video->questions_count ?? 0 }})" 
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-lg">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No videos found</h3>
            <p class="text-gray-500">
                @if(request('search') || request('skill_id'))
                    No videos match your search criteria. Try adjusting your filters.
                @else
                    Get started by uploading your first video.
                @endif
            </p>
            @if(request('search') || request('skill_id'))
                <a href="{{ route('admin.videos.index') }}" class="mt-4 inline-block px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Clear Filters
                </a>
            @else
                <a href="{{ route('admin.videos.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Upload Video
                </a>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $videos->links() }}
    </div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>

        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg w-full max-w-md shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Delete Video</h3>
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
                                <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        Delete Video
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal animations */
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

    /* Line clamp for text */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@push('scripts')
<script>
// Delete Modal Functions
let deleteModal = document.getElementById('deleteModal');
let deleteForm = document.getElementById('deleteForm');
let deleteModalMessage = document.getElementById('deleteModalMessage');
let warningMessage = document.getElementById('warningMessage');
let warningText = document.getElementById('warningText');

function openDeleteModal(videoId, videoTitle, questionsCount) {
    // Set the form action
    deleteForm.action = `/admin/videos/${videoId}`;
    
    // Set the message
    let message = `Are you sure you want to delete video <span class="font-bold text-red-600">"${videoTitle}"</span>?`;
    deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;
    
    // Show warning if video has associated questions
    if (parseInt(questionsCount) > 0) {
        warningMessage.classList.remove('hidden');
        warningText.textContent = 
            `This video has ${questionsCount} associated question(s). Deleting it may affect related content.`;
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
</script>
@endpush
@endsection