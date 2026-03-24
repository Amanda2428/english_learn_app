@extends('layouts.admin')

@section('title', 'Skills')
@section('header', 'Skills Management')

@section('breadcrumbs')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">Skills</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Top Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Skills Management</h2>
                <p class="text-blue-50 mt-2">
                    Create and manage skills, organize by levels, and track related learning content.
                </p>
            </div>
            <a href="{{ route('admin.skills.create') }}"
               class="inline-flex items-center bg-white text-blue-700 px-4 py-2 rounded-xl font-medium hover:bg-blue-50 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Skill
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <!-- Status Filter -->
                <div class="relative">
                    <select id="statusFilter" class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 pl-4 pr-10 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Level Filter -->
                <div class="relative">
                    <select id="levelFilter" class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 pl-4 pr-10 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="all">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->level_id }}">{{ $level->level_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search skills by name or description..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Skills</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-emerald-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Skills</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_questions'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Videos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_videos'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Skills Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">All Skills</h3>
            <span class="text-sm text-gray-600">Drag to reorder</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Skill Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Levels</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Videos</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody id="skillsTableBody" class="divide-y divide-gray-200 bg-white">
                    @forelse($skills as $skill)
                        <tr class="hover:bg-gray-50 transition skill-row" 
                            data-skill-id="{{ $skill->skill_id }}" 
                            data-skill-name="{{ strtolower($skill->skill_name) }}"
                            data-level-ids="{{ $skill->levels->pluck('level_id')->join(',') }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $skill->skill_id }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="drag-handle cursor-move mr-2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 skill-name">{{ $skill->skill_name }}</p>
                                    </div>
                                </div>
                             </td>

                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs skill-description">
                                {{ Str::limit($skill->description, 50) ?: 'No description' }}
                             </td>

                            <!-- Levels Column -->
                            <td class="px-6 py-4">
                                @if($skill->levels->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($skill->levels->take(2) as $level)
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs level-badge" data-level-id="{{ $level->level_id }}">
                                                {{ $level->level_name }}
                                            </span>
                                        @endforeach
                                        @if($skill->levels->count() > 2)
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">
                                                +{{ $skill->levels->count() - 2 }} more
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No levels</span>
                                @endif
                             </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($skill->status)
                                    <span class="status-badge inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                        Active
                                    </span>
                                @else
                                    <span class="status-badge inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">
                                        Inactive
                                    </span>
                                @endif
                             </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                    {{ $skill->videos_count ?? 0 }}
                                </span>
                             </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                    {{ $skill->questions_count ?? 0 }}
                                </span>
                             </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('admin.skills.edit', $skill->skill_id) }}"
                                       class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </a>

                                    <a href="{{ route('admin.skills.show', $skill->skill_id) }}"
                                       class="text-indigo-600 hover:text-indigo-800">
                                        View
                                    </a>

                                    <button onclick="openDeleteModal({{ $skill->skill_id }}, '{{ addslashes($skill->skill_name) }}', {{ $skill->levels_count ?? 0 }}, {{ $skill->questions_count ?? 0 }})" 
                                            class="text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                No skills found.
                                <a href="{{ route('admin.skills.create') }}" class="text-emerald-600 hover:underline font-medium">
                                    Create your first skill
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($skills, 'links'))
        <div>
            {{ $skills->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
        
        <div class="relative bg-white rounded-lg w-full max-w-md shadow-2xl transform transition-all">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Delete Skill</h3>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>

                <div id="deleteModalMessage" class="text-center"></div>

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
                        Delete Skill
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Levels View Modal -->
<div id="levelsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeLevelsModal()"></div>
        
        <div class="relative bg-white rounded-lg w-full max-w-lg shadow-2xl transform transition-all">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Assigned Levels</h3>
                <button onclick="closeLevelsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div id="levelsList" class="space-y-3"></div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end">
                <button onclick="closeLevelsModal()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
// Delete Modal Functions
let deleteModal = document.getElementById('deleteModal');
let deleteForm = document.getElementById('deleteForm');
let deleteModalMessage = document.getElementById('deleteModalMessage');
let warningMessage = document.getElementById('warningMessage');
let warningText = document.getElementById('warningText');

function openDeleteModal(skillId, skillName, levelsCount, questionsCount) {
    deleteForm.action = `/admin/skills/${skillId}`;
    
    let message = `Are you sure you want to delete skill <span class="font-bold text-red-600">"${skillName}"</span>?`;
    deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;
    
    let warnings = [];
    if (parseInt(levelsCount) > 0) {
        warnings.push(`${levelsCount} assigned level(s)`);
    }
    if (parseInt(questionsCount) > 0) {
        warnings.push(`${questionsCount} question(s)`);
    }
    
    if (warnings.length > 0) {
        warningMessage.classList.remove('hidden');
        warningText.textContent = `This skill has ${warnings.join(' and ')}. Deleting it will affect related content.`;
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

// Levels Modal Functions
let levelsModal = document.getElementById('levelsModal');
let levelsList = document.getElementById('levelsList');

function showLevels(skillId) {
    fetch(`/admin/skills/${skillId}/levels`)
        .then(response => response.json())
        .then(data => {
            levelsList.innerHTML = '';
            if (data.levels.length > 0) {
                data.levels.forEach(level => {
                    levelsList.innerHTML += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-900">${level.level_name}</span>
                            <span class="text-sm text-gray-600">Order: ${level.level_order}</span>
                        </div>
                    `;
                });
            } else {
                levelsList.innerHTML = '<p class="text-center text-gray-500">No levels assigned</p>';
            }
            levelsModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
}

function closeLevelsModal() {
    levelsModal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const levelFilter = document.getElementById('levelFilter');
    const searchInput = document.getElementById('searchInput');
    
    if (statusFilter) statusFilter.addEventListener('change', filterSkills);
    if (levelFilter) levelFilter.addEventListener('change', filterSkills);
    if (searchInput) searchInput.addEventListener('keyup', filterSkills);
});

function filterSkills() {
    const status = document.getElementById('statusFilter').value;
    const level = document.getElementById('levelFilter').value;
    const search = document.getElementById('searchInput').value.toLowerCase().trim();
    
    const rows = document.querySelectorAll('.skill-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let show = true;
        
        // Status filter
        if (status !== 'all') {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                const isActive = statusBadge.classList.contains('bg-green-100');
                if ((status === 'active' && !isActive) || (status === 'inactive' && isActive)) {
                    show = false;
                }
            }
        }
        
        // Level filter
        if (show && level !== 'all') {
            const levelBadges = row.querySelectorAll('.level-badge');
            let hasLevel = false;
            levelBadges.forEach(badge => {
                if (badge.dataset.levelId === level) {
                    hasLevel = true;
                }
            });
            if (!hasLevel) {
                show = false;
            }
        }
        
        // Search filter 
        if (show && search) {
            const skillName = row.querySelector('.skill-name')?.textContent.toLowerCase() || '';
            const description = row.querySelector('.skill-description')?.textContent.toLowerCase() || '';
            
            if (!skillName.includes(search) && !description.includes(search)) {
                show = false;
            }
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    // Show/hide no results message
    const noResultsRow = document.querySelector('#no-results-row');
    if (visibleCount === 0) {
        if (!noResultsRow) {
            const tbody = document.getElementById('skillsTableBody');
            const tr = document.createElement('tr');
            tr.id = 'no-results-row';
            tr.innerHTML = '<td colspan="8" class="px-6 py-10 text-center text-gray-500">No skills match your filters.';
            tbody.appendChild(tr);
        }
    } else if (noResultsRow) {
        noResultsRow.remove();
    }
}

// Drag and Drop Reordering
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('skillsTableBody');
    if (tableBody) {
        new Sortable(tableBody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const skillIds = [];
                document.querySelectorAll('.skill-row').forEach(row => {
                    skillIds.push(row.dataset.skillId);
                });
                
                fetch('{{ route("admin.skills.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: skillIds })
                });
            }
        });
    }
});

// Keyboard events
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!deleteModal.classList.contains('hidden')) {
            closeDeleteModal();
        }
        if (!levelsModal.classList.contains('hidden')) {
            closeLevelsModal();
        }
    }
});

// REMOVED: Flash messages alerts - no more alerts showing
// @if(session('success'))
//     alert('{{ session('success') }}');
// @endif

// @if(session('error'))
//     alert('{{ session('error') }}');
// @endif
</script>

<style>
/* Modal animations */
#deleteModal, #levelsModal {
    transition: opacity 0.3s ease;
}

#deleteModal .bg-white, #levelsModal .bg-white {
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

/* Drag handle hover effect */
.drag-handle:hover {
    transform: scale(1.1);
}

/* Table row transition */
.skill-row {
    transition: background-color 0.2s ease;
}

/* Filter selects styling */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
</style>
@endpush