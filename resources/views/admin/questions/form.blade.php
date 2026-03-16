@extends('layouts.admin')

@section('title', isset($question) ? 'Edit Question' : 'Create Question')
@section('header', isset($question) ? 'Edit Question' : 'Create Question')

@section('breadcrumbs')
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.questions.index') }}" class="hover:text-gray-900">Questions</a>
        <span>/</span>
        <span class="text-gray-700">{{ isset($question) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Intro -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <h2 class="text-2xl font-bold">{{ isset($question) ? 'Edit Question' : 'Create New Question' }}</h2>
            <p class="text-blue-100 mt-2">
                {{ isset($question) ? 'Update the question text, answers, and associations.' : 'Add a new question with answers and associate it with a skill and optional video.' }}
            </p>
        </div>

        <!-- Validation / Flash -->
        @if (session('success'))
            <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 border border-red-200">
                <p class="font-semibold mb-2">Please fix the following:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <form
                action="{{ isset($question) ? route('admin.questions.update', $question->question_id) : route('admin.questions.store') }}"
                method="POST" id="questionForm">
                @csrf
                @if (isset($question))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Skill Selection -->
                    <div>
                        <label for="skill_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Skill <span class="text-red-500">*</span>
                        </label>
                        <select name="skill_id" id="skill_id" onchange="loadLevels(this.value)"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('skill_id') border-red-500 @else border-gray-300 @enderror"
                            required>
                            <option value="">Select a skill</option>
                            @foreach (App\Models\Skill::orderBy('skill_name')->get() as $skill)
                                <option value="{{ $skill->skill_id }}"
                                    data-levels="{{ $skill->levels->pluck('level_id')->join(',') }}"
                                    {{ (old('skill_id') ?? ($question->skill_id ?? '')) == $skill->skill_id ? 'selected' : '' }}>
                                    {{ $skill->skill_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('skill_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Level Selection (based on selected skill) -->
                    <div>
                        <label for="level_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Level (Optional)
                        </label>
                        <select name="level_id" id="level_id"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level_id') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Select a level (optional)</option>
                            @if (isset($question) && $question->skill_id)
                                @php
                                    $skillLevels = App\Models\Level::whereHas('skills', function ($q) use ($question) {
                                        $q->where('skills.skill_id', $question->skill_id);
                                    })
                                        ->orderBy('level_order')
                                        ->get();
                                @endphp
                                @foreach ($skillLevels as $level)
                                    <option value="{{ $level->level_id }}"
                                        {{ old('level_id', $question->level_id) == $level->level_id ? 'selected' : '' }}>
                                        {{ $level->level_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('level_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Question Text -->
                    <div class="md:col-span-2">
                        <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-2">
                            Question Text <span class="text-red-500">*</span>
                        </label>
                        <textarea name="question_text" id="question_text" rows="4"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('question_text') border-red-500 @else border-gray-300 @enderror"
                            placeholder="Enter your question here..." required>{{ old('question_text', $question->clean_question_text ?? '') }}</textarea>
                        @error('question_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label for="difficulty" class="block text-sm font-semibold text-gray-700 mb-2">
                            Difficulty <span class="text-red-500">*</span>
                        </label>
                        <select name="difficulty" id="difficulty"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('difficulty') border-red-500 @else border-gray-300 @enderror"
                            required>
                            <option value="easy"
                                {{ (old('difficulty') ?? ($question->difficulty ?? '')) == 'easy' ? 'selected' : '' }}>Easy
                            </option>
                            <option value="medium"
                                {{ (old('difficulty') ?? ($question->difficulty ?? '')) == 'medium' ? 'selected' : '' }}>
                                Medium</option>
                            <option value="hard"
                                {{ (old('difficulty') ?? ($question->difficulty ?? '')) == 'hard' ? 'selected' : '' }}>Hard
                            </option>
                        </select>
                        @error('difficulty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Points -->
                    <div>
                        <label for="points" class="block text-sm font-semibold text-gray-700 mb-2">
                            Points <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="points" id="points"
                            value="{{ old('points', $question->points ?? 10) }}" min="0" step="1"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('points') border-red-500 @else border-gray-300 @enderror"
                            required>
                        @error('points')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Question Type -->
                    <div>
                        <label for="question_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Question Type <span class="text-red-500">*</span>
                        </label>
                        <select name="question_type" id="question_type" onchange="toggleQuestionType(this.value)"
                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('question_type') border-red-500 @else border-gray-300 @enderror"
                            required>
                            <option value="multiple_choice"
                                {{ (old('question_type') ?? ($question->question_type ?? '')) == 'multiple_choice' ? 'selected' : '' }}>
                                Multiple Choice</option>
                            <option value="true_false"
                                {{ (old('question_type') ?? ($question->question_type ?? '')) == 'true_false' ? 'selected' : '' }}>
                                True/False</option>
                            <option value="choose_correct"
                                {{ (old('question_type') ?? ($question->question_type ?? '')) == 'choose_correct' ? 'selected' : '' }}>
                                Choose Correct One</option>
                        </select>
                        @error('question_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Correct Answers Toggle (for Multiple Choice) -->
                    <div id="multipleCorrectToggle"
                        class="md:col-span-2 {{ (old('question_type') ?? ($question->question_type ?? '')) == 'multiple_choice' ? '' : 'hidden' }}">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="allow_multiple_correct" name="allow_multiple_correct"
                                    value="1"
                                    {{ old('allow_multiple_correct', $question->allow_multiple_correct ?? false) ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <label for="allow_multiple_correct" class="ml-3 text-sm font-semibold text-gray-700">
                                    Allow multiple correct answers
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-8">
                                Enable this if the question can have more than one correct answer
                            </p>
                        </div>
                    </div>

                    <!-- Video Association Toggle -->
                    <div class="md:col-span-2">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="has_video" x-data x-init="$el.checked = {{ isset($question) && $question->video_id ? 'true' : 'false' }}"
                                    @change="document.getElementById('videoSelector').classList.toggle('hidden')"
                                    class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <label for="has_video" class="ml-3 text-sm font-semibold text-gray-700">
                                    Associate with a video
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-8">
                                Enable this if the question should be linked to a specific video lesson
                            </p>
                        </div>
                    </div>

                    <!-- Video Selector (Hidden by default) -->
                    <div id="videoSelector"
                        class="md:col-span-2 {{ isset($question) && $question->video_id ? '' : 'hidden' }}">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Select Video
                            </label>
                            @include('admin.questions._video_select', [
                                'selectedVideoIds' =>
                                    isset($question) && $question->video_id ? [$question->video_id] : [],
                                'skillId' => old('skill_id') ?? ($question->skill_id ?? null),
                            ])
                        </div>
                    </div>

                    <!-- Answers Section -->
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-semibold text-gray-700">
                                Answers <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="addAnswer()"
                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-xl hover:bg-green-700 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Answer
                            </button>
                        </div>

                        <div id="answersContainer" class="space-y-3">
                            <!-- Answers will be inserted here via JavaScript -->
                        </div>

                        <p class="text-xs text-gray-500 mt-2" id="answersHelpText">
                            For multiple choice, you can select one or more correct answers using the checkboxes.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('admin.questions.index') }}"
                        class="px-5 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                        {{ isset($question) ? 'Update Question' : 'Create Question' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Answers Management
            let answers = [];
            let allowMultipleCorrect = document.getElementById('allow_multiple_correct')?.checked || false;

            @if (isset($question) && $question->answers->count() > 0)
                // Load existing answers
                answers = @json($question->answers->map->only(['answer_id','answer_text','is_correct'])->values());
                  
            @else
                // Initialize with 2 empty answers
                answers = [{
                        answer_text: '',
                        is_correct: false
                    },
                    {
                        answer_text: '',
                        is_correct: false
                    }
                ];
                // Set first answer as correct by default for single correct mode
                if (!allowMultipleCorrect) {
                    answers[0].is_correct = true;
                }
            @endif

            function renderAnswers() {
                const container = document.getElementById('answersContainer');
                const questionType = document.getElementById('question_type').value;
                const isMultipleChoice = questionType === 'multiple_choice';
                const isMultipleCorrect = document.getElementById('allow_multiple_correct')?.checked || false;

                let html = '';

                answers.forEach((answer, index) => {
                    const isCorrect = answer.is_correct;
                    const borderClass = isCorrect ? 'border-green-300 bg-green-50' : 'border-gray-200';

                    html += `
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl border ${borderClass}" id="answer-${index}">
                            <div class="flex-1">
                                <input type="text" 
                                       name="answers[${index}][answer_text]"
                                       value="${(answer.answer_text || '').replace(/"/g, '&quot;')}"
                                       placeholder="Enter answer option ${index + 1}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"
                                       onchange="updateAnswer(${index}, this.value)">
                                ${answer.answer_id ? `<input type="hidden" name="answers[${index}][answer_id]" value="${answer.answer_id}">` : ''}
                            </div>
                            <div class="flex items-center gap-3">
                                ${isMultipleChoice ? `
                                    <label class="flex items-center cursor-pointer">
                                        <input type="${isMultipleCorrect ? 'checkbox' : 'radio'}" 
                                               name="${isMultipleCorrect ? 'correct_answers[' + index + ']' : 'correct_answer'}"
                                               value="${index}"
                                               ${isCorrect ? 'checked' : ''}
                                               onchange="${isMultipleCorrect ? `toggleCorrectCheckbox(${index})` : `setSingleCorrectAnswer(${index})`}"
                                               class="h-5 w-5 text-blue-600 ${isMultipleCorrect ? 'rounded' : 'rounded-full'} border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">Correct</span>
                                    </label>
                                ` : `
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" 
                                               name="correct_answer"
                                               value="${index}"
                                               ${isCorrect ? 'checked' : ''}
                                               onchange="setSingleCorrectAnswer(${index})"
                                               class="h-5 w-5 text-blue-600 rounded-full border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">Correct</span>
                                    </label>
                                `}
                                <button type="button" 
                                        onclick="removeAnswer(${index})"
                                        class="text-red-600 hover:text-red-800 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

                // Update help text
                const helpText = document.getElementById('answersHelpText');
                if (isMultipleChoice && isMultipleCorrect) {
                    helpText.textContent = 'You can select multiple correct answers using the checkboxes.';
                } else if (isMultipleChoice) {
                    helpText.textContent = 'Select one correct answer using the radio buttons.';
                } else {
                    helpText.textContent = 'Select the correct answer using the radio button.';
                }

                // Update hidden inputs for is_correct
                updateCorrectAnswerInputs();
            }

            function updateAnswer(index, value) {
                answers[index].answer_text = value;
            }

            function setSingleCorrectAnswer(index) {
                answers.forEach((answer, i) => {
                    answer.is_correct = (i === parseInt(index));
                });
                renderAnswers();
            }

            function toggleCorrectCheckbox(index) {
                answers[index].is_correct = !answers[index].is_correct;
                renderAnswers();
            }

            function addAnswer() {
                answers.push({
                    answer_text: '',
                    is_correct: false
                });
                renderAnswers();
            }

            function removeAnswer(index) {
                if (answers.length <= 2) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cannot Remove',
                            text: 'You need at least 2 answers for a question.',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        alert('You need at least 2 answers for a question.');
                    }
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Remove Answer?',
                        text: 'Are you sure you want to remove this answer?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performAnswerRemoval(index);
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: 'The answer has been removed.',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                } else {
                    if (confirm('Are you sure you want to remove this answer?')) {
                        performAnswerRemoval(index);
                        alert('Answer removed successfully.');
                    }
                }
            }

            function performAnswerRemoval(index) {
                answers.splice(index, 1);
                const questionType = document.getElementById('question_type').value;
                const isMultipleCorrect = document.getElementById('allow_multiple_correct')?.checked || false;

                if (questionType === 'multiple_choice' && !isMultipleCorrect) {
                    const hasCorrect = answers.some(a => a.is_correct);
                    if (!hasCorrect && answers.length > 0) {
                        answers[0].is_correct = true;
                    }
                }
                renderAnswers();
            }

            function updateCorrectAnswerInputs() {
                document.querySelectorAll('input[name^="answers"][name$="[is_correct]"]').forEach(el => el.remove());

                answers.forEach((answer, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `answers[${index}][is_correct]`;
                    input.value = answer.is_correct ? '1' : '0';
                    document.getElementById('questionForm').appendChild(input);
                });
            }

            document.getElementById('questionForm').addEventListener('submit', function(e) {
                const hasCorrect = answers.some(a => a.is_correct);
                if (!hasCorrect) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Correct Answer',
                            text: 'Please select at least one correct answer.',
                            timer: 3000,
                            showConfirmButton: true
                        });
                    } else {
                        alert('Please select at least one correct answer.');
                    }
                    return;
                }
                updateCorrectAnswerInputs();
            });

            function toggleQuestionType(type) {
                const multipleCorrectToggle = document.getElementById('multipleCorrectToggle');
                const allowMultipleCorrect = document.getElementById('allow_multiple_correct');

                if (type === 'multiple_choice') {
                    multipleCorrectToggle.classList.remove('hidden');
                    if (answers.length === 2) {
                        if (allowMultipleCorrect?.checked) {
                            answers.forEach(a => a.is_correct = false);
                        } else {
                            answers.forEach((a, i) => a.is_correct = i === 0);
                        }
                    }
                } else {
                    multipleCorrectToggle.classList.add('hidden');
                    const hasCorrect = answers.some(a => a.is_correct);
                    if (!hasCorrect && answers.length > 0) {
                        answers[0].is_correct = true;
                    } else if (hasCorrect) {
                        let firstCorrect = true;
                        answers.forEach(a => {
                            if (a.is_correct && firstCorrect) {
                                firstCorrect = false;
                            } else if (a.is_correct) {
                                a.is_correct = false;
                            }
                        });
                    }
                }
                renderAnswers();
            }

            function loadLevels(skillId) {
                const levelSelect = document.getElementById('level_id');
                
                if (!skillId) {
                    levelSelect.innerHTML = '<option value="">Select a level (optional)</option>';
                    return;
                }

                levelSelect.innerHTML = '<option value="">Loading levels...</option>';
                levelSelect.disabled = true;

                fetch(`/admin/api/skills/${skillId}/levels`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(levels => {
                        let options = '<option value="">Select a level (optional)</option>';
                        const currentLevelId = '{{ $question->level_id ?? '' }}';
                        
                        levels.forEach(level => {
                            const selected = (currentLevelId && level.level_id == currentLevelId) ? 'selected' : '';
                            options += `<option value="${level.level_id}" ${selected}>${level.level_name}</option>`;
                        });
                        
                        levelSelect.innerHTML = options;
                        levelSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading levels:', error);
                        levelSelect.innerHTML = '<option value="">Error loading levels</option>';
                        levelSelect.disabled = false;
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const questionType = document.getElementById('question_type').value;
                const skillSelect = document.getElementById('skill_id');

                toggleQuestionType(questionType);
                renderAnswers();

                @if (isset($question) && $question->video_id)
                    document.getElementById('has_video').checked = true;
                    document.getElementById('videoSelector').classList.remove('hidden');
                @endif

                if (skillSelect && skillSelect.value) {
                    setTimeout(() => {
                        loadLevels(skillSelect.value);
                    }, 100);
                }

                if (skillSelect) {
                    skillSelect.addEventListener('change', function() {
                        loadLevels(this.value);
                        const hasVideoChecked = document.getElementById('has_video')?.checked;
                        if (hasVideoChecked && window.videoSelectorComponent) {
                            window.videoSelectorComponent.filters.skill_id = this.value;
                            window.videoSelectorComponent.fetchVideos();
                        }
                    });
                }

                const multipleCorrectToggle = document.getElementById('allow_multiple_correct');
                if (multipleCorrectToggle) {
                    multipleCorrectToggle.addEventListener('change', function() {
                        if (!this.checked) {
                            const hasCorrect = answers.some(a => a.is_correct);
                            if (!hasCorrect && answers.length > 0) {
                                answers[0].is_correct = true;
                            } else if (hasCorrect) {
                                let firstCorrect = true;
                                answers.forEach(a => {
                                    if (a.is_correct && firstCorrect) {
                                        firstCorrect = false;
                                    } else if (a.is_correct) {
                                        a.is_correct = false;
                                    }
                                });
                            }
                        }
                        renderAnswers();
                    });
                }

                const hasVideoCheckbox = document.getElementById('has_video');
                const videoSelector = document.getElementById('videoSelector');

                if (hasVideoCheckbox) {
                    hasVideoCheckbox.addEventListener('change', function() {
                        if (this.checked) {
                            videoSelector.classList.remove('hidden');
                        } else {
                            videoSelector.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection