@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header with breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">Dashboard</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.chatbot.rules.index') }}" class="hover:text-gray-700 transition-colors">Chatbot Rules</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-gray-700 font-medium">{{ isset($rule) ? 'Edit' : 'Create' }} Rule</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    {{ isset($rule) ? 'Edit Chatbot Rule' : 'Create New Chatbot Rule' }}
                </h1>
                <p class="text-sm text-gray-600 mt-1 ml-11">
                    {{ isset($rule) ? 'Update rule details and settings' : 'Add a new rule to help the chatbot respond to specific keywords' }}
                </p>
            </div>
            
            @if(isset($rule))
            <div class="mt-4 sm:mt-0 ml-11 sm:ml-0">
                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                    </svg>
                    Rule ID: #{{ $rule->rule_id }}
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Form Header with icon -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h2 class="ml-3 text-lg font-semibold text-gray-900">Rule Information</h2>
            </div>
        </div>
        
        <form action="{{ isset($rule) ? route('admin.chatbot.rules.update', $rule) : route('admin.chatbot.rules.store') }}" 
              method="POST" 
              class="p-6 space-y-6">
            @csrf
            @if(isset($rule))
                @method('PUT')
            @endif
            
            <div class="space-y-6">
                <!-- Keyword Field -->
                <div class="space-y-2">
                    <label for="keyword" class="block text-sm font-medium text-gray-700">
                        Keyword <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="keyword" 
                               id="keyword" 
                               value="{{ old('keyword', $rule->keyword ?? '') }}" 
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('keyword') border-red-500 ring-red-500 @enderror" 
                               placeholder="e.g., grammar, vocabulary, pronunciation" 
                               required>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        The bot will respond when this keyword is detected in user messages
                    </p>
                    @error('keyword')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Response Text Field -->
                <div class="space-y-2">
                    <label for="response_text" class="block text-sm font-medium text-gray-700">
                        Response Text <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <textarea name="response_text" 
                                  id="response_text" 
                                  rows="5" 
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('response_text') border-red-500 ring-red-500 @enderror" 
                                  placeholder="Enter the response the chatbot should give when this keyword is detected..."
                                  required>{{ old('response_text', $rule->response_text ?? '') }}</textarea>
                    </div>
                    <p class="text-sm text-gray-500 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        This message will be sent to users when they use the keyword
                    </p>
                    @error('response_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">Optional Link</h3>
                        <span class="ml-3 px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Not Required</span>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-5 space-y-4">
                        <div class="space-y-2">
                            <label for="link_url" class="block text-sm font-medium text-gray-700">Link URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <input type="url" 
                                       name="link_url" 
                                       id="link_url" 
                                       value="{{ old('link_url', $rule->link_url ?? '') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('link_url') border-red-500 ring-red-500 @enderror" 
                                       placeholder="https://example.com">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Add a link to provide additional resources (optional)</p>
                            @error('link_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="link_title" class="block text-sm font-medium text-gray-700">Link Title</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="link_title" 
                                       id="link_title" 
                                       value="{{ old('link_title', $rule->link_title ?? '') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('link_title') border-red-500 ring-red-500 @enderror" 
                                       placeholder="Click here for more info">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">The text displayed for the link (optional, defaults to URL if not provided)</p>
                            @error('link_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">💡 Preview</h4>
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">When user says "{{ old('keyword', $rule->keyword ?? 'keyword') }}":</span><br>
                                <span class="italic">"{{ old('response_text', $rule->response_text ?? 'Your response will appear here') }}"</span>
                            </p>
                            @if(old('link_url', $rule->link_url ?? false))
                            <p class="text-sm text-blue-700 mt-2">
                                <span class="inline-flex items-center text-blue-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    {{ old('link_title', $rule->link_title ?? 'Link') }}: {{ old('link_url', $rule->link_url ?? '') }}
                                </span>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.chatbot.rules.index') }}" 
                       class="inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center items-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if(isset($rule))
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            @endif
                        </svg>
                        {{ isset($rule) ? 'Update Rule' : 'Create Rule' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Additional Tips Card -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-start">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Best Practices for Chatbot Rules</h4>
                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                    <li>Use specific keywords that users are likely to type</li>
                    <li>Keep responses clear and helpful</li>
                    <li>Add links to relevant resources when applicable</li>
                    <li>Test your rules after creating them</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keywordInput = document.getElementById('keyword');
        const responseInput = document.getElementById('response_text');
        const linkUrlInput = document.getElementById('link_url');
        const linkTitleInput = document.getElementById('link_title');
        
        function updatePreview() {
            const keyword = keywordInput.value || 'keyword';
            const response = responseInput.value || 'Your response will appear here';
            const linkUrl = linkUrlInput.value;
            const linkTitle = linkTitleInput.value || 'Link';
            

            

        }
        
        keywordInput?.addEventListener('input', updatePreview);
        responseInput?.addEventListener('input', updatePreview);
        linkUrlInput?.addEventListener('input', updatePreview);
        linkTitleInput?.addEventListener('input', updatePreview);
    });
</script>
@endpush

<style>
/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease-in-out;
}

/* Focus styles */
input:focus, textarea:focus, select:focus {
    outline: none;
}

/* Custom placeholder styles */
input::placeholder, textarea::placeholder {
    color: #9ca3af;
    font-size: 0.875rem;
}
</style>
@endsection