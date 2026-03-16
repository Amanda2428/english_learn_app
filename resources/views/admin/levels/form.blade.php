@extends('layouts.admin')

@section('title', isset($level) ? 'Edit Level' : 'Create Level')
@section('header', isset($level) ? 'Edit Level' : 'Create Level')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.levels.index') }}" class="hover:text-gray-900">Levels</a>
        <span>/</span>
        <span class="text-gray-700">{{ isset($level) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Intro -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold">{{ isset($level) ? 'Edit Level' : 'Create New Level' }}</h2>
        <p class="text-blue-100 mt-2">
            {{ isset($level) ? 'Update the level name, order, and description.' : 'Add a new level to organize users, skills, videos, and questions.' }}
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
        <form action="{{ isset($level) ? route('admin.levels.update', $level->level_id) : route('admin.levels.store') }}" method="POST">
            @csrf
            @if(isset($level))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Level Name -->
                <div class="md:col-span-2">
                    <label for="level_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Level Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="level_name"
                        id="level_name"
                        value="{{ old('level_name', $level->level_name ?? '') }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level_name') border-red-500 @else border-gray-300 @enderror"
                        placeholder="Enter level name"
                        required
                        autofocus
                    >
                    @error('level_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Level Order -->
                <div>
                    <label for="level_order" class="block text-sm font-semibold text-gray-700 mb-2">
                        Level Order <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="level_order"
                        id="level_order"
                        value="{{ old('level_order', $level->level_order ?? ($nextOrder ?? 1)) }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level_order') border-red-500 @else border-gray-300 @enderror"
                        min="1"
                        step="1"
                        required
                    >
                    @error('level_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if(!isset($level))
                        <p class="mt-1 text-sm text-gray-500">
                            Suggested next order: <span class="font-semibold">{{ $nextOrder ?? 1 }}</span>
                        </p>
                    @endif
                </div>

                <!-- Preview Info -->
                <div class="bg-gray-50 rounded-xl p-4 border">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Level Tips</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Lower order appears first</li>
                        <li>• Keep names short and clear</li>
                        <li>• Description is optional</li>
                    </ul>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="5"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @else border-gray-300 @enderror"
                        placeholder="Write a short description for this level..."
                    >{{ old('description', $level->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('admin.levels.index') }}"
                   class="px-5 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                    {{ isset($level) ? 'Update Level' : 'Create Level' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection