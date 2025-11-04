{{-- filepath: resources/views/user/layout.blade.php --}}
{{-- ================= HEADER SECTION ================= --}}
@section('header')
    {{-- Add your custom header code here. Example: --}}
    {{-- <div class="bg-green-700 text-white p-4 text-center font-bold text-xl">Welcome to AgriEcom User Dashboard!</div> --}}
@show
{{-- =============== END HEADER SECTION =============== --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AgriEcom User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
#chatbot-window {
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border-radius: 1rem;
    font-family: inherit;
}
#chatbot-messages {
    min-height: 200px;
    max-height: 300px;
    overflow-y: auto;
    background: #f9fafb;
}
#chatbot-input {
    border: none;
    outline: none;
    background: #f3f4f6;
}
</style>
</head>

{{-- <body class="bg-white min-h-screen flex flex-col text-green-900 font-sans"> --}}
<body class="bg-white min-h-screen flex flex-col text-green-900 font-sans">

    <!-- Navbar -->
    <nav class="fixed w-full z-40 backdrop-blur-lg bg-white/60 shadow-lg border-b border-green-100" x-data="{ open: false, dropdown: false }">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button class="md:hidden text-green-900" @click="open = !open">
                    <i class="bi bi-list text-3xl"></i>
                </button>
                <h2 class="text-2xl font-extrabold tracking-wide flex items-center gap-2 text-green-900">
                    <img src="{{ asset('agri-icon.png') }}" alt="AgriEcom" class="w-8 h-8 inline-block">
                    <span>AgriEcom</span>
                </h2>
            </div>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center justify-between w-full">
                <div class="flex-1 flex justify-center space-x-8 text-base font-semibold tracking-wide">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-green-700 text-white' : '' }}">
                        <i class="bi bi-house-door-fill"></i> Home
                    </a>
                    <a href="{{ route('user.profiles.index') }}"
                        class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2 {{ request()->routeIs('user.profiles.index') ? 'bg-green-700 text-white' : '' }}">
                        <i class="bi bi-person-lines-fill"></i> LSA
                    </a>
                    <a href="{{ route('user.services.index') }}"
                        class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2 {{ request()->routeIs('user.services.index') ? 'bg-green-700 text-white' : '' }}">
                        <i class="bi bi-gear-wide-connected"></i>Training Services
                    </a>
                    <a href="{{ route('user.products.index') }}"
                        class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2 {{ request()->routeIs('user.products.index') ? 'bg-green-700 text-white' : '' }}">
                        <i class="bi bi-basket-fill"></i> Products
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Widget -->
<!-- Widget -->
<!-- Widget -->
<div x-data="{ showWidget: false }" class="relative">
    <button title="Widgets" @click="showWidget = !showWidget; if(showWidget) { window.dispatchEvent(new CustomEvent('close-messages')); }" x-init="window.addEventListener('close-widgets', () => { showWidget = false; })" class="hover:text-green-700 transition">
        <i class="bi bi-grid-3x3-gap-fill text-2xl"></i>
    </button>
    <div 
        x-show="showWidget" 
        @click.away="showWidget = false" 
        @mouseleave="showWidget = false"
        x-transition
        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border p-4 z-50 space-y-3"
    >
        <h3 class="font-semibold text-green-800 mb-2 flex items-center gap-2"   >
            <i class="bi bi-grid-3x3-gap-fill"></i> Quick Widgets
        </h3>
        <a href="{{ route('user.myprofile') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-person-circle text-green-700"></i> My Account
        </a>
        <a href="{{ route('user.profiles.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-person-lines-fill text-green-700"></i> LSA
        </a>
        <a href="" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-bag-check-fill text-blue-600"></i> My Purchase
        </a>
        <a href="{{ route('user.bookings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-calendar2-check-fill text-orange-500"></i> My Booking
        </a>
        <a href="{{ route('user.services.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-gear-wide-connected text-gray-700"></i> Training Services
        </a>
        <a href="{{ route('user.products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-basket-fill text-green-700"></i> Products
        </a>
        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-gear text-gray-700"></i> Settings
        </a>
        <a href="{{ route('settings.password.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-lime-100">
            <i class="bi bi-key text-yellow-600"></i> Update Password
        </a>
    </div>
