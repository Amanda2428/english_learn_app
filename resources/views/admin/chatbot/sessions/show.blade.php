@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Chat Conversation</h1>
        <a href="{{ route('admin.chatbot.sessions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Back to Sessions</a>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">User</p>
                    <p class="text-lg font-medium">{{ $session->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $session->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Session Info</p>
                    <p class="text-sm">Started: {{ $session->started_at->format('M d, Y H:i:s') }}</p>
                    <p class="text-sm">Last Message: {{ $session->last_msg_at ? $session->last_msg_at->format('M d, Y H:i:s') : 'N/A' }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
            @foreach($session->messages as $message)
            <div class="flex {{ $message->user_message ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-2xl {{ $message->user_message ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                    @if($message->user_message)
                        <p class="text-sm">{{ $message->user_message }}</p>
                    @else
                        <p class="text-sm">{{ $message->bot_response }}</p>
                        @if($message->link_url)
                            <div class="mt-2 pt-2 border-t border-gray-300">
                                <a href="{{ $message->link_url }}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                    {{ $message->link_title ?? 'Click here for more info' }}
                                </a>
                            </div>
                        @endif
                        @if($message->rule_id)
                            <p class="text-xs text-gray-500 mt-1">Matched rule #{{ $message->rule_id }}</p>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection