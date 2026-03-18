@extends('layouts.user')

@section('title', 'Practice - ' . $skill->skill_name)
@section('subtitle', 'Test your knowledge with these questions')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Practice: {{ $skill->skill_name }}</h1>
                <p class="text-blue-100">
                    @if($questions->count() == 1)
                        Practice one question
                    @else
                        Answer {{ $questions->count() }} questions to test your knowledge
                    @endif
                </p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold">{{ $questions->count() }}</div>
                <div class="text-xs text-blue-100">
                    {{ $questions->count() == 1 ? 'Question' : 'Questions' }}
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.skills.show', $skill) }}" class="text-white/80 hover:text-white text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to skill
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-redo-alt text-red-500 mr-3 animate-spin"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-gray-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Time Elapsed</p>
                <p id="timer" class="text-xl font-mono font-bold text-gray-900">00:00</p>
            </div>
        </div>
        <div class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i> Take your time, no rush!
        </div>
    </div>

    @php
        $isAlreadyAnswered = $progress && $progress->status === 'completed';
    @endphp

    <form id="practice-form" action="{{ route('user.skills.practice.submit', $skill) }}" method="POST">
        @csrf
        <input type="hidden" name="time_spent" id="time_spent" value="{{ old('time_spent', 0) }}">

        <div class="space-y-6">
            @foreach($questions as $index => $question)
            @php
                $isIncorrect = session('incorrect_questions') && in_array($question->question_id, session('incorrect_questions'));
                $isMultiple = $question->question_type === 'multiple_choice';
            @endphp

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden transition-all 
                {{ $isAlreadyAnswered ? 'border-green-400' : ($isIncorrect ? 'border-red-500 ring-2 ring-red-50' : 'border-gray-200') }}">
                
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">
                            @if($questions->count() > 1)
                                Question {{ $index + 1 }} of {{ $questions->count() }}
                            @else
                                Question
                            @endif

                            @if($isIncorrect)
                                <span class="ml-2 text-red-600 font-bold text-xs uppercase animate-pulse">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Try Again
                                </span>
                            @elseif($isAlreadyAnswered)
                                <span class="ml-2 text-green-600 font-bold text-xs uppercase">
                                    <i class="fas fa-check-circle mr-1"></i> Correct
                                </span>
                            @endif
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            @if($question->difficulty === 'easy') bg-green-100 text-green-700
                            @elseif($question->difficulty === 'medium') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($question->difficulty) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-lg text-gray-900 mb-2">{{ $question->question_text }}</p>
                    @if($isMultiple)
                        <p class="text-xs text-blue-600 font-medium mb-6 uppercase tracking-wider">
                            <i class="fas fa-tasks mr-1"></i> Select all that apply
                        </p>
                    @else
                        <div class="mb-6"></div>
                    @endif

                    @if($question->video)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">📺 Related Video</h4>
                        <div class="bg-gray-900 rounded-lg overflow-hidden">
                            <div class="aspect-video">
                                @php
                                    $videoFile = $question->video->video_file;
                                    $youtubeId = null;
                                    if (preg_match('/(?:youtube\.com\/.*[?&]v=|youtu\.be\/)([^"&?\/\s]{11})/', $videoFile, $matches)) {
                                        $youtubeId = $matches[1];
                                    }
                                @endphp

                                @if($youtubeId)
                                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen class="w-full h-full"></iframe>
                                @else
                                    <video controls class="w-full h-full">
                                        <source src="{{ $videoFile }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </div>
                        </div>
                        <h5 class="mt-2 font-medium text-gray-800">{{ $question->video->title }}</h5>
                        <p class="text-sm text-gray-600 mt-1">{{ $question->video->description }}</p>
                    </div>
                    @endif

                    <div class="space-y-3">
                        @foreach($question->answers as $answer)
                        @php
                            // For checkboxes, we check if the ID exists in the old array
                            $oldAnswers = old("answers.{$question->question_id}", []);
                            $wasSelected = is_array($oldAnswers) ? in_array($answer->answer_id, $oldAnswers) : $oldAnswers == $answer->answer_id;
                            $isCorrectReveal = $isAlreadyAnswered && $answer->is_correct;
                        @endphp
                        <label class="flex items-center p-4 border rounded-xl transition-colors group 
                            {{ $isAlreadyAnswered ? 'cursor-default' : 'cursor-pointer' }}
                            {{ $isCorrectReveal ? 'bg-green-50 border-green-400' : ($wasSelected ? 'bg-blue-50 border-blue-400' : 'border-gray-200 hover:bg-blue-50 hover:border-blue-300') }}">
                            
                            <input type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                                name="answers[{{ $question->question_id }}]{{ $isMultiple ? '[]' : '' }}"
                                value="{{ $answer->answer_id }}"
                                class="w-4 h-4 text-blue-600 focus:ring-blue-500 {{ $isMultiple ? 'rounded' : '' }}"
                                {{ ($wasSelected || $isCorrectReveal) ? 'checked' : '' }}
                                {{ $isAlreadyAnswered ? 'disabled' : '' }}>
                            
                            <span class="ml-3 {{ $isCorrectReveal ? 'text-green-700 font-bold' : 'text-gray-700' }}">
                                {{ $answer->answer_text }}
                            </span>

                            @if($isCorrectReveal)
                                <i class="fas fa-check text-green-600 ml-auto"></i>
                            @endif
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-4 text-sm text-gray-500">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                        {{ $question->points }} points
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            @if($isAlreadyAnswered)
                <div class="px-8 py-4 bg-gray-400 text-white rounded-xl font-semibold shadow-lg cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>
                    You already answered it
                </div>
            @else
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-check-circle mr-2"></i>
                    Submit Answers
                </button>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
    let seconds = {{ old('time_spent', 0) }};
    const timerElement = document.getElementById('timer');
    const timeSpentInput = document.getElementById('time_spent');

    function updateTimer() {
        seconds++;
        const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
        const remainingSeconds = (seconds % 60).toString().padStart(2, '0');
        timerElement.textContent = `${minutes}:${remainingSeconds}`;
        timeSpentInput.value = seconds;
    }

    @if(!$isAlreadyAnswered)
        setInterval(updateTimer, 1000);
    @endif

    const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
    const remainingSeconds = (seconds % 60).toString().padStart(2, '0');
    timerElement.textContent = `${minutes}:${remainingSeconds}`;
</script>
@endpush

@endsection