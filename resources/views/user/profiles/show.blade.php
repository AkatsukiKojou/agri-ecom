@extends('user.layout')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var footer = document.querySelector('footer');
    if (footer) footer.style.display = 'none';
});
</script>
@endpush

@push('styles')
<style>
footer { display: none !important; }
/* Service card adjustments to prevent overlapping */
.service-desc {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* show up to 2 lines for a more compact card */
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.service-card {
    box-sizing: border-box;
    height: 11.5rem; /* smaller fixed card height */
    min-height: 11.5rem;
    overflow: hidden;
    display: flex;
    align-items: stretch;
    transition: transform 120ms ease, box-shadow 120ms ease;
    transform-origin: center center;
}

/* When hovered, lift above siblings instead of pushing them */
.service-card:hover {
    transform: scale(1.03);
    z-index: 30;
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.12);
}

/* Ensure image container doesn't grow and keeps the scaled image clipped */
.service-card > div:first-child {
    flex: 0 0 auto;
}
.service-card img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Make the content column hide overflow so the card stays compact */
.service-card .flex-1 {
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-8 bg-gradient-to-br from-green-50 to-white rounded-3xl shadow-2xl border border-green-200 overflow-hidden">
<br>
    {{-- Banner --}}
    @if($profile->farm_photo)
        <div class="relative">
            <img src="{{ asset('storage/' . $profile->farm_photo) }}" alt="Farm Banner" class="w-full h-72 object-cover rounded-t-3xl">
            <div class="absolute inset-0 bg-gradient-to-t from-green-900/80 to-transparent rounded-t-3xl"></div>
            <!-- Farm Name Overlay -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 bg-white/90 px-10 py-4 rounded-xl shadow-lg text-center">
                <h1 class="text-4xl font-extrabold text-green-800 tracking-tight drop-shadow">{{ $profile->farm_name }}</h1>
                <p class="text-green-600 text-lg mt-2 font-semibold">Owned by: {{ $profile->farm_owner }}</p>
            </div>
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="relative px-4 md:px-8 pb-8 -mt-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-44 h-44 rounded-full overflow-hidden border-4 border-green-200 shadow-xl bg-white flex items-center justify-center -mt-20 md:mt-0">
                <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 text-center md:text-left mt-4 md:mt-0">
                <div class="flex gap-8 mt-2 justify-center md:justify-start text-sm">
                    <div class="bg-white/90 px-6 py-3 rounded-xl shadow flex flex-col items-center">
                        <span class="font-bold text-green-700 text-2xl">{{ $totalProducts }}</span>
                        <span class="text-xs text-gray-600 uppercase">Products</span>
                    </div>
                    <div class="bg-white/90 px-6 py-3 rounded-xl shadow flex flex-col items-center">
                        <span class="font-bold text-green-700 text-2xl">{{ $totalServices }}</span>
                        <span class="text-xs text-gray-600 uppercase">Services</span>
                    </div>
                </div>
                <!-- Social Stats Row -->
                <div class="flex flex-wrap gap-8 mt-8 justify-center md:justify-start items-center">
                    <!-- Followers -->
                    <div class="flex flex-col items-center">
                        <span class="text-green-700 font-bold text-xl followers-count">{{ $followersCount }}</span>
                        <span class="text-xs text-gray-600">Followers</span>
                    </div>
                    <!-- Rating -->
                    <div class="flex flex-col items-center">
                        <span class="text-yellow-500 font-bold text-xl flex items-center gap-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.382 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.382-2.455a1 1 0 00-1.175 0l-3.382 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z"/></svg>
                            {{ $profile->average_rating ?? '0.0' }}
                        </span>
                        <span class="text-xs text-gray-600">Rating</span>
                    </div>
                    <!-- Likes -->
                    <div class="flex flex-col items-center">
                        <span class="text-pink-600 font-bold text-xl likes-count">{{ $likesCount }}</span>
                        <span class="text-xs text-gray-600">Likes</span>
                    </div>
                </div>
                <!-- Follow and Like Buttons -->
                <div class="flex gap-6 mt-8 justify-center md:justify-start">
                    @if($alreadyFollowed)
                        <form method="POST" action="{{ route('user.profiles.unfollow', $profile->id) }}" class="inline unfollow-form" data-id="{{ $profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Unfollow
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.follow', $profile->id) }}" class="inline follow-form" data-id="{{ $profile->id }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Follow
                            </button>
                        </form>
                    @endif

                    @if($alreadyLiked)
                        <form method="POST" action="{{ route('user.profiles.unlike', $profile->id) }}" class="inline unlike-form" data-id="{{ $profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-pink-400 hover:bg-pink-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                Unlike
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.like', $profile->id) }}" class="inline like-form" data-id="{{ $profile->id }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                Like
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <br>
        <div>
                    <p><strong>Phone:</strong> {{ $profile->phone_number }}</p>
                    <p><strong>Email:</strong> {{ $profile->email }}</p>
                    <p><strong>Address:</strong> {{ $profile->address}} {{ $profile->barangay }} {{ $profile->city }} {{ $profile->province }}, {{ $profile->region }}</p>
                </div>
                <!-- Chat Now Button (right under address, right-aligned, green) -->
       <div
                    x-data="{
                        openChat: false,
                        minimized: false,
                        message: '',
                        imageFile: null,
                        messages: [],
                        poll: null,
                        adminId: '{{ $profile->admin_id }}',
                        adminName: '{{ $profile->admin->profile->farm_owner }}',
                        adminPhoto: '{{ (!empty($profile->admin) && !empty($profile->admin->profile) && !empty($profile->admin->profile->profile_photo)) ? asset('storage/' . $profile->admin->profile->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($profile->admin->name ?? $profile->farm_owner ?? 'User') }}',
                        fetchMessages() {
                            fetch(`/messages/${this.adminId}`)
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
                                fetch(`/messages/${this.adminId}`, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: formData
                                }).then(() => {
                                    this.message = '';
                                    this.imageFile = null;
                                    this.fetchMessages();
                                });
                            }
                        },
                        openBox() {
                            this.openChat = true;
                            this.minimized = false;
                            this.fetchMessages();
                            if(this.poll) clearInterval(this.poll);
                            this.poll = setInterval(() => this.fetchMessages(), 5000);
                        },
                        closeBox() {
                            this.openChat = false;
                            this.minimized = false;
                            if(this.poll) clearInterval(this.poll);
                        }
                    }"
                >
                    <div class="flex justify-end">
                        <button
                            @click="openBox()"
                            class="flex items-center gap-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-xl shadow"
                            type="button"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.657 0-3.204-.406-4.5-1.107L3 21l1.107-4.5C3.406 15.204 3 13.657 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Chat Now</span>
                        </button>
                    </div>
                    <div
                        x-show="openChat"
                        x-transition
                        :style="minimized 
                            ? 'width:3rem;height:3rem;bottom:1rem;right:5rem;padding:2;overflow:hidden;' 
                            : 'width:22rem;bottom:0;right:5rem;'"
                        class="fixed bg-white border border-gray-300 rounded-   full shadow-lg z-50"
                        style="display:none;"
                        @click.away="minimized ? minimized = false : closeBox()"
                    >
                        <!-- Minimized State -->
                        <template x-if="minimized">
                            <div class="flex items-center justify-center h-full w-full cursor-pointer" @click="minimized = false">
                                <img :src="adminPhoto" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-yellow-500">
                            </div>
                        </template>
                        <!-- Expanded State -->
                        <template x-if="!minimized">
                            <div class="flex flex-col h-[26rem] w-[21rem] rounded-xl overflow-hidden bg-white">
                                <!-- Header -->
                                <div class="flex items-center justify-between bg-green-600 text-white px-4 py-2 relative">
                                    <div class="flex items-center gap-2">
                                        <img :src="adminPhoto" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-white">
                                        <span x-text="adminName"></span>
                                        <!-- Dropdown Trigger -->
                                        <div x-data="{ showDropdown: false }" class="relative">
                                        <button @click="showDropdown = !showDropdown" class="ml-2 focus:outline-none">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                            <div x-show="showDropdown" @click.away="showDropdown = false" x-transition
                                                class="absolute left-0 mt-2 w-44 bg-white text-gray-800 rounded shadow-lg z-50">
                                                <a href="{{route('user.profiles.show', $profile->admin_id)  }}"
                                                class="block px-4 py-2 hover:bg-green-100 text-sm">
                                                    <i class="bi bi-person-circle"></i> View Profile
                                                </a>
                                                <form method="POST" action="#">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-green-100 text-sm text-red-600">
                                                        <i class="bi bi-trash"></i> Delete Conversation
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="minimized = true" class="text-white text-xl leading-none" title="Minimize">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <button @click="closeBox()" class="text-white text-2xl leading-none">&times;</button>
                                    </div>
                                </div>
                                <!-- Messages -->
                                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="user-chat-messages">
                                    <template x-for="msg in messages" :key="msg.id">
                                        <div :class="msg.sender_id == {{ auth()->id() }} ? 'text-right' : 'text-left'" class="my-2">
                                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'inline-block bg-yellow-100 text-yellow-900' : 'inline-block bg-gray-100 text-gray-800'"
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
                                                        </div>

                                                        <div class="mb-4 w-full flex flex-col items-center relative">
                                    </template>
                                    <div x-show="messages.length == 0" class="text-gray-400 text-center text-xs mt-10">No messages yet.</div>
                                </div>
                                <!-- Chat Input -->
                                <form @submit.prevent="sendMessage()" class="border-t px-2 py-3 bg-white">
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
                                        <div class="relative flex-1 flex-1" x-data="{ showEmoji: false }">
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
        {{-- Description & Contact --}}
        <div class="bg-white/80 rounded-2xl shadow-inner p-6 text-green-900 space-y-4 mt-8">
            <h2 class="text-xl font-semibold mb-2">Description</h2>
            @php
                $descWords = str_word_count(strip_tags($profile->description));
                $descShort = \Illuminate\Support\Str::words($profile->description, 20, '...');
            @endphp
            <div x-data="{ expanded: false }">
                <p class="text-gray-700" x-show="!expanded">{!! $descShort !!}
                    @if($descWords > 20)
                        <button type="button" class="text-green-700 underline ml-2" @click="expanded = true">See more</button>
                    @endif
                </p>
                <div x-show="expanded" x-transition>
                    <div class="text-gray-700 whitespace-pre-line break-words overflow-auto" style="word-break:break-word;width:100%;max-height:250px;padding-right:4px;box-sizing:border-box;">
                        {!! nl2br(e($profile->description)) !!}
                    </div>
                    <button type="button" class="text-green-700 underline ml-2" @click="expanded = false">See less</button>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 mt-6 text-sm"></div>
        </div>

        {{-- Gallery --}}
        @if (!empty($profile->farm_photos))
            <div class="mt-10">
                <h2 class="text-xl font-semibold text-green-800 mb-4">Farm Gallery</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($profile->farm_photos as $photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Farm Photo" class="w-full h-40 object-cover rounded-lg border border-green-300 shadow">
                    @endforeach
                </div>
            </div>
        @endif
        {{-- Farm Gallery --}}
@if (!empty($profile->farm_gallery))
    @php
        // Ensure farm_gallery is an array
        $gallery = is_array($profile->farm_gallery) ? $profile->farm_gallery : json_decode($profile->farm_gallery, true);
        if (!is_array($gallery)) $gallery = [];
    @endphp
    @if(count($gallery))
        <div x-data="{ open: false, imgSrc: '' }">
        <div class="mt-10">
            <h2 class="text-xl font-semibold text-green-800 mb-4">Farm Gallery</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($gallery as $photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Farm Gallery Photo" class="w-full h-40 object-cover rounded-lg border border-green-300 shadow cursor-pointer" @click="open = true; imgSrc = '{{ asset('storage/' . $photo) }}'">
                @endforeach
            </div>
            <!-- Modal -->
            <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" style="display: none;">
                <div class="relative">
                    <img :src="imgSrc" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl border-4 border-white">
                    <button @click="open = false" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow text-black hover:bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @php
        // Collect certificates from profile and admin's profile (handles string, json array or array)
        $certificates = [];

        $collect = function ($value) {
            $out = [];
            if (empty($value)) return $out;
            if (is_array($value)) return $value;
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) return $decoded;
                return [$value];
            }
            return $out;
        };

        // From the current profile
        $certificates = array_merge($certificates, $collect($profile->certificates ?? null));

        // From the admin's profile (some data stores certificates on the admin's profile)
        if (!empty($profile->admin) && !empty($profile->admin->profile)) {
            $adminProfile = $profile->admin->profile;
            $adminCerts = $adminProfile->certificates ?? $adminProfile->certificate ?? null;
            $certificates = array_merge($certificates, $collect($adminCerts));
        }

        // Normalize paths (remove leading slash), remove null/empty and duplicate entries
        $certificates = array_values(array_filter(array_unique(array_map(function($c){ return $c ? ltrim($c, '/') : null; }, $certificates))));
    @endphp
   
    @if(count($certificates))
        <div class="mt-10">
            <h2 class="text-xl font-semibold text-green-800 mb-4">Certificates</h2>
            <div x-data="{ open:false, src:'' }">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($certificates as $cert)
                        @php
                            $relative = ltrim($cert, '/');
                            $publicPath = public_path('storage/' . $relative);
                            $url = asset('storage/' . $relative);
                            $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
                            $isPdf = $ext === 'pdf';
                            $exists = file_exists($publicPath);
                        @endphp

                        @if($isPdf)
                            <div class="rounded-lg overflow-hidden border border-green-200 shadow bg-white p-2 flex flex-col items-center justify-center text-center">
                                @if($exists)
                                    <a href="{{ $url }}" target="_blank" class="flex flex-col items-center gap-2 text-green-800 hover:text-green-600">
                                        <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM13 3.5V9h5.5L13 3.5z"/></svg>
                                        <span class="text-xs break-words">{{ basename($relative) }}</span>
                                    </a>
                                @else
                                    <div class="text-xs text-red-500">Missing file<br><span class="font-mono text-[10px]">{{ basename($relative) }}</span></div>
                                @endif
                            </div>
                        @else
                            @if($exists)
                                <button type="button" class="rounded-lg overflow-hidden border border-green-200 shadow bg-white p-1" @click="open=true; src='{{ $url }}'">
                                    <img src="{{ $url }}" alt="Certificate" class="w-full h-36 object-contain bg-white" />
                                </button>
                            @else
                                <div class="rounded-lg overflow-hidden border border-green-200 shadow bg-white p-2 flex items-center justify-center text-xs text-red-500">Missing image</div>
                            @endif
                        @endif
                    @endforeach
                </div>

                <!-- Modal -->
                <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" style="display:none;">
                    <div class="relative max-w-3xl w-full p-4">
                        <button @click="open=false" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow">&times;</button>
                        <img :src="src" class="w-full max-h-[80vh] object-contain rounded-lg shadow-2xl bg-white" />
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
    </div>
