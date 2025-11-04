
@extends('user.layout')

@section('content')
<div class="w-screen h-screen px-4 pb-4 pt-0 overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-[4fr_2fr] gap-10 h-full w-full">
        <!-- Left column removed: Products & Training moved to right sidebar as requested -->

        <!-- Center: Events -->
    <main class="overflow-y-auto h-full">
            <div class="bg-white rounded-2x2 shadow-lg p-8 h-full flex flex-col">
                <h3 class="font-bold text-green-700 flex items-center gap-2 text-2xl mb-6">
                    <i class="bi bi-calendar-event text-green-600 text-2xl"></i> Latest Events
                </h3>
                @php($events = \App\Models\Event::latest()->take(20)->get())
                @php($grouped = $events->groupBy('created_by'))
                @forelse($grouped as $adminId => $adminEvents)
                    @php($admin = \App\Models\User::find($adminId))
                    @foreach($adminEvents as $event)
                        <div class="mb-8 bg-green-50 rounded-xl p-6 shadow hover:shadow-md transition flex flex-col gap-3">
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $admin && $admin->profile && $admin->profile->profile_photo ? asset('storage/' . ltrim($admin->profile->profile_photo, '/')) : asset('/storage/default.png') }}" class="w-14 h-14 rounded-full object-cover border border-green-200" alt="Admin Photo">
                                <span class="font-semibold text-green-700 text-lg">{{ $admin ? $admin->name : 'Admin' }}</span>
                            </div>
                            <span class="text-xs text-gray-500 mb-1">{{ $event->created_at->format('F j, Y g:i A') }}</span>
                            <div class="text-lg mb-1" x-data="{ expanded: false }">
                                <span x-show="!expanded">
                                    {{ Str::words($event->description, 15, '...') }}
                                    <button @click="expanded = true" class="text-green-600 hover:underline ml-2 text-xs">See More</button>
                                </span>
                                <span x-show="expanded">
                                    {{ $event->description }}
                                    <button @click="expanded = false" class="text-green-600 hover:underline ml-2 text-sm">See Less</button>
                                </span>
                            </div>
                            @if($event->images && $event->images->count())
                                <div class="grid grid-cols-{{ min(3, $event->images->count()) }} gap-2 mb-2">
                                    @foreach($event->images->take(6) as $img)
                                        <div class="aspect-w-1 aspect-h-1">
                                            @if($img->type === 'video')
                                                <video src="{{ asset('storage/' . $img->image_path) }}" class="object-cover w-full h-full rounded border border-green-200" controls muted></video>
                                            @else
                                                <img src="{{ asset('storage/' . $img->image_path) }}" class="object-cover w-full h-full rounded border border-green-200" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @empty
                    <div class="text-gray-400 text-sm text-center">No events yet.</div>
                @endforelse
            </div>
        </main>

    <!-- Right: Products, Training Services, Top Likes & Followers -->
    <aside class="flex flex-col gap-8 overflow-y-auto h-full">
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-4">
                <h3 class="font-bold text-green-700 flex items-center gap-2 text-lg mb-2">
                    <i class="bi bi-basket-fill text-green-600 text-xl"></i> Products
                </h3>
                <div class="flex flex-col gap-3">
                    @forelse($randomProducts as $product)
                        <a href="{{ route('product.show', $product->id) }}" class="flex items-center gap-3 bg-green-50 hover:bg-green-100 rounded-xl p-3 transition group">
                            <div class="flex-shrink-0">
                                <img src="{{ $product->image ? asset('storage/' . ltrim($product->image, '/')) : asset('/storage/default-product.png') }}" alt="Product Image" class="w-10 h-10 rounded object-cover border border-green-200 group-hover:scale-105 transition">
                            </div>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-800">{{ $product->product_name ?? $product->name }}</span>
                                <span class="text-green-700 font-semibold text-sm">₱{{ number_format($product->price, 2) }} <span class="text-gray-500 font-normal">/ {{ $product->unit ?? 'unit' }}</span></span>
                            </div>
                        </a>
                    @empty
                        <div class="text-gray-400 text-sm text-center">No products yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-4">
                <h3 class="font-bold text-green-700 flex items-center gap-2 text-lg mb-2">
                    <i class="bi bi-gear-wide-connected text-green-600 text-xl"></i> Training Services
                </h3>
                <div class="flex flex-col gap-3">
                    @forelse($services->take(3) as $service)
                        <a href="{{ route('user.services.show', $service->id) }}" class="flex items-center gap-3 bg-green-50 hover:bg-green-100 rounded-xl p-3 transition group">
                            <div class="flex-shrink-0">
                                <img src="{{ $service->images ? asset('storage/' . ltrim($service->images, '/')) : asset('/storage/default-service.png') }}" alt="Service Image" class="w-10 h-10 rounded object-cover border border-green-200 group-hover:scale-105 transition">
                            </div>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-800">{{ $service->service_name }}</span>
                                <span class="text-green-700 font-semibold text-sm">₱{{ number_format($service->price, 2) }} <span class="text-gray-500 font-normal">/ {{ $service->unit ?? 'unit' }}</span></span>
                            </div>
                        </a>
                    @empty
                        <div class="text-gray-400 text-sm text-center">No services yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-4">
                <h3 class="font-bold text-green-700 flex items-center gap-2 text-lg mb-2 justify-center">
                    <i class="bi bi-hand-thumbs-up-fill text-green-600 text-xl"></i> Top Likes
                </h3>
                <div class="flex flex-col gap-3">
                    @php($topLikedUsers = \App\Models\User::withCount('profileLikes')->where('role', 'admin')->orderByDesc('profile_likes_count')->take(3)->get())
                    @forelse($topLikedUsers as $user)
                        <a href="{{ url('/user/profiles/' . $user->id) }}" class="flex items-center gap-3 bg-green-50 hover:bg-green-100 rounded-xl p-3 transition group">
                            <img src="{{ $user->profile && $user->profile->profile_photo ? asset('storage/' . ltrim($user->profile->profile_photo, '/')) : asset('/storage/default.png') }}" alt="Admin Photo" class="w-10 h-10 rounded-full object-cover border border-green-200 group-hover:scale-105 transition">
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </a>
                    @empty
                        <div class="text-gray-400 text-sm text-center">No likes yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col gap-4">
                <h3 class="font-bold text-green-700 flex items-center gap-2 text-lg mb-2 justify-center">
                    <i class="bi bi-people-fill text-green-600 text-xl"></i> Top Followers
                </h3>
                <div class="flex flex-col gap-3">
                    @php($topFollowers = \App\Models\User::withCount('profileFollowers')->where('role', 'admin')->orderByDesc('profile_followers_count')->take(3)->get())
                    @forelse($topFollowers as $user)
                        <a href="{{ url('/user/profiles/' . $user->id) }}" class="flex items-center gap-3 bg-green-50 hover:bg-green-100 rounded-xl p-3 transition group">
                            <img src="{{ $user->profile && $user->profile->profile_photo ? asset('storage/' . ltrim($user->profile->profile_photo, '/')) : asset('/storage/default.png') }}" alt="Admin Photo" class="w-10 h-10 rounded-full object-cover border border-green-200 group-hover:scale-105 transition">
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </a>
                    @empty
                        <div class="text-gray-400 text-sm text-center">No followers yet.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div><!-- Chatbot Button -->
