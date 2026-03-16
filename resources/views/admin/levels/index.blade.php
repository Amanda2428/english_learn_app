@extends('layouts.admin')

@section('title', 'Levels')
@section('header', 'Levels Management')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-700">Levels</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Top Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Levels</h2>
                <p class="text-blue-100 mt-2">
                    Manage level structure, order, and related learning content.
                </p>
            </div>
            <a href="{{ route('admin.levels.create') }}"
               class="inline-flex items-center bg-white text-blue-700 px-4 py-2 rounded-xl font-medium hover:bg-blue-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Level
            </a>
        </div>
    </div>

   

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">All Levels</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Level Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Skills</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Videos</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($levels as $level)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $level->level_id }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $level->level_name }}</p>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $level->level_order }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                {{ $level->description ?: 'No description' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                    {{ $level->skills_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                    {{ $level->videos_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                    {{ $level->questions_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                    {{ $level->users_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('admin.levels.edit', $level->level_id) }}"
                                       class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </a>

                                    <button onclick="openDeleteModal({{ $level->level_id }}, '{{ addslashes($level->level_name) }}', {{ $level->users_count ?? 0 }})" 
                                            class="text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                                No levels found.
                                <a href="{{ route('admin.levels.create') }}" class="text-blue-600 hover:underline font-medium">
                                    Create your first level
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($levels, 'links'))
        <div>
            {{ $levels->links() }}
        </div>
    @endif
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
                <h3 class="text-xl font-bold text-gray-900">Delete Level</h3>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>

                <div id="deleteModalMessage" class="text-center">
                    <!-- Message  -->
                </div>

                <div id="warningMessage" class="mt-3 hidden">
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                        Delete Level
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delete Modal Functions
let deleteModal = document.getElementById('deleteModal');
let deleteForm = document.getElementById('deleteForm');
let deleteModalMessage = document.getElementById('deleteModalMessage');
let warningMessage = document.getElementById('warningMessage');
let warningText = document.getElementById('warningText');

function openDeleteModal(levelId, levelName, userCount) {

    deleteForm.action = `/admin/levels/${levelId}`;
    

    let message = `Are you sure you want to delete level <span class="font-bold text-red-600">"${levelName}"</span>?`;
    deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;

    if (parseInt(userCount) > 0) {
        warningMessage.classList.remove('hidden');
        warningText.textContent = `This level has ${userCount} assigned user(s). Deleting it will affect these users.`;
    } else {
        warningMessage.classList.add('hidden');
    }
    
    deleteModal.classList.remove('hidden');
    

    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}


document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
        closeDeleteModal();
    }
});

@if(session('success'))
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

@if(session('error'))
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Modal animation */
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
@endpush