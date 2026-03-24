@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                Chatbot Rules
            </h1>
            <p class="text-sm text-gray-600 mt-1 ml-11">
                Manage automated responses for your chatbot
            </p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Stats Badge -->
            <div class="px-3 py-1.5 bg-gray-100 rounded-lg text-sm text-gray-600 hidden sm:block">
                <span class="font-semibold text-gray-900">{{ $totalRules }}</span> total rules
            </div>
            
            <!-- Add New Button -->
            <a href="{{ route('admin.chatbot.rules.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-sm hover:shadow transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Rule
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Rules</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalRules }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Keywords</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeKeywords }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">With Links</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rulesWithLinks }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Usage</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalUsage }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.chatbot.rules.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" 
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search rules by keyword or response..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <select name="filter" 
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="">All Rules</option>
                <option value="with_links" {{ ($currentFilter ?? '') == 'with_links' ? 'selected' : '' }}>With Links</option>
                <option value="no_links" {{ ($currentFilter ?? '') == 'no_links' ? 'selected' : '' }}>Without Links</option>
                <option value="most_used" {{ ($currentFilter ?? '') == 'most_used' ? 'selected' : '' }}>Most Used</option>
                <option value="least_used" {{ ($currentFilter ?? '') == 'least_used' ? 'selected' : '' }}>Least Used</option>
            </select>
            @if(($search ?? '') || ($currentFilter ?? ''))
            <a href="{{ route('admin.chatbot.rules.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Rules Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage Count</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rules as $rule)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $rule->rule_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-.56 2.242a1 1 0 11-1.94-.486L12.47 8H9.53l-.56 2.242a1 1 0 11-1.94-.486L7.47 8H5a1 1 0 010-2h2.97l.56-2.242a1 1 0 011.213-.727zM5.5 13.5a1 1 0 011-1h7a1 1 0 110 2h-7a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $rule->keyword }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $rule->response_text }}">
                                {{ $rule->response_text }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($rule->link_url)
                                <a href="{{ $rule->link_url }}" target="_blank" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    {{ $rule->link_title ?? 'View Link' }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $rule->messages_count }}</span>
                                @if($rule->messages_count > 0)
                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5">
                                    @php
                                        $maxUsage = $rules->max('messages_count');
                                        $percentage = $maxUsage > 0 ? ($rule->messages_count / $maxUsage) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.chatbot.rules.edit', $rule) }}" 
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <button onclick="openDeleteModal({{ $rule->rule_id }}, '{{ addslashes($rule->keyword) }}', {{ $rule->messages_count }})" 
                                    class="text-red-600 hover:text-red-900 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No rules found</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    @if(($search ?? '') || ($currentFilter ?? ''))
                                        No rules match your search criteria. Try adjusting your filters.
                                    @else
                                        Get started by creating your first chatbot rule.
                                    @endif
                                </p>
                                @if(($search ?? '') || ($currentFilter ?? ''))
                                <a href="{{ route('admin.chatbot.rules.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear Filters
                                </a>
                                @else
                                <a href="{{ route('admin.chatbot.rules.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Your First Rule
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Table Footer with Pagination -->
        @if($rules->hasPages() || $rules->total() > 0)
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $rules->firstItem() }}</span> 
                    to <span class="font-medium">{{ $rules->lastItem() }}</span> 
                    of <span class="font-medium">{{ $rules->total() }}</span> results
                </div>
                <div class="flex justify-center sm:justify-end">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Tips -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-blue-800 mb-1">💡 Quick Tips</h4>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Rules are triggered when user messages contain the exact keyword</li>
                    <li>You can add links to provide additional resources</li>
                    <li>Usage count helps you identify your most popular responses</li>
                    <li>Use the search and filters to find specific rules quickly</li>
                </ul>
            </div>
        </div>
    </div>
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
                <h3 class="text-xl font-bold text-gray-900">Delete Chatbot Rule</h3>
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
                        Delete Rule
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
// Delete Modal Functions
let deleteModal = document.getElementById('deleteModal');
let deleteForm = document.getElementById('deleteForm');
let deleteModalMessage = document.getElementById('deleteModalMessage');
let warningMessage = document.getElementById('warningMessage');
let warningText = document.getElementById('warningText');

function openDeleteModal(ruleId, keyword, usageCount) {
    // Set the form action
    deleteForm.action = `/admin/chatbot/rules/${ruleId}`;
    
    // Set the message
    let message = `Are you sure you want to delete the rule for keyword <span class="font-bold text-red-600">"${keyword}"</span>?`;
    deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;
    
    // Show warning if rule has been used
    if (parseInt(usageCount) > 0) {
        warningMessage.classList.remove('hidden');
        warningText.textContent = 
            `This rule has been used ${usageCount} time(s) in chat sessions. Deleting it may affect existing conversations.`;
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