<button id="chatbot-btn" class="fixed bottom-6 right-6 bg-green-700 text-white p-4 rounded-full shadow-lg z-50">
    <i class="bi bi-robot text-2xl"></i>
</button>
<!-- Chatbot Window -->
<div id="chatbot-window" class="hidden fixed bottom-0 right-4 w-80 bg-white border rounded-lg shadow-lg z-50 flex flex-col" style="height: 400px;">    <div class="bg-green-700 text-white px-4 py-2 rounded-t-lg flex items-center justify-between">
        <span><i class="bi bi-robot"></i> AgriEcom AI Chat</span>
        <button onclick="document.getElementById('chatbot-window').classList.add('hidden')" class="text-white">&times;</button>
    </div>
    <div id="chatbot-messages" class="flex-1 overflow-y-auto p-3 space-y-2 text-sm"></div>
    <form id="chatbot-form" class="flex border-t">
        <input type="text" id="chatbot-input" class="flex-1 px-3 py-2 focus:outline-none" placeholder="Type your message..." autocomplete="off">
        <button type="submit" class="bg-green-700 text-white px-4">Send</button>
    </form>
</div>  

  @stack('scripts')
   <script>
const chatbotBtn = document.getElementById('chatbot-btn');
const chatbotWindow = document.getElementById('chatbot-window');
const chatbotForm = document.getElementById('chatbot-form');
const chatbotInput = document.getElementById('chatbot-input');
const chatbotMessages = document.getElementById('chatbot-messages');

function saveHistory() {
    localStorage.setItem('chatbotHistory', chatbotMessages.innerHTML);
}
function loadHistory() {
    chatbotMessages.innerHTML = localStorage.getItem('chatbotHistory') || '';
}

chatbotBtn.onclick = function() {
    chatbotWindow.classList.toggle('hidden');
    if (!chatbotWindow.classList.contains('hidden')) {
        loadHistory();
        setTimeout(() => chatbotInput.focus(), 200);
    }
};

chatbotForm.onsubmit = async function(e) {
    e.preventDefault();
    const userMsg = chatbotInput.value.trim();
    if (!userMsg) return;
    appendMessage(userMsg, 'right');
    chatbotInput.value = '';
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

    // Loading indicator
    const loadingId = 'loading-' + Date.now();
    appendMessage('<span class="animate-pulse">AgriEcom AI is typing...</span>', 'left', loadingId);

    try {
        const res = await fetch("{{ route('chatbot.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: userMsg })
        });
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        removeMessage(loadingId);
        appendMessage(data.reply, 'left');
    } catch (err) {
        removeMessage(loadingId);
        appendMessage('<span class="text-red-600">Sorry, something went wrong. Please try again.</span>', 'left');
    }
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
};

function appendMessage(msg, side, id = null) {
    const div = document.createElement('div');
    div.className = side === 'right' ? 'text-right' : 'text-left';
    div.innerHTML = `<span class="inline-block ${side === 'right' ? 'bg-green-100' : 'bg-gray-100'} px-2 py-1 rounded mb-1">${msg}</span>`;
    if (id) div.id = id;
    chatbotMessages.appendChild(div);
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    saveHistory();
}
function removeMessage(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    saveHistory();
}
window.onload = loadHistory;
</script>
<script src="//unpkg.com/alpinejs" defer></script>

<style>
/* Custom scrollbar for all scrollable containers */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
    background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #a7f3d0; /* Tailwind green-200 */
    border-radius: 6px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #34d399; /* Tailwind green-400 */
}
.overflow-y-auto::-webkit-scrollbar-corner {
    background: transparent;
}
/* For Firefox */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #a7f3d0 transparent;
}
</style>
@endsection