</div>
{{-- Messenger-style Message Dropdown --}}
@php
    $userId = auth()->id();
    $chats = \App\Models\Message::with(['sender', 'receiver'])
        ->where(function($q) use ($userId) {
            $q->where('receiver_id', $userId)
              ->orWhere('sender_id', $userId);
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->unique(function($item) use ($userId) {
            return $item->sender_id == $userId ? $item->receiver_id : $item->sender_id;
        })
        ->take(20)
        ->values();
    // Count unique senders with unread messages only (if all messages from a sender are read, do not count)
    $unreadSenders = \App\Models\Message::where('receiver_id', $userId)
        ->where('is_read', 0)
        ->get()
        ->groupBy('sender_id')
        ->filter(function($msgs) {
            return $msgs->where('is_read', 0)->count() > 0;
        })
        ->keys();
    $unreadCount = $unreadSenders->count();
@endphp
<div class="relative" x-data="{
    open: false,
    chatTab: 'all',
    searchChat: '',
    chats: {{ Js::from($chats) }},
    userId: null,
    userName: '',
    messages: [],
    message: '',
    imageFile: null,
    poll: null,
    minimized: false,
    filteredChats() {
        let filtered = this.chats;
        if (this.searchChat.trim() !== '') {
            filtered = filtered.filter(chat =>
                (chat.sender_id == {{ $userId }} ? chat.receiver.name : chat.sender.name)
                    .toLowerCase().includes(this.searchChat.toLowerCase())
            );
        }
        if (this.chatTab === 'unread') {
            filtered = filtered.filter(chat =>
                chat.receiver_id == {{ $userId }} && chat.is_read == 0
            );
        }
        return filtered;
    },
    openChat(id, name) {
        this.open = true;
        this.userId = id;
        this.userName = name;
        this.fetchMessages();
        if(this.poll) clearInterval(this.poll);
        this.poll = setInterval(() => this.fetchMessages(), 5000);
    },
    closeChat() {
        this.open = false;
        this.userId = null;
        this.userName = '';
        this.messages = [];
        this.message = '';
        this.imageFile = null;
        if(this.poll) clearInterval(this.poll);
    },
    fetchMessages() {
            fetch('/user/messages/fetch/' + this.userId)
                .then(res => res.json())
                .then(data => {
                    this.messages = data;
                    this.$nextTick(() => {
                        let box = document.getElementById('user-chat-messages');
                        if(box) box.scrollTop = box.scrollHeight;
                    });
                });
    },
    handleImageUpload(e) {
        const file = e.target.files[0];
        if (file) this.imageFile = file;
    },
    sendLike() {
        this.message = '👍';
        this.sendMessage();
    },
    sendMessage() {
        if (this.message.trim() !== '' || this.imageFile) {
            const formData = new FormData();
            formData.append('message', this.message);
            if (this.imageFile) formData.append('image', this.imageFile);
            fetch('/user/messages/' + this.userId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            }).then(() => {
                this.message = '';
                this.imageFile = null;
                this.fetchMessages();
            });
        }
    }
}">
        <button class="relative text-green-800 hover:text-green-600 transition" @click="open = !open" x-init="window.addEventListener('close-messages', () => { open = false; }); window.addEventListener('close-widgets', () => { open = false; })">
        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-50 hover:bg-green-100 transition">
            <i class="bi bi-chat-dots-fill text-green-700 text-xl"></i>
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 font-bold shadow">
                    {{ $unreadCount }}
                </span>
            @endif
        </span>
    </button>
    <div x-show="open" x-transition class="dropdown-anim absolute right-0 left-auto mt-2 w-80 bg-white border rounded-2xl shadow-2xl z-50 max-h-[40rem] overflow-auto p-0">
        <div class="p-4 border-b bg-gradient-to-r from-green-50 to-white rounded-t-2xl">
            <div class="text-lg font-bold text-green-800 mb-2 flex items-center gap-2">
                <i class="bi bi-chat-dots text-green-600"></i> Chats
            </div>
            <div class="relative mb-2">
                <input 
                    type="text"
                    x-model="searchChat"
                    placeholder="Search by name..."
                    class="w-full px-4 py-2 border border-green-200 rounded-full text-sm focus:ring-2 focus:ring-green-400 bg-gray-50 placeholder-gray-400"
                >
                <i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-green-400"></i>
            </div>
            <div class="flex gap-2 mt-1">
                <button type="button" @click="chatTab = 'all'" 
                    :class="chatTab === 'all' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-1 rounded-full text-xs font-semibold transition">All</button>
                <button type="button" @click="chatTab = 'unread'" 
                    :class="chatTab === 'unread' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-3 py-1 rounded-full text-xs font-semibold transition">Unread</button>
            </div>
        </div>
        <div class="divide-y">
            <template x-for="msg in filteredChats()" :key="msg.id">
                <a href="#"
                   @click.prevent="openChat(msg.sender_id == {{ $userId }} ? msg.receiver.id : msg.sender.id, msg.sender_id == {{ $userId }} ? msg.receiver.name : msg.sender.name)"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition">
                    <div class="relative">
                        <img :src="'/storage/' + ((msg.sender_id == {{ $userId }} ? msg.receiver.photo : msg.sender.photo) || 'default.png')" alt="Profile" class="w-11 h-11 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                        <template x-if="msg.is_read == 0 && msg.receiver_id == {{ $userId }}">
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div :class="msg.is_read == 0 && msg.receiver_id == {{ $userId }} ? 'font-bold text-green-800' : 'font-normal'" class="truncate text-base" x-text="msg.sender_id == {{ $userId }} ? msg.receiver.name : msg.sender.name"></div>
                        <div :class="msg.is_read == 0 && msg.receiver_id == {{ $userId }} ? 'font-semibold text-green-700' : 'font-normal text-gray-600'" class="truncate text-xs mt-0.5" x-text="msg.message.split(' ').slice(0,9).join(' ') + (msg.message.split(' ').length > 9 ? '...' : '')"></div>
                    </div>
                    <div class="text-xs text-gray-400 whitespace-nowrap ml-2" x-text="window.dayjs ? window.dayjs(msg.created_at).fromNow() : msg.created_at"></div>
                </a>
            </template>
            <div x-show="filteredChats().length == 0" class="px-4 py-8 text-gray-400 text-sm text-center">No new messages</div>
        </div>
        <div class="text-center mt-2 border-t bg-gray-50 rounded-b-2xl">
            <a href="#" class="text-green-600 hover:underline text-sm py-3 block font-semibold transition">View all messages</a>
        </div>
    </div>
    <!-- User Chatbox Modal -->
    <div 
        x-show="open && userId"
        x-transition
        :style="minimized ? 'width:4rem;height:4rem;padding:0;overflow:hidden;left:50%;transform:translateX(-50%);bottom:0;' : 'width:24rem; height:32rem;left:50%;transform:translateX(-50%);bottom:0;'"
        class="fixed bg-white border border-gray-300 rounded-full shadow-lg z-50"
        id="user-chatbox"
        style="display:none;"
    >
        <!-- Minimized State -->
        <template x-if="minimized">
            <div class="flex items-center justify-center h-full w-full cursor-pointer" @click="minimized = false">
                <img 
                    :src="messages.length && messages[0].sender && messages[0].sender.photo 
                        ? '/storage/' + messages[0].sender.photo 
                        : '/storage/default.png'"
                    alt="Profile"
                    class="w-12 h-12 rounded-full object-cover border-2 border-green-600"
                >
            </div>
        </template>
        <!-- Expanded State -->
        <template x-if="!minimized">
            <div class="flex flex-col h-full w-full rounded-xl overflow-hidden bg-white">
                <!-- Header -->
                <div class="flex items-center justify-between bg-green-600 text-white px-4 py-2">
                    <div class="flex items-center gap-2">
                        <img 
                            :src="messages.length && messages[0].sender && messages[0].sender.profile && messages[0].sender.profile.photo 
                                ? '/storage/' + messages[0].sender.profile.photo 
                                : '/storage/default.png'"
                            alt="Profile"
                            class="w-8 h-8 rounded-full object-cover border border-white"
                        >
                        <span x-text="userName"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="minimized = true" class="text-white text-xl leading-none" title="Minimize">
                            <i class="bi bi-dash-square"></i>
                        </button>
                        <button @click="closeChat()" class="text-white text-2xl leading-none">&times;</button>
                    </div>
                </div>
                <!-- Messages (scrollable only) -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="user-chat-messages">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_id == {{ $userId }} ? 'text-right' : 'text-left'" class="my-2">
                            <div :class="msg.sender_id == {{ $userId }} ? 'inline-block bg-green-100 text-green-800' : 'inline-block bg-gray-100 text-gray-800'"
                                class="rounded-lg px-3 py-2 max-w-xs"
                                style="word-break:break-word;">
                                <template x-if="msg.image">
                                    <img 
                                        :src="'/storage/' + msg.image" 
                                        class="max-w-[120px] rounded mb-2"
                                        style="display:block;margin-left:auto;margin-right:auto;"
                                    />
                                </template>
                                <template x-if="msg.message">   
                                    <div class="block w-full">
                                        <span x-text="msg.message"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                <span x-text="new Date(msg.created_at).toLocaleString()"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="messages.length == 0" class="text-gray-400 text-center text-xs mt-10">No messages yet.</div>
                </div>
                <!-- Input Area (fixed at the bottom, always visible) -->
                <form @submit.prevent="sendMessage()" class="border-t px-2 py-3 bg-white sticky bottom-0 z-10">
                    <div class="flex items-end gap-2 w-full">
                        <!-- Image Upload -->
                        <label class="cursor-pointer flex-shrink-0">
                            <i class="bi bi-image text-2xl text-green-700"></i>
                            <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                        </label>

                        <!-- Image Preview -->
                        <template x-if="imageFile">
                            <img :src="URL.createObjectURL(imageFile)" class="max-w-[80px] max-h-[90px] rounded border flex-shrink-0" />
                        </template>

                        <!-- Textarea Input with Emoji Icon -->
                        <div class="relative flex-1" x-data="{ showEmoji: false }">
<textarea 
    x-model="message"
    id="chat-message-textarea"
    rows="1"
    class="w-full border border-gray-300 text-base rounded-full px-4 py-1 focus:ring-2 focus:ring-green-400 bg-gray-50 pr-10"
    placeholder="Type a message..."
    style="min-height: 40px; max-height: 80px; overflow-y: auto;"
    @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
></textarea>

                            <!-- Emoji icon -->
                            <button type="button" 
                                class="absolute right-2 top-2 text-xl text-green-600"
                                title="Insert Emoji"
                                @click="showEmoji = !showEmoji">
                                😊
                            </button>

                            <!-- Emoji Picker Above the Icon -->
                            <div 
                                x-show="showEmoji" 
                                @click.away="showEmoji = false" 
                                class="absolute bottom-full mb-2 right-0 z-50 bg-white shadow-lg border rounded"
                            >
                                <emoji-picker></emoji-picker>
                            </div>
                        </div>

                        <!-- Like or Send Button -->
                        <template x-if="!message.trim()">
                            <button type="button" 
                                @click="sendLike()" 
                                class="text-green-600 text-2xl px-2 flex-shrink-0 active:scale-225 transition-transform duration-150" 
                                title="Send Like">
                                <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>
                        </template>
                        <template x-if="message.trim()">
                            <button type="submit" 
                                class="ml-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-lg flex items-center justify-center flex-shrink-0" 
                                title="Send">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </template>
    </div>