</div>
<br>
<br><br><br>

<!-- Products Section -->
<div class="max-w-6xl mx-auto">
      <div class="max-w-xl mx-auto px-2">
        <input 
            type="text" 
            id="searchBar" 
            placeholder="Search products or services..." 
            class="w-full border border-green-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-green-600 shadow text-base"
        />
    </div> <br><br>
    <div class="mb-4 flex flex-col items-center w-full relative">
        <h2 class="text-2xl font-bold text-green-800 text-center w-full">Available Products</h2>
        <div class="flex w-full justify-end mt-2">
            <div class="flex space-x-4">
            @php
                $cartCount = session('cart') ? count(session('cart')) : 0;
                $purchaseCount = auth()->user()->orders()->count();
            @endphp
            <a id="cart-icon" href="{{ route('cart.index') }}" class="relative text-green-800 font-semibold hover:text-lime-600 flex items-center space-x-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.3 5.2a1 1 0 001 .8h11.6a1 1 0 001-.8L17 13M9 21h.01M15 21h.01"/>
                </svg>
                @if ($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cartCount }}
                    </span>
                @endif
                <span>Cart</span>
            </a>

            <a href="{{ route('user.orders') }}" class="relative flex items-center text-green-800 font-semibold hover:text-lime-600 transition ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5h6M9 3h6a2 2 0 012 2v2H7V5a2 2 0 012-2zM7 9h10M7 13h10M7 17h10M5 7h14a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                </svg>
                @if ($purchaseCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $purchaseCount }}
                    </span>
                @endif
                <span>Purchase</span>
            </a>
            </div>
            </div>
        </div>
    </div>


    @if ($products->count())
    <div class="max-w-6xl mx-auto" x-data="{
        products: {{ Js::from($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'description' => $p->description,
            'price' => $p->price,
            'image' => $p->image,
            'unit' => $p->unit,
            'stock_quantity' => $p->stock_quantity ?? 0
        ])) }},
        cartCount: {{ session('cart') ? count(session('cart')) : 0 }},
        fly: false,
        flySrc: '',
        flyStyle: '',
        addToCart(product, event) {
            const img = event.target.closest('.product-card').querySelector('img');
            const cartIcon = document.getElementById('cart-icon');
            const imgRect = img.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            this.flySrc = img.src;
            this.flyStyle = `
                left:${imgRect.left}px;
                top:${imgRect.top}px;
                width:${imgRect.width}px;
                height:${imgRect.height}px;
            `;
            this.fly = true;

            this.$nextTick(() => {
                const flyImg = document.querySelector('.fly-img');
                if (flyImg) {
                    flyImg.style.left = imgRect.left + 'px';
                    flyImg.style.top = imgRect.top + 'px';
                    flyImg.style.width = imgRect.width + 'px';
                    flyImg.style.height = imgRect.height + 'px';

                    setTimeout(() => {
                        flyImg.classList.add('fly-active');
                        flyImg.style.left = cartRect.left + 'px';
                        flyImg.style.top = (cartRect.top - 40) + 'px';
                    }, 50);

                    setTimeout(() => {
                        this.fly = false;
                        flyImg.classList.remove('fly-active');
                    }, 800);
                }
            });

            // Ajax request to add to cart
            fetch(`/cart/add/${product.id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.cartCount++;
                    cartIcon.classList.add('animate-bounce');
                    setTimeout(() => cartIcon.classList.remove('animate-bounce'), 400);
                }
            });
        }
    }">
        <template x-if="fly">
            <img :src="flySrc" :style="flyStyle" class="fly-img" />
        </template>
        <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-4 gap-4 mt-6 items-stretch px-4 md:px-8">

<style>
.fly-img {
    position: fixed;
    z-index: 1000;
    border-radius: 50%;
    box-shadow: 0 8px 24px rgba(76,175,80,0.25);
    pointer-events: none;
    transition:
        transform 0.7s cubic-bezier(.22,1,.36,1),
        opacity 0.7s,
        box-shadow 0.7s;
    opacity: 1;
}
.fly-img.fly-active {
    transform: scale(0.3) translateY(-120px) rotate(-25deg);
    opacity: 0;
    box-shadow: 0 2px 8px rgba(76,175,80,0.12);
}
</style>
            <template x-for="product in products" :key="product.id">
                <div class="product-card border border-lime-200 rounded-xl p-2 shadow-md hover:shadow-xl transition duration-300 bg-white/80 flex flex-col justify-between h-full">
                    <a :href="'/products/' + product.id">
                        <template x-if="product.image">
                            <img :src="'/storage/' + product.image" :alt="product.name" class="w-full h-28 object-cover rounded-md mb-2 shadow">
                        </template>
                        <template x-if="!product.image">
                            <div class="flex flex-col items-center justify-center w-full h-28 text-gray-300 bg-green-50 rounded-md mb-2">
                                <i class="bi bi-image" style="font-size:2rem;"></i>
                                <span class="text-xs mt-1">No Image</span>
                            </div>
                        </template>
                        <h2 class="text-base font-bold text-green-900 capitalize truncate" x-text="product.name"></h2>
                        <p class="text-xs text-gray-700 mb-1" x-text="product.description.split(' ').slice(0,8).join(' ') + (product.description.split(' ').length > 8 ? '...' : '')"></p>
                        <p class="text-green-700 font-bold mb-1 text-sm">
                            ₱<span x-text="Number(product.price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})"></span> <span class="text-gray-700">/ <span x-text="product.unit"></span></span>
                        </p>
                        <p class="text-xs text-gray-500 mb-1">Stock: <span x-text="product.stock_quantity ?? 'N/A'"></span></p>
                    </a>
                    <div class="flex flex-col gap-1 mt-2">
                        <template x-if="product.stock_quantity > 0">
                            <button @click="addToCart(product, $event)"
                                class="w-full bg-lime-600 hover:bg-lime-700 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.3 5.2a1 1 0 001 .8h11.6a1 1 0 001-.8L17 13M9 21h.01M15 21h.01"/>
                                </svg>
                                Add to Cart
                            </button>
                        </template>
                        <template x-if="product.stock_quantity > 0">
                            <form :action="'/buy/now/' + product.id" method="POST" class="w-full mt-1">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit"
                                    class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm">
                                    Buy Now
                                </button>
                            </form>
                        </template>
                        <template x-if="product.stock_quantity == 0">
                            <button type="button"
                                class="w-full bg-gray-400 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm cursor-not-allowed"
                                disabled>
                                Out of Stock
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Pagination links --}}
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-gray-500">No products available.</p>
    @endif
</div>
<br>
<!-- Services Section -->
<div class="max-w-6xl mx-auto mt-10">
    <h2 class="text-2xl font-bold text-green-800 mb-4 text-center">Available Training Services</h2>
       <!-- Right: Bookings Button -->
       <div class="flex w-full justify-end mb-2">
           <a href="{{ route('user.bookings.index') }}"
               class="relative text-green-800 font-bold hover:text-lime-600 flex items-center gap-2 transition text-sm">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
               </svg>
               <span>Bookings</span>
           </a>
       </div>
       <br>

    @if ($services->count())
    <div id="servicesGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6 justify-items-center">
            @foreach ($services as $service)
                <div class="bg-white rounded-3xl shadow-xl transition-all duration-200 cursor-pointer flex flex-row border border-green-100 ring-1 ring-green-100 group relative service-card" data-name="{{ strtolower($service->service_name) }}" data-desc="{{ strtolower($service->description) }}" onclick="window.location='{{ route('user.services.show', $service->id) }}'">
                    @php
                        $images = [];
                        if (!empty($service->images)) {
                            if (is_array($service->images)) {
                                $images = $service->images;
                            } elseif (is_string($service->images)) {
                                $decoded = json_decode($service->images, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $images = $decoded;
                                } else {
                                    $images = [$service->images];
                                }
                            }
                        }
                    @endphp
                    <div class="w-20 h-20 rounded-2xl overflow-hidden m-2 bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center relative flex-shrink-0">
                        <button class="absolute top-2 right-2 z-10 bg-white/80 rounded-full p-1 favorite-btn" title="Add to favorites" onclick="event.stopPropagation(); this.classList.toggle('text-red-500');">
                            <i class="bi bi-heart"></i>
                        </button>
                        @if(!empty($images) && isset($images[0]) && $images[0])
                            <img src="{{ asset('storage/' . ltrim($images[0], '/')) }}" alt="{{ $service->service_name }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
                        @else
                            <div class="flex flex-col items-center justify-center w-full h-full text-gray-300">
                                <i class="bi bi-image" style="font-size:3rem;"></i>
                                <span class="text-xs mt-1">No Image</span>
                            </div>
                        @endif
                        @if ($service->deleted_at)
                            <span class="absolute top-2 left-2 bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full shadow">Archived</span>
                        @else
                            <span class="absolute top-2 left-2 bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full shadow">Available</span>
                        @endif
                    </div>
                    <div class="flex flex-col flex-1 py-4 pr-4 pl-2 justify-between">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-sm font-semibold text-green-800 truncate flex items-center gap-2">{{ $service->service_name }}</h2>
                        </div>
                      
                        <p class="text-gray-700 mb-1 text-xs sm:text-sm leading-snug text-justify service-desc">
                            {{ \Illuminate\Support\Str::words($service->description, 12, '...') }}
                        </p>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-green-700 text-base drop-shadow">₱{{ number_format($service->price, 2) }}</span>
                            <span class="text-gray-500 text-xs font-semibold bg-green-50 px-2 py-0.5 rounded-full">/ {{ $service->unit }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs text-gray-500 bg-green-50 px-2 py-0.5 rounded-full">Duration: <span class="font-bold">{{ $service->duration ?? '-' }}</span></span>
                        </div>
                        <div class="flex items-center gap-1 mb-2">
                            <span class="text-yellow-500">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                            </span>
                            <span class="text-xs text-gray-600">4.0 (123 ratings)</span>
                        </div>
                        <div class="flex gap-2 mt-auto">
                            <button
                            class="flex-1 bg-lime-600 hover:bg-lime-700 text-white font-semibold py-1.5 rounded-lg shadow transition text-xs flex items-center justify-center gap-1 inquire-btn"
                            style="min-width: 0; padding-left:0.75rem; padding-right:0.75rem;"
                            onclick="event.stopPropagation(); openChatModal({
                                serviceId: '{{ $service->id }}',
                                serviceName: '{{ $service->service_name }}',
                                serviceImage: '{{ asset('storage/' . ltrim($images[0] ?? '', '/')) }}',
                                serviceType: '{{ $service->service_name }}',
                                servicePrice: '{{ $service->price }}',
                                adminId: '{{ $service->admin ? $service->admin->id : '' }}',
                                adminName: '{{ $service->admin ? $service->admin->name : 'Unknown' }}'
                            });">
                            <i class="bi bi-chat-dots"></i> Inquire
                        </button>
                            <button class="flex-1 bg-green-800 hover:bg-green-900 text-white font-semibold py-1.5 rounded-lg shadow transition text-xs flex items-center justify-center gap-1" onclick="event.stopPropagation(); window.location='{{ route('user.services.show', $service->id) }}';">
                                <i class="bi bi-calendar-check"></i> Reserve
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination links --}}
        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @else
        <p class="text-gray-500">No services available.</p>
    @endif
</div>

    <!-- Chat Modal (Alpine.js powered) -->
    <div
        x-data="{
            openChat: false,
            minimized: false,
            message: '',
            imageFile: null,
            messages: [],
            poll: null,
            serviceId: null,
            serviceName: '',
            serviceImage: '',
            serviceType: '',
            servicePrice: '',
            adminId: null,
            adminName: '',
                            adminPhoto: '{{ !empty($profile->profile_photo) && file_exists(public_path('storage/' . $profile->profile_photo)) ? asset('storage/' . $profile->profile_photo) : asset('agri-profile.png') }}',
            fetchMessages() {
                if (!this.adminId) return;
                fetch(`/messages/${this.adminId}`)
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
                    formData.append('service_id', this.serviceId);
                    formData.append('service_name', this.serviceName);
                    formData.append('service_image', this.serviceImage);
                    formData.append('service_type', this.serviceType);
                    formData.append('service_price', this.servicePrice);
                    fetch(`/messages/${this.adminId}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    }).then(() => {
                        this.message = '';
                        this.imageFile = null;
                        this.fetchMessages();
                    });
                }
            },
            openBox(serviceId, serviceName, serviceImage, serviceType, servicePrice, adminId, adminName, adminPhoto) {
                this.openChat = true;
                this.minimized = false;
                this.serviceId = serviceId;
                this.serviceName = serviceName;
                this.serviceImage = serviceImage;
                this.serviceType = serviceType;
                this.servicePrice = servicePrice;
                this.adminId = adminId;
                this.adminName = adminName;
                this.adminPhoto = adminPhoto;
                this.fetchMessages();
                // Automatically send service info as a message
                setTimeout(() => {
                    this.message = 'Service: ' + this.serviceName + '\nType: ' + this.serviceType + '\nPrice: ₱' + this.servicePrice;
                    // If serviceImage exists, send as image
                    if (this.serviceImage) {
                        fetch(this.serviceImage)
                            .then(res => res.blob())
                            .then(blob => {
                                this.imageFile = blob;
                                this.sendMessage();
                            });
                    } else {
                        this.sendMessage();
                    }
                }, 500);
                if(this.poll) clearInterval(this.poll);
                this.poll = setInterval(() => this.fetchMessages(), 5000);
            },
            closeBox() {
                this.openChat = false;
                this.minimized = false;
                if(this.poll) clearInterval(this.poll);
            }
        }"
        x-init="window.openChatModal = (opts) => openBox(opts.serviceId, opts.serviceName, opts.serviceImage, opts.serviceType, opts.servicePrice, opts.adminId, opts.adminName, opts.adminPhoto)"
    >
        <div
            x-show="openChat"
            x-transition
            :style="minimized 
                ? 'width:3rem;height:3rem;bottom:1rem;right:1rem;padding:2;overflow:hidden;' 
                : 'width:22rem;bottom:0;right:0;'"
            class="fixed bg-white border border-gray-300 rounded-full shadow-lg z-50"
            style="display:none;"
            @click.away="minimized ? minimized = false : closeBox()"
        >
            <!-- Minimized State -->
            <template x-if="minimized">
                <div class="flex items-center justify-center h-full w-full cursor-pointer" @click="minimized = false">
                    <img :src="adminPhoto" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-yellow-500">
                </div>
            </template>
            <!-- Expanded State -->
            <template x-if="!minimized">
                <div class="flex flex-col h-[24rem] w-[21rem] rounded-xl overflow-hidden bg-white">
                    <!-- Header -->
                    <div class="flex flex-col">
                        <div class="flex items-center justify-between bg-green-600 text-white px-4 py-2 relative">
                            <div class="flex items-center gap-2">
                                <img :src="adminPhoto" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-white">
                                <span x-text="adminName"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="minimized = true" class="text-white text-xl leading-none" title="Minimize">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <button @click="closeBox()" class="text-white text-2xl leading-none">&times;</button>
                            </div>
                        </div>
                        <!-- Service Name/Type -->
                        <div class="bg-green-50 text-green-800 px-4 py-2 text-sm font-semibold border-b border-green-100">
                            <span>Inquiring about: </span>
                            <span x-text="serviceName"></span>
                        </div>
                    </div>
                  
                    <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="user-chat-messages">
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_id == {{ auth()->id() }} ? 'text-right' : 'text-left'" class="my-2">
                                <div :class="msg.sender_id == {{ auth()->id() }} ? 'inline-block bg-yellow-100 text-yellow-900' : 'inline-block bg-gray-100 text-gray-800'"
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
                    <!-- Chat Input -->
                    <form @submit.prevent="sendMessage()" class="border-t px-2 py-3 bg-white">
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
                            <div class="relative flex-1 flex-1" x-data="{ showEmoji: false }">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.inquire-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var card = btn.closest('[data-name]');
                var serviceName = card.querySelector('h2')?.textContent?.trim() || '';
                var servicePrice = card.querySelector('.font-bold')?.textContent?.replace('₱','').trim() || '';
                var serviceDuration = card.querySelector('.bg-green-50 span.font-bold')?.textContent?.trim() || '';
                var adminId = '{{ $profile->user_id ?? $profile->id }}';
                var adminName = '{{ $profile->farm_owner }}';
                var adminPhoto = '{{ !empty($profile->profile_photo) && file_exists(public_path('storage/' . $profile->profile_photo)) ? asset('storage/' . $profile->profile_photo) : asset('agri-profile.png') }}';
                var serviceImage = '';
                var imgEl = card.querySelector('img');
                if (imgEl && imgEl.src) {
                    serviceImage = imgEl.src;
                }
                window.openChatModal({
                    serviceId: '',
                    serviceName: serviceName,
                    serviceImage: serviceImage,
                    serviceType: '',
                    servicePrice: servicePrice,
                    adminId: adminId,
                    adminName: adminName,
                    adminPhoto: adminPhoto
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBar = document.getElementById('searchBar');
        const productCards = document.querySelectorAll('.product-card');
        const serviceCards = document.querySelectorAll('.service-card');

        searchBar.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();

            productCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const desc = card.getAttribute('data-desc');
                if (name.includes(query) || desc.includes(query)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });

            serviceCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const desc = card.getAttribute('data-desc');
                if (name.includes(query) || desc.includes(query)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });

        // Attach AJAX events for follow/unfollow/like/unlike
        function attachFollowLikeEvents() {
            document.querySelectorAll('.follow-form').forEach(form => {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.querySelector('.followers-count').textContent = data.followers;
                            this.outerHTML = `
                                <form method="POST" action="{{ route('user.profiles.unfollow', $profile->id) }}" class="inline unfollow-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-5 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                        Unfollow
                                    </button>
                                </form>
                            `;
                            attachFollowLikeEvents();
                        }
                    });
                };
            });
            document.querySelectorAll('.unfollow-form').forEach(form => {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    fetch(this.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.querySelector('.followers-count').textContent = data.followers;
                            this.outerHTML = `
                                <form method="POST" action="{{ route('user.profiles.follow', $profile->id) }}" class="inline follow-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                        Follow
                                    </button>
                                </form>
                            `;
                            attachFollowLikeEvents();
                        }
                    });
                };
            });
            document.querySelectorAll('.like-form').forEach(form => {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.querySelector('.likes-count').textContent = data.likes;
                            this.outerHTML = `
                                <form method="POST" action="{{ route('user.profiles.unlike', $profile->id) }}" class="inline unlike-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-5 py-2 bg-pink-400 hover:bg-pink-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                        Unlike
                                    </button>
                                </form>
                            `;
                            attachFollowLikeEvents();
                        }
                    });
                };
            });
            document.querySelectorAll('.unlike-form').forEach(form => {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    fetch(this.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.querySelector('[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            document.querySelector('.likes-count').textContent = data.likes;
                            this.outerHTML = `
                                <form method="POST" action="{{ route('user.profiles.like', $profile->id) }}" class="inline like-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    <button type="submit" class="px-5 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                        Like
                                    </button>
                                </form>
                            `;
                            attachFollowLikeEvents();
                        }
                    });
                };
            });
        }
        // Initial attach
        attachFollowLikeEvents();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var productId = btn.getAttribute('data-product-id');
                fetch('/cart/add/' + productId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success && data.cartCount !== undefined) {
                        var cartIcon = document.getElementById('cart-icon');
                        var badge = cartIcon.querySelector('span');
                        if (badge) {
                            badge.textContent = data.cartCount;
                        } else {
                            var newBadge = document.createElement('span');
                            newBadge.className = 'absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full';
                            newBadge.textContent = data.cartCount;
                            cartIcon.appendChild(newBadge);
                        }
                    }
                });
            });
        });
    });
</script>
<script>
function openChatbox() {
    document.getElementById('chatbox-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('chatbox-input').focus(), 200);
}
function closeChatbox() {
    document.getElementById('chatbox-modal').classList.add('hidden');
}

// Basic send/receive simulation (replace with AJAX for real chat)
document.getElementById('chatbox-form').onsubmit = function(e) {
    e.preventDefault();
    const input = document.getElementById('chatbox-input');
    const messages = document.getElementById('chatbox-messages');
    const userMsg = input.value.trim();
    if (!userMsg) return;
    messages.innerHTML += `<div class="text-right mb-2"><span class="inline-block bg-green-100 px-2 py-1 rounded">${userMsg}</span></div>`;
    input.value = '';
    messages.scrollTop = messages.scrollHeight;

    // Simulate reply (replace this with AJAX call to your backend)
    setTimeout(() => {
        messages.innerHTML += `<div class="text-left mb-2"><span class="inline-block bg-gray-100 px-2 py-1 rounded">Thank you for your message! (Simulated reply)</span></div>`;
        messages.scrollTop = messages.scrollHeight;
    }, 1000);
};
</script>
<style>
.fly-img {
    position: fixed;
    z-index: 1000;
    border-radius: 50%;
    box-shadow: 0 8px 24px rgba(76,175,80,0.25);
    pointer-events: none;
    transition:
        transform 0.7s cubic-bezier(.22,1,.36,1),
        opacity 0.7s,
        box-shadow 0.7s;
    opacity: 1;
}
.fly-img.fly-active {
    transform: scale(0.3) translateY(-120px) rotate(-25deg);
    opacity: 0;
    box-shadow: 0 2px 8px rgba(76,175,80,0.12);
}
</style>
@endpush

@endsection