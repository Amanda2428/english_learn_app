@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $video->title }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.videos.edit', $video->video_id) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Video
                </a>
                <a href="{{ route('admin.videos.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Video Player -->
        <div class="bg-black rounded-lg overflow-hidden mb-6">
            <video controls class="w-full">
                <source src="{{ Storage::url($video->video_file) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <!-- Video Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <!-- Meta Info -->
            <div class="flex flex-wrap gap-3 mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    {{ $video->skill->skill_name ?? 'No Skill' }}
                </span>
                @if($video->level_name != 'No Level')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        Level: {{ $video->level_name }}
                    </span>
                @endif
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                    Duration: {{ $video->duration }}
                </span>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                    Uploaded: {{ $video->created_at->format('M d, Y') }}
                </span>
            </div>

            <!-- Description -->
            @if($video->clean_description)
                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                    <p class="text-gray-700">{{ $video->clean_description }}</p>
                </div>
            @endif

            <!-- Video Info -->
            <div class="border-t mt-4 pt-4">
                <h3 class="text-lg font-semibold mb-2">Video Information</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">File Name</dt>
                        <dd class="text-gray-900">{{ basename($video->video_file) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">File Size</dt>
                        <dd class="text-gray-900">
                            @if(Storage::disk('public')->exists($video->video_file))
                                {{ round(Storage::disk('public')->size($video->video_file) / 1024 / 1024, 2) }} MB
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Last Updated</dt>
                        <dd class="text-gray-900">{{ $video->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Associated Questions</dt>
                        <dd class="text-gray-900">
                            <span class="font-bold">{{ $video->questions->count() }}</span>
                            @if($video->questions->count() > 0)
                                <a href="{{ route('admin.questions.index', ['video_id' => $video->video_id]) }}" 
                                   class="ml-2 text-blue-600 hover:text-blue-800 text-sm">
                                    View All
                                </a>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Associated Questions Section -->
            @if($video->questions->count() > 0)
            <div class="border-t mt-4 pt-4">
                <h3 class="text-lg font-semibold mb-3">Questions Using This Video</h3>
                <div class="space-y-3">
                    @foreach($video->questions->take(5) as $question)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ Str::limit($question->question_text, 100) }}</p>
                            <div class="flex gap-2 mt-1">
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                    {{ ucfirst($question->difficulty) }}
                                </span>
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded">
                                    {{ $question->points }} points
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('admin.questions.edit', $question->question_id) }}" 
                           class="ml-4 text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                    @endforeach
                    
                    @if($video->questions->count() > 5)
                    <div class="text-center pt-2">
                        <a href="{{ route('admin.questions.index', ['video_id' => $video->video_id]) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            View all {{ $video->questions->count() }} questions →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection