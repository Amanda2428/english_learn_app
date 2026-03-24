@extends('layouts.admin')

@section('title', 'Questions')
@section('header', 'Questions Management')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-700">Questions</span>
    </div>
@endsection

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


@section('content')
    <div class="space-y-6">
        <!-- Top Summary -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Questions</h2>
                    <p class="text-blue-100 mt-2">
                        Manage questions, answers, and associate them with skills, levels, and videos.
                    </p>
                </div>
                <a href="{{ route('admin.questions.create') }}"
                    class="inline-flex items-center bg-white text-blue-700 px-4 py-2 rounded-xl font-medium hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Question
                </a>
            </div>
        </div>


        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form method="GET" action="{{ route('admin.questions.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Skill</label>
                    <select name="skill_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">All Skills</option>
                        @foreach (App\Models\Skill::orderBy('skill_name')->get() as $skill)
                            <option value="{{ $skill->skill_id }}"
                                {{ request('skill_id') == $skill->skill_id ? 'selected' : '' }}>
                                {{ $skill->skill_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Level</label>
                    <select name="level_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        @foreach (App\Models\Level::orderBy('level_order')->get() as $level)
                            <option value="{{ $level->level_id }}"
                                {{ request('level_id') == $level->level_id ? 'selected' : '' }}>
                                {{ $level->level_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Difficulty</label>
                    <select name="difficulty"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">All Difficulties</option>
                        <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Has Video</label>
                    <select name="has_video"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('has_video') == '1' ? 'selected' : '' }}>With Video</option>
                        <option value="0" {{ request('has_video') == '0' ? 'selected' : '' }}>Without Video</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.questions.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">All Questions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Question</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Level</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Skill</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Difficulty</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Points</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Video</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Answers</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($questions as $question)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $question->question_id }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ Str::limit($question->question_text, 60) }}</p>
                                        @if ($question->question_text !== $question->clean_question_text)
                                            <p class="text-xs text-gray-500 mt-1">(Contains video marker)</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (isset($question->level) && $question->level)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                            {{ $question->level->level_name }}
                                        </span>
                                    @elseif($question->level_id)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">
                                            Level ID: {{ $question->level_id }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                        {{ $question->skill->skill_name ?? 'N/A' }}
                                    </span>
                                </td>



                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $difficultyColors = [
                                            'easy' => 'bg-green-100 text-green-800',
                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                            'hard' => 'bg-red-100 text-red-800',
                                        ];
                                        $colorClass =
                                            $difficultyColors[$question->difficulty] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full {{ $colorClass }} text-xs font-semibold capitalize">
                                        {{ $question->difficulty }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                        {{ $question->points }} pts
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                    {{ str_replace('_', ' ', ucfirst($question->question_type)) }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($question->video_id)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Yes
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold">
                                        {{ $question->answers_count ?? $question->answers->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.questions.edit', $question->question_id) }}"
                                            class="text-blue-600 hover:text-blue-800">
                                            Edit
                                        </a>

                                        <button
                                            onclick="openDeleteModal({{ $question->question_id }}, '{{ addslashes(Str::limit($question->question_text, 30)) }}', {{ $question->answers->count() }})"
                                            class="text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-10 text-center text-gray-500">
                                    No questions found.
                                    <a href="{{ route('admin.questions.create') }}"
                                        class="text-blue-600 hover:underline font-medium">
                                        Create your first question
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if (method_exists($questions, 'links'))
            <div>
                {{ $questions->links() }}
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
                    <h3 class="text-xl font-bold text-gray-900">Delete Question</h3>
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
                        <!-- Message  -->
                    </div>

                    <div id="warningMessage" class="mt-3 hidden">
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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
                    <div>
                        <button onclick="closeDeleteModal()"
                            class="px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            Cancel
                        </button>
                    </div>
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-3  bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete Question
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

        function openDeleteModal(questionId, questionText, answersCount) {
            // Set the form action
            deleteForm.action = `/admin/questions/${questionId}`;

            // Set the message
            let message =
                `Are you sure you want to delete question <span class="font-bold text-red-600">"${questionText}"</span>?`;
            deleteModalMessage.innerHTML = `<p class="text-gray-700">${message}</p>`;

            // Show warning if there are answers
            if (parseInt(answersCount) > 0) {
                warningMessage.classList.remove('hidden');
                warningText.textContent =
                    `This question has ${answersCount} answer(s). Deleting it will also delete all associated answers.`;
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
