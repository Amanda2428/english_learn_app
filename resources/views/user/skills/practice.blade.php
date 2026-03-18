@extends('layouts.user')

@section('title', 'Practice - ' . $skill->skill_name)
@section('subtitle', 'Test your knowledge with these questions')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Practice: {{ $skill->skill_name }}</h1>
                <p class="text-blue-100">Answer {{ $questions->count() }} questions to test your knowledge</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold">{{ $questions->count() }}</div>
                <div class="text-xs text-blue-100">Questions</div>
            </div>
        </div>
    </div>

    <!-- Timer -->
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
            <i class="fas fa-info-circle mr-1"></i>
            Take your time, no rush!
        </div>
    </div>

    <!-- Practice Form -->
    <form id="practice-form" action="{{ route('skills.practice.submit', $skill) }}" method="POST">
        @csrf
        <input type="hidden" name="time_spent" id="time_spent" value="0">
        
        <div class="space-y-6">
            @foreach($questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Question Header -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                @if($question->difficulty === 'easy') bg-green-100 text-green-700
                                @elseif($question->difficulty === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Question Content -->
                    <div class="p-6">
                        <p class="text-lg text-gray-900 mb-6">{{ $question->question_text }}</p>
                        
                        <!-- Answers -->
                        <div class="space-y-3">
                            @foreach($question->answers as $answer)
                                <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors group">
                                    <input type="radio" 
                                           name="answers[{{ $question->question_id }}][selected]" 
                                           value="{{ $answer->answer_id }}"
                                           class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                           required>
                                    <span class="ml-3 text-gray-700 group-hover:text-gray-900">{{ $answer->answer_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="mt-8 flex justify-center">
            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-check-circle mr-2"></i>
                Submit Answers
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Timer functionality
    let seconds = 0;
    const timerElement = document.getElementById('timer');
    const timeSpentInput = document.getElementById('time_spent');
    
    function updateTimer() {
        seconds++;
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
        timeSpentInput.value = seconds;
    }
    
    setInterval(updateTimer, 1000);
    
    // Prevent form submission if no answer selected for any question
    document.getElementById('practice-form').addEventListener('submit', function(e) {
        const questions = {{ $questions->count() }};
        let answered = 0;
        
        document.querySelectorAll('input[type="radio"]:checked').forEach(() => answered++);
        
        if (answered < questions) {
            e.preventDefault();
            alert('Please answer all questions before submitting.');
        }
    });
</script>
@endpush
@endsection