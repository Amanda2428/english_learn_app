@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Conversation Details</h1>
                <p class="text-sm text-gray-500">Session #{{ $session->session_id }}</p>
            </div>
            <a href="{{ route('admin.chatbot.sessions.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Sessions
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if ($session->user->profile)
                                <img src="{{ Storage::url($session->user->profile) }}" alt="Profile Picture"
                                    class="w-16 h-16 rounded-full object-cover shadow-sm border border-gray-100">
                            @else
                                <div
                                    class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center border border-blue-200">
                                    <span class="text-blue-600 font-bold text-xl">
                                        {{ substr($session->user->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">User Information</p>
                            <p class="text-lg font-bold text-gray-900">{{ $session->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $session->user->email }}</p>
                        </div>
                    </div>
                    <div class="md:border-l md:pl-6 space-y-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Activity Log</p>
                        <p class="text-sm text-gray-700 flex justify-between">
                            <span class="font-medium">Started:</span>
                            <span>{{ $session->started_at?->format('M d, Y H:i:s') ?? 'N/A' }}</span>
                        </p>
                        <p class="text-sm text-gray-700 flex justify-between">
                            <span class="font-medium">Last Interaction:</span>
                            <span>{{ $session->last_msg_at?->format('M d, Y H:i:s') ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6 bg-gray-50/50 min-h-[400px] max-h-[600px] overflow-y-auto">
                @if ($session->messages->isEmpty())
                    <div class="text-center py-12">
                        <i class="fas fa-comments text-gray-300 text-5xl mb-3"></i>
                        <p class="text-gray-500">No messages found for this session.</p>
                    </div>
                @else
                    @foreach ($session->messages as $message)
                        <div class="flex justify-end">
                            <div class="max-w-md">
                                <div class="bg-blue-600 text-white rounded-2xl rounded-tr-none px-4 py-3 shadow-sm">
                                    <p class="text-sm leading-relaxed">{{ $message->user_message }}</p>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 text-right">
                                    {{ $message->created_at->format('h:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-start">
                            <div class="max-w-md">
                                <div
                                    class="bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-robot text-blue-600 text-[10px]"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">English Tutor Bot</span>
                                    </div>

                                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ $message->bot_response }}</p>

                                    @if ($message->link_url)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <a href="{{ $message->link_url }}" target="_blank"
                                                class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                {{ $message->link_title ?? 'View Resource' }}
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center mt-1 px-1">
                                    @if ($message->rule_id)
                                        <span class="text-[10px] text-gray-400 italic">Matched Rule
                                            #{{ $message->rule_id }}</span>
                                    @endif
                                    <span class="text-[10px] text-gray-400">
                                        {{ $message->created_at->format('h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200">
                <p class="text-xs text-gray-400 text-center uppercase tracking-widest font-medium">
                    End of Conversation Log
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Scrollbar for the chat window */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }
    </style>
@endpush
