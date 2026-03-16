@extends('layouts.admin')

@section('title', isset($skill) ? 'Edit Skill' : 'Create Skill')
@section('header', isset($skill) ? 'Edit Skill' : 'Create Skill')

@section('breadcrumbs')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.skills.index') }}" class="text-gray-500 hover:text-gray-700">Skills</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">{{ isset($skill) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Intro -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold">{{ isset($skill) ? 'Edit Skill' : 'Create New Skill' }}</h2>
        <p class="text-emerald-100 mt-2">
            {{ isset($skill) ? 'Update the skill name, description, and level assignments.' : 'Add a new skill to organize learning content across different levels.' }}
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
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <form action="{{ isset($skill) ? route('admin.skills.update', $skill->skill_id) : route('admin.skills.store') }}" method="POST">
            @csrf
            @if(isset($skill))
                @method('PUT')
            @endif

            <!-- Basic Information Section -->
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Skill Name -->
                    <div class="md:col-span-2">
                        <label for="skill_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Skill Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="skill_name"
                            id="skill_name"
                            value="{{ old('skill_name', $skill->skill_name ?? '') }}"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('skill_name') border-red-500 @else border-gray-300 @enderror"
                            placeholder="e.g., Reading Comprehension, Vocabulary, Grammar"
                            required
                            autofocus
                        >
                        @error('skill_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Toggle -->
                    <div class="md:col-span-2">
                        <label for="status" class="flex items-center space-x-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="status"
                                id="status"
                                value="1"
                                {{ old('status', isset($skill) ? $skill->status : true) ? 'checked' : '' }}
                                class="h-5 w-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                            >
                            <span class="text-sm font-semibold text-gray-700">Active Skill</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 ml-8">Inactive skills won't be visible to users in the learning interface</p>
                    </div>

                    <!-- Tips Box -->
                    <div class="md:col-span-2 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Skill Tips</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Use clear, specific skill names that reflect the learning objective</li>
                                    <li>Add a detailed description to help content creators understand the skill's scope</li>
                                    <li>Assign skills to multiple levels if they're applicable across different proficiency levels</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="p-6 border-t border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Description</h3>
                
                <div>
                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @else border-gray-300 @enderror"
                        placeholder="Describe what this skill covers, what learners will achieve, and any important details..."
                    >{{ old('description', $skill->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        A good description helps content creators understand when and how to use this skill.
                    </p>
                </div>
            </div>

            <!-- Level Assignment Section -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Assign to Levels</h3>
                    @if(isset($skill))
                        <span class="text-sm bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            {{ $skill->levels->count() }} level(s) assigned
                        </span>
                    @endif
                </div>

                @if($levels->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($levels as $level)
                            @php
                                $isAssigned = isset($skill) && $skill->levels->contains('level_id', $level->level_id);
                                $oldLevels = old('levels', []);
                                $isChecked = $isAssigned || in_array($level->level_id, $oldLevels);
                            @endphp
                            <label class="relative flex items-start space-x-3 p-4 border rounded-xl cursor-pointer transition-all {{ $isChecked ? 'bg-emerald-50 border-emerald-300 ring-1 ring-emerald-200' : 'hover:bg-gray-50 border-gray-200' }}">
                                <input
                                    type="checkbox"
                                    name="levels[]"
                                    value="{{ $level->level_id }}"
                                    {{ $isChecked ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                >
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ $level->level_name }}</p>
                                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">
                                            Order {{ $level->level_order }}
                                        </span>
                                    </div>
                                    @if($level->description)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $level->description }}</p>
                                    @endif
                                </div>
                                
                                @if($isChecked)
                                    <div class="absolute top-2 right-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    @if(isset($skill) && $skill->levels->count() === 0)
                        <p class="text-sm text-amber-600 bg-amber-50 p-4 rounded-xl mt-4">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            This skill is not assigned to any levels yet. Select levels above to make it available.
                        </p>
                    @endif
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-gray-500 mb-3">No levels available for assignment.</p>
                        <a href="{{ route('admin.levels.create') }}" class="text-emerald-600 hover:underline font-medium">
                            Create a level first
                        </a>
                    </div>
                @endif

                @if($levels->count() > 0)
                    <p class="text-sm text-gray-500 mt-4 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Skills can be assigned to multiple levels. Select all levels where this skill should be available.
                    </p>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.skills.index') }}"
                   class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition font-medium shadow-sm">
                    {{ isset($skill) ? 'Update Skill' : 'Create Skill' }}
                </button>
            </div>
        </form>
    </div>

     <!-- Related Content -->
     
    @if(isset($skill) && ($skill->videos_count > 0 || $skill->questions_count > 0))
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Related Content</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($skill->videos_count > 0)
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-blue-800">Videos</h4>
                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                {{ $skill->videos_count }}
                            </span>
                        </div>
                        <p class="text-sm text-blue-700">
                            This skill has {{ $skill->videos_count }} video(s) associated with it.
                        </p>
                        <a href="{{ route('admin.skills.show', ['skill' => $skill->skill_id]) }}" 
                           class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-800">
                            Manage videos
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif

                @if($skill->questions_count > 0)
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-green-800">Questions</h4>
                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                {{ $skill->questions_count }}
                            </span>
                        </div>
                        <p class="text-sm text-green-700">
                            This skill has {{ $skill->questions_count }} question(s) in the question bank.
                        </p>
                        <a href="{{ route('admin.skills.show', ['skill' => $skill->skill_id]) }}" 
                           class="inline-flex items-center mt-3 text-sm text-green-600 hover:text-green-800">
                            Manage questions
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush