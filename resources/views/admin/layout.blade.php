<!-- filepath: resources/views/admin/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSA Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        /* Hide elements marked with x-cloak until Alpine is initialized */
        [x-cloak] { display: none !important; }
        html, body { height: 100%; }
        .sidebar a:hover { background-color: #2F855A; }
        .sidebar a.active { background-color: #22543D; }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: #22543D; border-radius: 3px; }
        header { box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07); }
        .profile-img { border: 2px solid #22543D; }
        .dropdown-anim { transition: all 0.2s; }
        .dropdown-anim.hidden { opacity: 0; transform: translateY(-10px); pointer-events: none; }
        .dropdown-anim:not(.hidden) { opacity: 1; transform: translateY(0); pointer-events: auto; }
        #sidebar { width: 16rem; transition: width 0.3s; overflow-x: hidden; }
        #sidebar.collapsed { width: 4.5rem; }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; pointer-events: none; display: none; }
        #sidebar .sidebar-label { opacity: 1; width: auto; display: inline; transition: opacity 0.2s, width 0.2s; white-space: nowrap; }
        #sidebar.hovered .sidebar-label { opacity: 1 !important; width: auto !important; display: inline !important; pointer-events: auto !important; }
        .sidebar-header .sidebar-label { transition: opacity 0.2s, width 0.2s; white-space: nowrap; }
        #sidebar.collapsed .sidebar-header .sidebar-label { opacity: 0; width: 0; pointer-events: none; display: none; }
        #mainContent { transition: margin-left 0.3s; margin-left: 16rem; }
        #mainContent.expanded { margin-left: 4.5rem; }
        #mainContent.hovered-expanded { margin-left: 16rem !important; }
        @media (max-width: 1024px) {
            #sidebar { left: -16rem; }
            #sidebar:not(.collapsed) { left: 0; }
            #mainContent, #mainContent.expanded, #mainContent.hovered-expanded { margin-left: 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 h-full flex font-sans">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-gradient-to-b from-green-900 to-green-700 text-white flex flex-col h-screen fixed shadow-lg sidebar overflow-y-auto z-30 transition-all duration-300 group">
        <div class="p-6 text-2xl font-bold text-center bg-green-800 tracking-wide border-b border-green-900 flex items-center justify-center sidebar-header">
            <span class="flex items-center gap-2">
                <i class="bi bi-leaf-fill text-lime-300"></i>
                <span class="sidebar-label">AgriEcom LSA</span>
            </span>
        </div>
        <nav class="flex-1 flex flex-col gap-1 px-2 py-4">
            <a href="{{route('admin.dashboard')}}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="home" class="text-lg"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i data-lucide="box" class="text-lg"></i>
                <span class="sidebar-label">Products</span>
            </a>
            <a href="{{ route('services.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <i data-lucide="graduation-cap" class="text-lg"></i>
                <span class="sidebar-label">Training Services</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i data-lucide="shopping-cart" class="text-lg"></i>
                <span class="sidebar-label">Orders</span>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i data-lucide="calendar" class="text-lg"></i>
                <span class="sidebar-label">Bookings</span>
            </a>
            <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.away="open = false">
                <button type="button" @click="open = !open" class="flex items-center justify-between gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out w-full text-left {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                        <span class="sidebar-label">Reports</span>
                    </div>
                    <i class="bi bi-chevron-down sidebar-label transition-transform" :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Inline dropdown: pushes sidebar items down (matches the image style) -->
                <div x-cloak x-show="open" x-transition class="mt-1 ml-2 bg-green-900 rounded-md py-2 flex flex-col space-y-1">
                    {{-- <a href="{{route('admin.reports.index')}}" class="text-sm text-white px-4 py-2 hover:bg-green-800 transition">Product & Training Services Sale</a> --}}
                    <a href="{{route('admin.reports.productsreport')}}" class="text-sm text-white px-4 py-2 hover:bg-green-800 transition">Products Report</a>
                    <a href="{{route('admin.reports.servicesreport')}}" class="text-sm text-white px-4 py-2 hover:bg-green-800 transition">Training Services Report</a>
                </div>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <i data-lucide="layers" class="text-lg"></i>
                <span class="sidebar-label">Inventory</span>
            </a>
            <a href="{{ route('events.index') }}" class="flex items-center gap-3 py-3 px-3 rounded-lg hover:bg-green-700 transition duration-200 ease-in-out {{ request()->routeIs('events.*') ? 'active' : '' }}">
                <i data-lucide="calendar-days" class="text-lg"></i>
                <span class="sidebar-label">Events</span>
            </a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-3 p-4 mt-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 ease-in-out justify-center">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full">
                <i data-lucide="log-out" class="text-lg"></i>
                <span class="sidebar-label">Logout</span>
            </button>
        </form>
    </aside>

    <!-- Main Content -->
    <div id="mainContent" class="flex-1 flex flex-col min-h-screen transition-all duration-300">
    <!-- Header -->
    <header class="bg-white shadow-md p-6 flex justify-between items-center relative z-30 sticky top-0 left-0 w-full" style="position:sticky;top:0;z-index:30;">
            <div class="flex items-center gap-3">
                <i class="bi bi-speedometer2 text-3xl text-green-700 bg-green-100 rounded-full p-2 shadow-sm"></i>
                    <div>
                    <h1 class="text-2xl font-extrabold text-green-800 tracking-wide leading-tight">LSA</h1>
                    <div class="text-xs text-green-600 font-medium mt-0.5">Welcome, {{ optional(auth()->user()->profile)->farm_owner ?? auth()->user()->name }}!</div>
                </div>
            </div>
            <div class="flex items-center space-x-6 relative">
                <!-- Widgets Button & Dropdown (Alpine.js controlled) -->
                <div class="relative" x-data="{ open: false }">
                    <button class="relative text-green-800 hover:text-green-600 transition" title="Widgets" type="button" @click="open = !open">
                        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 hover:bg-blue-100 transition">
                            <i class="bi bi-grid-fill text-blue-700 text-xl"></i>
                        </span>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-56 bg-white border rounded shadow-lg z-50"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        @click.away="open = false"
                        @mouseleave="open = false"
                    >
                        <div class="p-3 border-b text-green-700 font-bold text-lg flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="mr-2"></i> Widgets
                        </div>
                        <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="box" class="mr-2 text-green-700"></i> <span>Products</span>
                        </a>
                        <a href="{{ route('services.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="settings" class="mr-2 text-green-700"></i> <span>Training Services</span>
                        </a>
                        <a href="{{ route('admin.reports.productsreport') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="file-text" class="mr-2 text-green-700"></i> <span>Products Report</span>
                        </a>
                        <a href="{{ route('admin.reports.servicesreport') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="bar-chart-2" class="mr-2 text-green-700"></i> <span>Training Services Report</span>
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="shopping-cart" class="mr-2 text-green-700"></i> <span>Orders</span>
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="calendar" class="mr-2 text-green-700"></i> <span>Bookings</span>
                        </a>
                        <a href="{{ route('admin.inventory.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="layers" class="mr-2 text-green-700"></i> <span>Inventory</span>
                        </a>
                        <a href="{{ route('events.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                            <i data-lucide="calendar-days" class="mr-2 text-green-700"></i> <span>Events</span>
                        </a>
                        {{-- Users menu removed: admin.users.index route does not exist --}}
                       
                    </div>
                </div>
                <!-- Messages Dropdown -->
                <div class="relative"
                    x-data="{
                        open: false,
                        userId: null,
                        userName: '',
                        messages: [],
                        message: '',
                        poll: null,
                        minimized: false,
                        imageFile: null,
                        searchChat: '',
                        chatTab: 'all',
                        chats: {{ Js::from(
                            \App\Models\Message::with(['sender', 'receiver'])
                                ->where(function($q) {
                                    $userId = auth()->id();
                                    $q->where('receiver_id', $userId)
                                      ->orWhere('sender_id', $userId);
                                })
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->unique(function($item) {
                                    $userId = auth()->id();
                                    return $item->sender_id == $userId ? $item->receiver_id : $item->sender_id;
                                })
                                ->take(20)
                                ->values()
                        ) }},
                        get filteredChats() {
                            let filtered = this.chats;
                            // Search by name
                            if (this.searchChat.trim() !== '') {
                                filtered = filtered.filter(chat =>
                                    (chat.sender_id == {{ auth()->id() }} ? chat.receiver.name : chat.sender.name)
                                        .toLowerCase().includes(this.searchChat.toLowerCase())
                                );
                            }
                            // Unread filter
                            if (this.chatTab === 'unread') {
                                filtered = filtered.filter(chat =>
                                    chat.receiver_id == {{ auth()->id() }} && chat.is_read == 0
                                );
                            }
                            return filtered;
                        },
                        startUnreadPolling() {
                            setInterval(() => {
                                fetch('/admin/messages/unread-count')
                                    .then(res => res.json())
                                    .then(data => { this.unreadCount = data.count });
                            }, 3000); // every 3 seconds
                        },
                        openChat(userId, userName) {
                            this.open = true;
                            this.userId = userId;
                            this.userName = userName;
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
                            if(this.poll) clearInterval(this.poll);
                        },
                        fetchMessages() {
                            fetch('/admin/messages/fetch/' + this.userId,)
                                .then(res => res.json())
                                .then(data => {
                                    this.messages = data;
                                    this.$nextTick(() => {
                                        let box = document.getElementById('admin-chat-messages');
                                        if(box) box.scrollTop = box.scrollHeight;
                                    });
                                });
                        },
                        handleImageUpload(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.imageFile = file;
                            }
                        },
                        sendLike() {
                            this.message = '👍';
                            this.sendMessage();
                        },
                        sendMessage() {
                            if (this.message.trim() !== '' || this.imageFile) {
                                const formData = new FormData();
                                formData.append('message', this.message);
                                if (this.imageFile) {
                                    formData.append('image', this.imageFile);
                                }
                                fetch('/admin/messages/' + this.userId, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                }).then(() => {
                                    this.message = '';
                                    this.imageFile = null;
                                    this.fetchMessages();
                                });
                            }
                        }
                    }">
                    <button onclick="toggleDropdown('messagesDropdown')" class="relative text-green-800 hover:text-green-600 transition">
                        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-50 hover:bg-green-100 transition">
                            <i class="bi bi-chat-dots-fill text-green-700 text-xl"></i>
                            @php
                                $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', 0)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 font-bold shadow">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </span>
                    </button>
<div id="messagesDropdown" class="dropdown-anim hidden absolute right-0 left-auto mt-2 w-[28rem] bg-white border rounded-2xl shadow-2xl z-50 max-h-[40rem] overflow-auto p-0">
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
        <template x-for="msg in filteredChats" :key="msg.id">
            <a href="#"
               @click.prevent="openChat(msg.sender_id == {{ auth()->id() }} ? msg.receiver.id : msg.sender.id, msg.sender_id == {{ auth()->id() }} ? msg.receiver.name : msg.sender.name)"
               class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition">
                <div class="relative">
                    <img :src="'/storage/' + ((msg.sender_id == {{ auth()->id() }} ? msg.receiver.photo : msg.sender.photo) || 'default.png')" alt="Profile" class="w-11 h-11 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                    <template x-if="msg.is_read == 0 && msg.receiver_id == {{ auth()->id() }}">
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <div :class="msg.is_read == 0 && msg.receiver_id == {{ auth()->id() }} ? 'font-bold text-green-800' : 'font-normal'" class="truncate text-base" x-text="msg.sender_id == {{ auth()->id() }} ? msg.receiver.name : msg.sender.name"></div>
                    <div :class="msg.is_read == 0 && msg.receiver_id == {{ auth()->id() }} ? 'font-semibold text-green-700' : 'font-normal text-gray-600'" class="truncate text-xs mt-0.5" x-text="msg.message.split(' ').slice(0,9).join(' ') + (msg.message.split(' ').length > 9 ? '...' : '')"></div>
                </div>
                <div class="text-xs text-gray-400 whitespace-nowrap ml-2" x-text="window.dayjs ? window.dayjs(msg.created_at).fromNow() : msg.created_at"></div>
            </a>
        </template>
        <div x-show="filteredChats.length == 0" class="px-4 py-8 text-gray-400 text-sm text-center">No new messages</div>
    </div>
    <div class="text-center mt-2 border-t bg-gray-50 rounded-b-2xl">
        <a href="{{ route('admin.messages') }}" class="text-green-600 hover:underline text-sm py-3 block font-semibold transition">View all messages</a>
    </div>