</div>
{{-- Notification --}}
<div x-data="{ openNotif: false }" class="relative">
    <button title="Notifications" @click="openNotif = !openNotif; if(openNotif) { window.dispatchEvent(new CustomEvent('close-messages')); window.dispatchEvent(new CustomEvent('close-widgets')); } if(!openNotif) return; fetch('{{ route('notifications.read') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})" class="hover:text-green-700 transition relative">
        <i class="bi bi-bell-fill text-2xl"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full px-1 text-xs">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>
        <div 
            x-show="openNotif" 
            @click.away="openNotif = false" 
            x-transition
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border p-4 z-50"
            style="max-height:80vh; overflow-y:auto;"
        >
        <h3 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
            <i class="bi bi-bell-fill"></i> Notifications
        </h3>
        @php
            $today = \Carbon\Carbon::today();
            $todayNotifications = auth()->user()->notifications->filter(function($n) use ($today) {
                return \Carbon\Carbon::parse($n->created_at)->isSameDay($today);
            });
            $earlierNotifications = auth()->user()->notifications->filter(function($n) use ($today) {
                return !\Carbon\Carbon::parse($n->created_at)->isSameDay($today);
            });
        @endphp
    <div>
            <div class="text-xs font-bold text-green-700 mb-1">Today</div>
            <ul class="space-y-2">
                @forelse($todayNotifications as $notification)
                    @if(isset($notification->data['booking_id']))
                        <li class="p-3 bg-gray-50 rounded border-l-4 cursor-pointer
                            @if($notification->read_at) border-gray-300 @else border-green-500 @endif
                            hover:bg-green-100 transition"
                            @click="window.location='{{ route('user.bookings.show', $notification->data['booking_id']) }}'"
                        >
                            <div class="flex items-center gap-2 mb-1">
                                @if(!empty($notification->data['admin_image']))
                                    <img src="{{ asset('storage/' . $notification->data['admin_image']) }}" class="w-6 h-6 rounded-full" alt="Farm Owner">
                                @endif
                                <span class="font-semibold">{{ $notification->data['admin_name'] ?? 'Farm Owner' }}</span>
                                <span class="ml-2 text-xs text-gray-500">Booking Update</span>
                            </div>
                            <div>
                                {{ $notification->data['message'] ?? 'No message.' }}
                                <span class="block text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @elseif(isset($notification->data['order_id']))
                        <li class="p-3 bg-gray-50 rounded border-l-4 cursor-pointer
                            @if($notification->read_at) border-gray-300 @else border-green-500 @endif
                            hover:bg-green-100 transition"
                            @click="window.location='{{ route('user.orders.show', $notification->data['order_id']) }}'"
                        >
                            <div class="flex items-center gap-2 mb-1">
                                @if(!empty($notification->data['admin_image']))
                                    <img src="{{ asset('storage/' . $notification->data['admin_image']) }}" class="w-6 h-6 rounded-full" alt="Farm Owner">
                                @else
                                    <i class="bi bi-person-circle text-xl text-green-700"></i>
                                @endif
                                <span class="font-semibold">{{ $notification->data['admin_name'] ?? 'Farm Owner' }}</span>
                                <span class="ml-2 text-xs text-gray-500">Order Update</span>
                            </div>
                            <div>
                                {{ $notification->data['message'] ?? 'No message.' }}
                                <span class="block text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notification->data['updated_at'] ?? $notification->created_at)->diffForHumans() }}
                            </span>
                            </div>
                        </li>
                    @endif
                @empty
                    <li class="text-gray-400 text-sm">No notifications today.</li>
                @endforelse
            </ul>
            <div class="text-xs font-bold text-green-700 mt-3 mb-1">Earlier</div>
            <ul class="space-y-2">
                @forelse($earlierNotifications as $notification)
                    @if(isset($notification->data['booking_id']))
                        <li class="p-3 bg-gray-50 rounded border-l-4 cursor-pointer
                            @if($notification->read_at) border-gray-300 @else border-green-500 @endif
                            hover:bg-green-100 transition"
                            @click="window.location='{{ route('user.bookings.show', $notification->data['booking_id']) }}'"
                        >
                            <div class="flex items-center gap-2 mb-1">
                                <i class="bi bi-calendar2-check-fill text-xl text-orange-500"></i>
                                <span class="font-semibold">Booking Update</span>
                            </div>
                            <div>
                                {{ $notification->data['message'] ?? 'No message.' }}
                                <span class="block text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @else
                        <li class="p-3 bg-gray-50 rounded border-l-4 cursor-pointer
                            @if($notification->read_at) border-gray-300 @else border-green-500 @endif
                            hover:bg-green-100 transition"
                            @click="window.location='{{ route('user.orders.show', $notification->data['order_id']) }}'"
                        >
                            <div class="flex items-center gap-2 mb-1">
                                @if(!empty($notification->data['admin_image']))
                                    <img src="{{ asset('storage/' . $notification->data['admin_image']) }}" class="w-6 h-6 rounded-full" alt="Farm Owner">
                                @else
                                    <i class="bi bi-person-circle text-xl text-green-700"></i>
                                @endif
                                <span class="font-semibold">{{ $notification->data['admin_name'] ?? 'Farm Owner' }}</span>
                            </div>
                            <div>
                                {{ $notification->data['message'] ?? 'No message.' }}
                                <span class="block text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notification->data['updated_at'] ?? $notification->created_at)->diffForHumans() }}
                            </span>
                            </div>
                        </li>
                    @endif
                @empty
                    <li class="text-gray-400 text-sm">No earlier notifications.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
