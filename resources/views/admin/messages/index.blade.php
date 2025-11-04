
@extends('admin.layout')

@section('content')
<div class="flex h-[80vh] max-w-7xl mx-auto bg-white rounded shadow-lg border border-green-100 overflow-hidden">
    <!-- Sidebar: Conversations -->
    <div class="w-1/4 bg-green-50 border-r flex flex-col">
        <div class="p-4 border-b">
            <input type="text" placeholder="Search messages..." class="w-full px-3 py-2 rounded border border-green-200 focus:ring-2 focus:ring-green-400" />
        </div>
        <div class="flex-1 overflow-y-auto" style="min-height:0;max-height:calc(80vh-64px);">
            @php
                $firstUserId = null;
                $firstUser = null;
                $firstMessages = null;
            @endphp
            @foreach($userMessages as $userId => $messages)
                @php
                    $lastMsg = $messages->last();
                    $user = $lastMsg->sender_id == auth()->id() ? $lastMsg->receiver : $lastMsg->sender;
                    if ($loop->first) {
                        $firstUserId = $user->id;
                        $firstUser = $user;
                        $firstMessages = $messages;
                    }
                @endphp
                <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-green-100 transition border-b conversation-link {{ $loop->first ? 'bg-green-200 active' : '' }}" data-user-id="{{ $user->id }}">
                    <img src="{{ $user->photo ? asset('storage/' . ltrim($user->photo, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-green-800 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-600 truncate">{{ $lastMsg->message ? Str::limit($lastMsg->message, 30) : '[Image]' }}</div>
                    </div>
                    @if($messages->where('receiver_id', auth()->id())->where('is_read', 0)->count() > 0)
                        <span class="ml-2 bg-green-600 text-white text-xs rounded-full px-2 py-0.5 font-bold shadow">{{ $messages->where('receiver_id', auth()->id())->where('is_read', 0)->count() }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
    <!-- Main Chat -->
    <div class="w-2/4 flex flex-col" id="main-chat-area">
        @if($firstUser && $firstMessages)
        <div class="flex items-center gap-3 p-4 border-b bg-green-700 text-white" id="chat-header">
            <img src="{{ $firstUser->photo ? asset('storage/' . ltrim($firstUser->photo, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100">
            <div>
                <div class="font-bold text-lg" id="chat-user-name">{{ $firstUser->name }}</div>
                <div class="text-xs text-green-100" id="chat-user-email">{{ $firstUser->email }}</div>
            </div>
        </div>
        <div id="chat-messages" class="p-4 flex-1 overflow-y-auto bg-gray-50">
            @foreach($firstMessages as $msg)
                <div class="mb-4 flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs px-4 py-2 rounded-lg {{ $msg->sender_id == auth()->id() ? 'bg-green-200 text-green-900' : 'bg-white text-gray-800 border' }}">
                        @if($msg->image)
                            <img src="{{ asset('storage/' . $msg->image) }}" class="max-w-[150px] rounded mb-2">
                        @endif
                        <div>{{ $msg->message }}</div>
                        <div class="text-xs text-gray-400 mt-1 text-right">{{ $msg->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <form id="send-message-form" action="{{ route('admin.messages.send', $firstUser->id) }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-2 p-4 border-t bg-white">
            @csrf
            <label class="cursor-pointer flex items-center">
                <i class="bi bi-image text-2xl text-green-700"></i>
                <input type="file" name="image" accept="image/*" class="hidden" id="image-input">
            </label>
            <textarea name="message" id="message-input" rows="1" class="flex-1 border border-gray-300 focus:ring-2 focus:ring-green-400 text-base resize-none rounded px-3 py-2 bg-gray-50" placeholder="Type a message..." style="min-height: 30px; max-height: 90px; overflow-y:auto;"></textarea>
            <button type="submit" class="ml-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-lg flex items-center justify-center">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
        @else
        <div class="flex-1 flex items-center justify-center text-gray-400">No conversations yet.</div>
        @endif
    </div>
    <!-- User Profile -->
    <div class="w-1/4 bg-gray-50 border-l flex flex-col items-center p-8" id="profile-pane">
        @if($firstUser)
            <img src="{{ $firstUser->photo ? asset('storage/' . ltrim($firstUser->photo, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-24 h-24 rounded-full object-cover border-2 border-green-200 mb-4" id="profile-photo">
            <div class="font-bold text-xl text-green-900 mb-1" id="profile-name">{{ $firstUser->name }}</div>
            <div class="text-gray-600 text-sm mb-2" id="profile-email">{{ $firstUser->email }}</div>
            <div class="text-gray-500 text-xs mb-4" id="profile-joined">Joined {{ $firstUser->created_at->format('M Y') }}</div>
            <a href="{{ route('admin.users.show', $firstUser->id) }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition" id="profile-view-btn">View Profile</a>
        @else
            <div class="text-gray-400">No user selected</div>
        @endif
    </div>
</div>
<script>
    // Auto-scroll to bottom
    function scrollChatToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        if(chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    scrollChatToBottom();

    // AJAX send message
    const sendForm = document.getElementById('send-message-form');
    if(sendForm) {
        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                form.reset();
                location.reload();
            });
        });
    }

    // Sidebar click: show chat/profile for selected user (client-side only, for demo; for real-time, use AJAX)
    document.querySelectorAll('.conversation-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.conversation-link').forEach(l => l.classList.remove('bg-green-200', 'active'));
            this.classList.add('bg-green-200', 'active');
            // In a real app, use AJAX to fetch chat/profile for this user and update DOM
            // For now, reload with server-side route if needed
            window.location = "{{ url()->current() }}?user=" + this.dataset.userId;
        });
    });
</script>
@endsection
