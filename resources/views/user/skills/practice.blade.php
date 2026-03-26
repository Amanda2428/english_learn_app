@extends('layouts.user')

@section('title', 'Practice - ' . $skill->skill_name)

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white mb-8 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Practice: {{ $skill->skill_name }}</h1>
                <p class="text-blue-100 text-sm">Test your knowledge and earn points</p>
            </div>
            <div class="bg-white/20 px-4 py-2 rounded-xl text-center">
                <div class="text-2xl font-bold">{{ $questions->count() }}</div>
                <div class="text-[10px] uppercase tracking-wider">Questions</div>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <a href="{{ route('user.skills.show', $skill) }}" class="text-white/80 hover:text-white text-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to skill
            </a>
            <div class="flex items-center bg-black/20 px-3 py-1 rounded-full">
                <i class="fas fa-clock text-xs mr-2"></i>
                <span id="timer" class="font-mono font-bold text-sm">00:00</span>
            </div>
        </div>
    </div>

    {{-- Error Message --}}
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm animate-pulse">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @php $completedIds = $progress ? ($progress->completed_questions ?? []) : []; @endphp

    <form id="practice-form" action="{{ route('user.skills.practice.submit', $skill) }}" method="POST">
        @csrf
        <input type="hidden" name="time_spent" id="time_spent" value="{{ old('time_spent', 0) }}">

        <div class="space-y-6">
            @foreach($questions as $index => $question)
            @php
                $isAlreadyAnswered = in_array($question->question_id, $completedIds);
                $isIncorrect = session('incorrect_questions') && in_array($question->question_id, session('incorrect_questions'));
                $isMultiple = $question->question_type === 'multiple_choice';
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border-2 transition-all duration-300 overflow-hidden
                {{ $isAlreadyAnswered ? 'border-green-400 bg-green-50/20' : ($isIncorrect ? 'border-red-400 ring-4 ring-red-50' : 'border-gray-100') }}">

                <div class="px-6 py-4 bg-gray-50/50 border-b flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Question {{ $index + 1 }}</span>
                    @if($isAlreadyAnswered)
                        <span class="text-green-600 font-bold text-xs"><i class="fas fa-check-circle mr-1"></i> SOLVED</span>
                    @endif
                </div>

                <div class="p-6">
                    <p class="text-lg font-medium text-gray-800 mb-4">{{ $question->question_text }}</p>

                    {{-- Video Player Section --}}
                    @if($question->video)
                    <div class="mb-6 rounded-xl overflow-hidden shadow-lg bg-black group">
                        <div class="aspect-video relative">
                            @php
                                $videoFile = $question->video->video_file;
                                $youtubeId = null;
                                if (preg_match('/(?:youtube\.com\/.*[?&]v=|youtu\.be\/)([^"&?\/\s]{11})/', $videoFile, $matches)) {
                                    $youtubeId = $matches[1];
                                }
                            @endphp

                            @if($youtubeId)
                                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{ $youtubeId }}" 
                                    frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                            @else
                                <video controls class="w-full h-full">
                                    <source src="{{ asset($videoFile) }}" type="video/mp4">
                                </video>
                            @endif
                        </div>
                        <div class="p-3 bg-gray-900">
                            <h5 class="text-white text-xs font-medium italic"><i class="fas fa-play-circle mr-2 text-blue-400"></i>{{ $question->video->title }}</h5>
                        </div>
                    </div>
                    @endif

                    @if($isMultiple)
                        <div class="mb-4 inline-block px-3 py-1 bg-blue-50 text-blue-600 rounded-md text-[10px] font-bold uppercase">
                            <i class="fas fa-tasks mr-1"></i> Multiple Choice
                        </div>
                    @endif

                    {{-- Answer Options --}}
                    <div class="grid gap-3">
                        @foreach($question->answers as $answer)
                        @php
                            $oldAnswers = old("answers.{$question->question_id}", []);
                            $wasSelected = is_array($oldAnswers) ? in_array($answer->answer_id, $oldAnswers) : $oldAnswers == $answer->answer_id;
                            $shouldCheck = $isAlreadyAnswered ? $answer->is_correct : $wasSelected;
                        @endphp

                        <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all group
                            {{ $isAlreadyAnswered ? 'cursor-default' : 'hover:border-blue-400 hover:bg-blue-50' }}
                            {{ ($isAlreadyAnswered && $answer->is_correct) ? 'bg-green-50 border-green-500' : 'border-gray-100' }}
                            {{ ($wasSelected && !$isAlreadyAnswered) ? 'bg-blue-50 border-blue-500' : '' }}">
                            
                            <input type="{{ $isMultiple ? 'checkbox' : 'radio' }}" 
                                name="answers[{{ $question->question_id }}]{{ $isMultiple ? '[]' : '' }}" 
                                value="{{ $answer->answer_id }}" 
                                class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                {{ $shouldCheck ? 'checked' : '' }}
                                {{ $isAlreadyAnswered ? 'disabled' : '' }}>
                            
                            <span class="ml-4 font-medium {{ ($isAlreadyAnswered && $answer->is_correct) ? 'text-green-700' : 'text-gray-700' }}">
                                {{ $answer->answer_text }}
                            </span>

                            @if($isAlreadyAnswered && $answer->is_correct)
                                <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @php $allAnswered = $questions->every(fn($q) => in_array($q->question_id, $completedIds)); @endphp

        <div class="mt-10 mb-20 flex justify-center">
            @if($allAnswered)
                <a href="{{ route('user.skills.show', $skill) }}" class="px-10 py-4 bg-green-600 text-white rounded-2xl font-bold shadow-xl hover:bg-green-700 transition-all flex items-center">
                    <i class="fas fa-star mr-2"></i> All Done! Back to Skill
                </a>
            @else
                <button type="submit" class="px-12 py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-xl hover:bg-blue-700 transform hover:-translate-y-1 transition-all">
                    Submit All Answers
                </button>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let seconds = {{ old('time_spent', 0) }};
    const timerElement = document.getElementById('timer');
    const timeSpentInput = document.getElementById('time_spent');

    function updateTimer() {
        seconds++;
        const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
        const secs = (seconds % 60).toString().padStart(2, '0');
        timerElement.textContent = `${mins}:${secs}`;
        timeSpentInput.value = seconds;
    }

    @if(!$allAnswered)
        setInterval(updateTimer, 1000);
    @endif
</script>
@endpush