<!-- Profile Dropdown -->
<div class="relative" x-data="{ dropdown: false }">
    <button @click="dropdown = !dropdown" class="focus:outline-none">
        <img
            src="{{ Auth::check() && Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('agri-profile.png') }}"
            class="w-10 h-10 rounded-full border-2 border-green-300 object-cover hover:scale-105 transition"
            alt="Profile"
        >
    </button>
    <div 
        x-show="dropdown" 
        @click.away="dropdown = false" 
        @mouseleave="dropdown = false"
        x-transition
        class="absolute right-0 mt-2 w-44 bg-white text-sm text-green-900 rounded-md shadow-lg overflow-hidden z-50"
    >
        <a href="{{ route('user.myprofile') }}" class="block px-4 py-2 hover:bg-lime-100 flex items-center gap-2">
            <i class="bi bi-person-circle text-xl text-green-700"></i> My Account
        </a>
        {{-- <a href="{{ route('settings.edit') }}" class="block px-4 py-2 hover:bg-lime-100 flex items-center gap-2">
            <i class="bi bi-gear text-xl text-gray-700"></i> Settings
        </a> --}}
        <a href="{{ route('settings.password.edit') }}" class="block px-4 py-2 hover:bg-lime-100 flex items-center gap-2">
            <i class="bi bi-key text-xl text-yellow-600"></i> Update Password
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-lime-100 text-red-600 flex items-center gap-2">
                <i class="bi bi-box-arrow-right text-xl text-red-600"></i> Logout
            </button>
        </form>
    </div>
</div>
    
    </nav>

    <!-- Main Content -->
    <main class="flex-grow bg-white pt-12">
            @yield('content')
        </div>
    </main>



</body>
</html>