</div>
                    <!-- Admin Chatbox Modal -->
                    <div 
                        x-show="open"
                        x-transition
                        :style="minimized ? 'width:4rem;height:4rem;bottom:0;right:5rem;padding:0;overflow:hidden;' : 'width:22rem;height:26rem;bottom:0;right:5rem;'"
                        class="fixed bg-white border border-gray-300 rounded-full shadow-lg z-50"
                        id="admin-chatbox"
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
                            <div class="flex flex-col h-[26rem] w-[22rem] rounded-xl overflow-hidden bg-white">
                                <!-- Header -->
                                <div class="flex items-center justify-between bg-green-600 text-white px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <img 
                                            :src="messages.length && messages[0].sender && messages[0].sender.photo 
                                                ? '/storage' + messages[0].sender.photo 
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
                                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="admin-chat-messages">
                                    <template x-for="msg in messages" :key="msg.id">
                                        <div :class="msg.sender_id == {{ auth()->id() }} ? 'text-right' : 'text-left'" class="my-2">
                                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'inline-block bg-green-100 text-green-800' : 'inline-block bg-gray-100 text-gray-800'"
                                                class="rounded-lg px-3 py-2 max-w-xs"
                                                style="word-break:break-word;">
                                                <template x-if="msg.image">
                                                    <img 
                                                        :src="'/storage/' + msg.image" 
                                                        class="max-w-[150px] rounded mb-2"
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
                                <!-- Input Area (fixed at the bottom) -->
                                <form @submit.prevent="sendMessage()" class="border-t px-2 py-3 bg-white">
                                    <div class="flex items-end gap-2 w-full">
                                        <!-- Image Upload -->
                                        <label class="cursor-pointer flex items-center">
                                            <i class="bi bi-image text-2xl text-green-700"></i>
                                            <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                                        </label>
                                        <!-- Image Preview -->
                                        <template x-if="imageFile">
                                            <img :src="URL.createObjectURL(imageFile)" class="max-w-[70px] max-h-[70px] rounded mr-2 border" />
                                        </template>
                                        <!-- Textarea Input (taller, Messenger style) -->
                                        <textarea 
                                            x-model="message"
                                            rows="1"
                                            class="flex-1 border border-gray-300 focus:ring-2 focus:ring-green-400 text-base resize-none rounded px-3 py-2 bg-gray-50"
                                            placeholder="Type a message..."
                                            style="min-height: 30px; max-height: 90px; overflow-y:auto;"
                                        ></textarea>
                                        <!-- Like Button -->
                                        <button type="button" @click="sendLike()" class="text-green-500 text-2xl px-2" title="Send Like">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                        <!-- Send Button -->
                                        <button type="submit" class="ml-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-lg flex items-center justify-center">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
     <!-- Notifications Dropdown -->
                <div class="relative">
                    <button onclick="toggleDropdown('notificationsDropdown')" class="relative text-green-800 hover:text-green-600 transition">
                        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-50 hover:bg-yellow-100 transition">
                            <i class="bi bi-bell-fill text-yellow-600 text-xl"></i>
                            @php $notifCount = auth()->user()->unreadNotifications->count(); @endphp
                            @if($notifCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 font-bold shadow">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </span>
                    </button>
                    <div id="notificationsDropdown" class="dropdown-anim hidden absolute right-0 mt-4 w-[28rem] bg-white border rounded-2xl shadow-2xl z-50 max-h-[30rem] overflow-auto p-0" style="right:-2px;"
                        x-data="{ notifTab: 'all' }">
                        <div class="p-4 border-b bg-gradient-to-r from-green-50 to-white rounded-t-2xl">
                            <div class="text-lg font-bold text-green-800 mb-2 flex items-center gap-2">
                                <i class="bi bi-bell text-green-600"></i> Notifications
                                <span class="ml-auto text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">{{ $notifCount }} unread</span>
                            </div>
                            <div class="flex gap-2 mt-1">
                                <button type="button" @click="notifTab = 'all'"
                                    :class="notifTab === 'all' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition">All</button>
                                <button type="button" @click="notifTab = 'unread'"
                                    :class="notifTab === 'unread' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition">Unread</button>
                            </div>
                        </div>
                        <div class="divide-y">
                        <template x-if="notifTab === 'all'">
                            <div>
                            @php $allNotifications = auth()->user()->notifications; @endphp
                            @forelse($allNotifications as $notification)
                                @php
                                    $type = $notification->data['type'] ?? null;
                                    $userId = $notification->data['follower_id'] ?? $notification->data['liker_id'] ?? $notification->data['user_id'] ?? null;
                                    $user = $userId ? \App\Models\User::find($userId) : null;
                                    $photo = $user && $user->profile && $user->profile->profile_photo ? $user->profile->profile_photo : null;
                                @endphp
                                @php
                                    $notifUserPhoto = $notification->data['user_photo'] ?? null;
                                    $notifUserId = $notification->data['user_id'] ?? $notification->data['follower_id'] ?? $notification->data['liker_id'] ?? null;
                                    if (!$notifUserPhoto && $notifUserId) {
                                        $notifUser = \App\Models\User::find($notifUserId);
                                        // Use 'photo' field from users table if available
                                        $notifUserPhoto = $notifUser && $notifUser->photo ? $notifUser->photo : null;
                                    }
                                @endphp
                                @if(isset($notification->data['booking_id']))
                                                            <a href="{{ route('admin.bookings.index') }}#booking-{{ $notification->data['booking_id'] }}"
                                                                class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b {{ is_null($notification->read_at) ? 'bg-green-100 font-bold' : '' }} notification-link"
                                                                data-id="{{ $notification->id }}">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $notifUserPhoto ? asset('storage/' . ltrim($notifUserPhoto, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['user_name'] ?? 'User' }} booked <span class="text-green-700">{{ $notification->data['service_name'] ?? 'Service' }}</span></div>
                                            <div class="text-xs text-gray-500">Booking Date: {{ $notification->data['booking_start'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">Reference #: BK-{{ str_pad($notification->data['booking_id'], 6, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </a>
                                @elseif(isset($notification->data['order_id']))
                                                            <a href="{{ route('admin.orders.index', $notification->data['order_id']) }}"
                                                                class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b {{ is_null($notification->read_at) ? 'bg-green-100 font-bold' : '' }} notification-link"
                                                                data-id="{{ $notification->id }}">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ ($notification->data['photo'] ?? null) ? asset('storage/' . ltrim($notification->data['photo'], '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['message'] ?? ($notification->data['user_name'] ?? 'User') . ' placed an order' }}</div>
                                            <div class="text-xs text-gray-500">Order #: OR-{{ str_pad($notification->data['order_id'], 6, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @elseif($type === 'follow' || $type === 'like')
                                    <a href="{{ route('profiles.show', $userId) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm group transition border-b {{ is_null($notification->read_at) ? 'bg-green-100 font-bold' : '' }} notification-link" data-id="{{ $notification->id }}">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $notifUserPhoto ? asset('storage/' . ltrim($notifUserPhoto, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['message'] }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @else
                                                            <a href="{{ isset($notification->data['order_id']) ? route('admin.orders.index', $notification->data['order_id']) : '#' }}"
                                                                class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b {{ is_null($notification->read_at) ? 'bg-green-100 font-bold' : '' }} notification-link" data-id="{{ $notification->id }}">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.notification-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const notifId = this.getAttribute('data-id');
                if(this.classList.contains('bg-green-100')) {
                    fetch('/admin/notifications/mark-as-read/' + notifId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if(res.ok) {
                            this.classList.remove('bg-green-100', 'font-bold');
                            // Update count in bell
                            let countElem = document.querySelector('.bi-bell-fill').parentElement.querySelector('span.absolute');
                            if(countElem) {
                                let count = parseInt(countElem.textContent.trim());
                                if(count > 1) countElem.textContent = count - 1;
                                else countElem.remove();
                            }
                        }
                    });
                }
            });
        });
    });
    </script>
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $notifUserPhoto ? asset('storage/' . ltrim($notifUserPhoto, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">Notification</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @endif
                            @empty
                                <div class="px-4 py-10 text-gray-400 text-sm text-center">No notifications</div>
                            @endforelse
                            </div>
                        </template>
                        <template x-if="notifTab === 'unread'">
                            <div>
                            @php $unreadNotifications = auth()->user()->unreadNotifications; @endphp
                            @forelse($unreadNotifications as $notification)
                                @php
                                    $type = $notification->data['type'] ?? null;
                                    $userId = $notification->data['follower_id'] ?? $notification->data['liker_id'] ?? $notification->data['user_id'] ?? null;
                                    $user = $userId ? \App\Models\User::find($userId) : null;
                                    $photo = $user && $user->profile && $user->profile->profile_photo ? $user->profile->profile_photo : null;
                                @endphp
                                @if(isset($notification->data['booking_id']))
                                    <a href=""
                                       class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $notification->data['photo'] ?? null ? asset('storage/' . ltrim($notification->data['photo'], '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['user_name'] ?? 'User' }} booked <span class="text-green-700">{{ $notification->data['service_name'] ?? 'Service' }}</span></div>
                                            <div class="text-xs text-gray-500">Booking Date: {{ $notification->data['booking_start'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">Reference #: BK-{{ str_pad($notification->data['booking_id'], 6, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </a>
                                @elseif(isset($notification->data['order_id']))
                                    <a href="{{ route('admin.orders.index', $notification->data['order_id']) }}"
                                       class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $notification->data['photo'] ?? null ? asset('storage/' . ltrim($notification->data['photo'], '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['user_name'] ?? 'User' }} placed an order</div>
                                            <div class="text-xs text-gray-500">{{ $notification->data['message'] ?? '' }}</div>
                                            <div class="text-xs text-gray-500">Order #: OR-{{ str_pad($notification->data['order_id'], 6, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @elseif($type === 'follow' || $type === 'like')
                                    <a href="{{ route('profiles.show', $userId) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm group transition border-b">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $photo ? asset('storage/' . ltrim($photo, '/')) : asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">{{ $notification->data['message'] }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ isset($notification->data['order_id']) ? route('admin.orders.index', $notification->data['order_id']) : '#' }}"
                                       class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-sm cursor-pointer group transition border-b">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ asset('/storage/default.png') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-green-100 group-hover:border-green-400 transition">
                                            <i class="bi bi-dot text-green-500 text-xl absolute -top-1 -right-1"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-green-800 truncate">Notification</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @endif
                            @empty
                                <div class="px-4 py-10 text-gray-400 text-sm text-center">No new notifications</div>
                            @endforelse
                            </div>
                        </template>
                        </div>
                        <div class="text-center mt-2 border-t bg-gray-50 rounded-b-2xl">
                            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                @csrf
                                <button type="submit" class="text-green-600 hover:underline text-sm py-3 block font-semibold transition w-full">Mark all as read</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button onclick="toggleDropdown('profileDropdown')" class="focus:outline-none">
                        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-50 hover:bg-purple-100 border-2 border-purple-200 shadow transition overflow-hidden">
                            @php
                                // Prefer profile photo stored on the related profile model; be null-safe
                                $profilePhoto = optional(auth()->user()->profile)->profile_photo ?? null;
                            @endphp
                            @if($profilePhoto)
                                <img src="{{ asset('storage/' . ltrim($profilePhoto, '/')) }}" alt="Profile" class="w-full h-full object-cover rounded-full">
                            @else
                                <i class="bi bi-person-circle text-purple-600 text-2xl"></i>
                            @endif
                        </span>
                    </button>
                    <div id="profileDropdown" class="dropdown-anim hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                        <div class="p-4 border-b bg-gradient-to-r from-purple-50 to-white rounded-t-2xl flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-purple-200 mb-2 shadow">
                                @php $profilePhoto = auth()->user()->profile->profile_photo ?? null; @endphp
                                @if($profilePhoto)
                                    <img src="{{ asset('storage/' . ltrim($profilePhoto, '/')) }}" alt="Profile" class="w-full h-full object-cover rounded-full">
                                @else
                                    <i class="bi bi-person-circle text-purple-600 text-4xl flex items-center justify-center w-full h-full"></i>
                                @endif
                            </div>
                            <div class="font-bold text-purple-900 text-base text-center w-full truncate">{{ optional(auth()->user()->profile)->farm_owner ?? auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 text-center w-full truncate">{{ optional(auth()->user()->profile)->email ?? auth()->user()->email }}</div>
                        </div>
                        <div class="flex flex-col py-2">
                            <a href="{{route('profiles.show')}}" class="px-4 py-2 hover:bg-purple-50 text-purple-800 font-medium transition rounded-t">My Profile</a>
                            <a href="{{ route('admin.profile.password.edit') }}" class="px-4 py-2 hover:bg-purple-50 text-purple-800 font-medium transition">Update Password</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-medium transition rounded-b">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-8 bg-gray-50">
                @yield('content')
            </main>
            <footer class="bg-white text-center p-4 shadow-md text-green-700">
                &copy; 2025 AgriEcom. All rights reserved.
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        // Sidebar auto-collapse/expand on hover only
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        document.querySelectorAll('.sidebar-label').forEach(label => label.classList.add('hidden'));
        sidebar.addEventListener('mouseenter', function() {
            sidebar.classList.add('hovered');
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            mainContent.classList.add('hovered-expanded');
            document.querySelectorAll('.sidebar-label').forEach(label => label.classList.remove('hidden'));
        });
        sidebar.addEventListener('mouseleave', function() {
            sidebar.classList.remove('hovered');
            sidebar.classList.add('collapsed');
            mainContent.classList.remove('hovered-expanded');
            mainContent.classList.add('expanded');
            document.querySelectorAll('.sidebar-label').forEach(label => label.classList.add('hidden'));
        });
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            document.querySelectorAll('.dropdown-anim').forEach(el => {
                if (el !== dropdown) el.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }
        lucide.createIcons();
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-anim').forEach(el => {
                if (!el.contains(e.target) && !e.target.closest('button[onclick^="toggleDropdown"]')) {
                    el.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>