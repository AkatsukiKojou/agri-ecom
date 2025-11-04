@extends('user.layout') {{-- or 'admin.layout' if admin --}}

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white rounded shadow p-6">
    <h2 class="text-xl font-bold mb-4">Chat with {{ $otherUser->name }}</h2>
    <div class="border rounded p-3 mb-4" style="height: 300px; overflow-y: auto;">
        @foreach($messages as $msg)
            <div class="mb-2 {{ $msg->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                <span class="inline-block px-3 py-1 rounded 
                    {{ $msg->sender_id == auth()->id() ? 'bg-green-100' : 'bg-gray-100' }}">
                    {{ $msg->message }}
                </span>
                <div class="text-xs text-gray-400">
                    {{ $msg->created_at->format('M d, H:i') }}
                </div>
            </div>
        @endforeach
    </div>
    <form action="{{ route('chat.send') }}" method="POST" class="flex gap-2">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
        <input type="text" name="message" class="flex-1 border rounded px-3 py-2" placeholder="Type a message..." required>
        <button type="submit" class="bg-green-700 text-white px-4 rounded">Send</button>
    </form>
</div>
@endsection 