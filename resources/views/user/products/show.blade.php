{{-- filepath: resources/views/user/products/show.blade.php --}}
@extends('user.layout')

@section('content')
<!-- Top Right Links -->
    <br><br><br>
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex space-x-4 items-center justify-center md:justify-end">
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

    <a href="{{ route('user.orders') }}" class="relative flex items-center text-green-800 font-semibold hover:text-lime-600 transition ml-2 md:mr-16">
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


<div class="max-w-4xl mx-auto mt-6 bg-white/90 p-8 rounded-2xl shadow-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="flex flex-col items-center">
            <img id="product-image"
                src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300' }}"
                alt="{{ $product->name }}"
                class="w-full h-72 object-cover rounded-xl shadow-md border-4 border-lime-200 transition hover:scale-105 duration-300">
        </div>
        <div class="flex flex-col justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-green-800 mb-2">{{ $product->name }}</h1>
                <p class="text-gray-700 mb-4 text-lg">{{ $product->description }}</p>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl font-bold text-lime-700">₱{{ number_format($product->price, 2) }}</span>
                    <span class="text-base text-gray-600">/ {{ $product->unit }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Stock Available: <span class="font-semibold">{{ $product->stock_quantity }}</span></p>
            </div>

<div class="my-4">
    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
    <div class="flex items-center space-x-2">
        <button type="button" id="minus-btn" class="px-3 py-1 bg-lime-100 text-green-800 rounded hover:bg-lime-200 font-bold text-lg">−</button>
        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
               class="w-16 text-center border-lime-300 rounded-md shadow-sm focus:ring-lime-400 focus:border-lime-400 text-lg">
        <button type="button" id="plus-btn" class="px-3 py-1 bg-lime-100 text-green-800 rounded hover:bg-lime-200 font-bold text-lg">+</button>
    </div>
</div>

<div class="mt-6 flex flex-col sm:flex-row gap-4">
    @if($product->stock_quantity > 0)
        <!-- Add to Cart -->
        <form id="addToCartForm" action="{{ route('cart.add', $product->id) }}" method="POST" onsubmit="return animateToCart(event)" class="flex-1">
            @csrf
            <input type="hidden" name="quantity" id="cartQuantity" value="1">
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-lime-600 hover:bg-lime-700 text-white px-6 py-3 rounded-xl font-semibold shadow transition text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9"/>
                </svg>
                <span>Add to Cart</span>
            </button>
        </form>
        <!-- Buy Now -->
        <form id="buyNowForm" action="{{ route('buy.now', $product->id) }}" method="POST" class="flex-1">
            @csrf
            <input type="hidden" name="quantity" id="hiddenQuantity" value="1">
            <button type="submit"
                    onclick="document.getElementById('hiddenQuantity').value = document.getElementById('quantity').value"
                    class="w-full flex items-center justify-center gap-2 bg-green-800 hover:bg-green-900 text-white px-6 py-3 rounded-xl font-semibold shadow transition text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a4 4 0 00-8 0v2m-3 4h14l-1.38 8.26a2 2 0 01-1.98 1.74H7.36a2 2 0 01-1.98-1.74L4 13z" />
                </svg>
                <span>Buy Now</span>
            </button>
        </form>
    @else
        <div class="w-full">
            <button type="button" class="w-full flex items-center justify-center gap-2 bg-gray-400 text-white px-6 py-3 rounded-xl font-semibold shadow transition text-lg cursor-not-allowed" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m4-9l2 9"/>
                </svg>
                <span>Out of Stock</span>
            </button>
        </div>
    @endif
</div>
        </div>
    </div>
</div>

{{-- Seller Info Section --}}


    <div class="max-w-4xl mx-auto mt-6 bg-white/90 p-6 rounded-2xl shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ $product->admin->profile && $product->admin->profile->profile_photo 
                            ? asset('storage/' . $product->admin->profile->profile_photo) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($product->admin->name) }}"
                     alt="{{ $product->admin->name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-lime-200 shadow">
                <div>
                    <div class="flex items-center flex-wrap gap-2">
                        <p class="text-lg font-semibold text-green-800">{{ $product->admin->profile->farm_owner }}</p>
                        <span class="text-xs bg-lime-100 text-green-700 px-2 py-1 rounded">Shop Badge</span>
                    </div>
                    <a href="{{route('user.profiles.show', $product->admin->profile->id ?? '')  }}" class="text-sm text-blue-600 hover:underline cursor-pointer">Visit shop</a>
                </div>
            </div>
            <div class="flex gap-3">
                     <a href="{{route('user.profiles.show', $product->admin->profile->id ?? '')  }}"
                         class="flex items-center gap-2 text-sm bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9l1.5 9h15l1.5-9M4.5 9h15m-12 0V6a1.5 1.5 0 013 0v3m3 0V6a1.5 1.5 0 013 0v3" />
                    </svg>
                    <span>Visit</span>
                </a>
                <div
                    x-data="{
                        openChat: false,
                        minimized: false,
                        message: '',
                        imageFile: null,
                        messages: [],
                        poll: null,
                        adminId: '{{ $product->admin_id }}',
                        adminName: '{{ $product->admin->profile->farm_owner }}',
                        adminPhoto: '{{ $product->admin->profile && $product->admin->profile->profile_photo ? asset('storage/' . $product->admin->profile->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($product->admin->name) }}',
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
                    <div
                        x-show="openChat"
                        x-transition
                        :style="minimized 
                            ? 'width:3rem;height:3rem;bottom:1rem;right:5rem;padding:2;overflow:hidden;' 
                            : 'width:22rem;bottom:0;right:5rem;'"
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
                                                <a href="{{route('user.profiles.show', $product->admin_id)  }}"
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
                @include('components.user.chat-box', [
                    'adminId' => $product->admin_id,
                    'adminName' => $product->admin->name,
                    'adminPhoto' => $product->admin->profile && $product->admin->profile->profile_photo
                        ? asset('storage/' . $product->admin->profile->profile_photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($product->admin->name),
                    'profileUrl' => route('user.profiles.show', $product->admin_id)
                ])
            </div>
        </div>

        <hr class="my-6 border-lime-200">
        <h3 class="text-green-700 font-bold text-base mb-2 flex items-center gap-1"><i class="bi bi-bar-chart-fill"></i> Seller Stats</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 mb-6 text-center">
            <div>
                <p class="text-lg font-bold text-gray-800">{{ $product->admin->products->count() }}</p>
                <p class="text-xs text-gray-500">Products</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-800">
                    {{ $product->admin->services ? $product->admin->services->count() : 0 }}
                </p>
                <p class="text-xs text-gray-500">Services</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-800">
                    {{ $product->admin->profile && $product->admin->profile->average_rating ? number_format($product->admin->profile->average_rating, 1) : '0.0' }}
                </p>
                <p class="text-xs text-gray-500">Rating</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-800 followers-count">{{ $product->admin->profile ? \App\Models\ProfileFollower::where('profile_id', $product->admin->profile->id)->count() : 0 }}</p>
                <p class="text-xs text-gray-500">Followers</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-800 likes-count">{{ $product->admin->profile ? \App\Models\ProfileLike::where('profile_id', $product->admin->profile->id)->count() : 0 }}</p>
                <p class="text-xs text-gray-500">Likes</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-800">Within hours</p>
                <p class="text-xs text-gray-500">Response Time</p>
            </div>
        </div>

        <h3 class="text-green-700 font-bold text-base mb-2 flex items-center gap-1"><i class="bi bi-person-lines-fill"></i> Seller Contact</h3>
        <div class="flex flex-col sm:flex-row gap-6 mb-6">
            <div>
                <p class="font-semibold">Phone:</p>
                <p>{{ $product->admin->profile->phone_number ?? 'Not Available' }}</p>
            </div>
            <div>
                <p class="font-semibold">Email:</p>
                <p>{{ $product->admin->profile->email ?? 'Not Available' }}</p>
            </div>
            <div>
                <p class="font-semibold">Address:</p>
                <p>
                    {{ $product->admin->profile->region ?? 'Region not available' }},
                    {{ $product->admin->profile->barangay ?? 'Barangay not available' }} 
                    {{ $product->admin->profile->city ?? 'City not available' }},
                    {{ $product->admin->profile->province ?? 'Province not available' }}
                </p>
            </div>
        </div>

        @if($product->admin->profile)
        <div id="follow-like-section">
            <div class="flex gap-4 mt-6">
                @php
                    $alreadyFollowed = false;
                    $alreadyLiked = false;
                    $followersCount = \App\Models\ProfileFollower::where('profile_id', $product->admin->profile->id)->count();
                    $likesCount = \App\Models\ProfileLike::where('profile_id', $product->admin->profile->id)->count();
                    if(auth()->check()) {
                        $alreadyFollowed = \App\Models\ProfileFollower::where('profile_id', $product->admin->profile->id)
                            ->where('user_id', auth()->id())->exists();
                        $alreadyLiked = \App\Models\ProfileLike::where('profile_id', $product->admin->profile->id)
                            ->where('user_id', auth()->id())->exists();
                    }
                @endphp
                <div>
                    @if($alreadyFollowed)
                        <form method="POST" action="{{ route('user.profiles.unfollow', $product->admin->profile->id) }}" class="inline unfollow-form" data-id="{{ $product->admin->profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Unfollow
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.follow', $product->admin->profile->id) }}" class="inline follow-form" data-id="{{ $product->admin->profile->id }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Follow
                            </button>
                        </form>
                    @endif
                </div>
                <div>
                    @if($alreadyLiked)
                        <form method="POST" action="{{ route('user.profiles.unlike', $product->admin->profile->id) }}" class="inline unlike-form" data-id="{{ $product->admin->profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-pink-400 hover:bg-pink-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                Unlike
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.like', $product->admin->profile->id) }}" class="inline like-form" data-id="{{ $product->admin->profile->id }}">
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
        @endif
    </div>
        @if ($otherProducts->count())
    <div x-data="{ search: '' }" class="max-w-4xl mx-auto mt-8 bg-white/90 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">More Products from {{ $product->admin->profile->farm_owner }}</h2>
        <!-- Search Bar -->
        <div class="mb-4 flex justify-end">
            <input
                type="text"
                x-model="search"
                placeholder="Search more products..."
                class="w-full sm:w-1/3 px-4 py-2 border border-lime-300 rounded-md focus:outline-none focus:ring-2 focus:ring-lime-400 bg-white/80"
            />
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($otherProducts as $p)
                <template x-if="search === '' || '{{ strtolower($p->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($p->description) }}'.includes(search.toLowerCase())">
                    <a href="{{ route('product.show', $p->id) }}"
                       class="block border border-lime-100 rounded-xl overflow-hidden hover:shadow-lg transition bg-white">
                        <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300' }}"
                             alt="{{ $p->name }}" class="w-full h-32 object-cover">
                        <div class="p-3">
                            <h3 class="text-sm font-semibold truncate text-green-900">{{ $p->name }}</h3>
                            <p class="text-green-700 font-bold text-sm mt-1">₱{{ number_format($p->price, 2) }}</p>
                        </div>
                    </a>
                </template>
            @endforeach
        </div>
        <!-- No products found message -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const quantityInput = document.getElementById('quantity');
                                const minusBtn = document.getElementById('minus-btn');
                                const plusBtn = document.getElementById('plus-btn');
                                const maxStock = parseInt(quantityInput.max);
                                const cartQuantity = document.getElementById('cartQuantity');
                                const hiddenQuantity = document.getElementById('hiddenQuantity');

                                minusBtn.addEventListener('click', function() {
                                    let value = parseInt(quantityInput.value);
                                    if (value > 1) {
                                        quantityInput.value = value - 1;
                                    }
                                    syncHiddenInputs();
                                });

                                plusBtn.addEventListener('click', function() {
                                    let value = parseInt(quantityInput.value);
                                    if (value < maxStock) {
                                        quantityInput.value = value + 1;
                                    }
                                    syncHiddenInputs();
                                });

                                quantityInput.addEventListener('input', function() {
                                    let value = parseInt(quantityInput.value);
                                    if (isNaN(value) || value < 1) {
                                        quantityInput.value = 1;
                                    } else if (value > maxStock) {
                                        quantityInput.value = maxStock;
                                    }
                                    syncHiddenInputs();
                                });

                                function syncHiddenInputs() {
                                    if (cartQuantity) cartQuantity.value = quantityInput.value;
                                    if (hiddenQuantity) hiddenQuantity.value = quantityInput.value;
                                }
                            });
                        </script>
        <div x-show="
            ![email protected]($otherProducts).some(p =>
                search === '' ||
                '{{ strtolower($p->name) }}'.includes(search.toLowerCase()) ||
                '{{ strtolower($p->description) }}'.includes(search.toLowerCase())
            )
        " class="text-center text-gray-500 mt-6 text-sm">
            No products found.
        </div>
    </div>
    @endif
    <div x-show="fly"
     x-transition
     :style="flyStyle"
     x-cloak>
    <img :src="flySrc" class="fly-img" />

@endsection

@push('scripts')<script>
function handleFollowLike(type, profileId, already) {
    let url, method;
    if(type === 'follow') {
        url = already ? `/profiles/${profileId}/unfollow` : `/profiles/${profileId}/follow`;
        method = already ? 'DELETE' : 'POST';
    } else {
        url = already ? `/profiles/${profileId}/unlike` : `/profiles/${profileId}/like`;
        method = already ? 'DELETE' : 'POST';
    }
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(res => {
        if(res.ok) {
            location.reload();
        } else {
            alert('Action failed. Please try again.');
        }
    })
    .catch(() => {
        alert('Action failed. Please try again.');
    });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
                        const newForm = document.createElement('form');
                        newForm.method = 'POST';
                        newForm.action = this.action.replace('follow', 'unfollow');
                        newForm.className = 'inline unfollow-form';
                        newForm.setAttribute('data-id', this.getAttribute('data-id'));
                        newForm.innerHTML = `
                            <input type="hidden" name="_token" value="${this.querySelector('[name=_token]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="px-5 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Unfollow
                            </button>
                        `;
                        this.parentNode.replaceChild(newForm, this);
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
                        const newForm = document.createElement('form');
                        newForm.method = 'POST';
                        newForm.action = this.action.replace('unfollow', 'follow');
                        newForm.className = 'inline follow-form';
                        newForm.setAttribute('data-id', this.getAttribute('data-id'));
                        newForm.innerHTML = `
                            <input type="hidden" name="_token" value="${this.querySelector('[name=_token]').value}">
                            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Follow
                            </button>
                        `;
                        this.parentNode.replaceChild(newForm, this);
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
                            const newForm = document.createElement('form');
                            newForm.method = 'POST';
                            newForm.action = this.action.replace('like', 'unlike');
                            newForm.className = 'inline unlike-form';
                            newForm.setAttribute('data-id', this.getAttribute('data-id'));
                            newForm.innerHTML = `
                                <input type="hidden" name="_token" value="${this.querySelector('[name=_token]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="px-5 py-2 bg-pink-400 hover:bg-pink-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                    Unlike
                                </button>
                            `;
                            this.parentNode.replaceChild(newForm, this);
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
                            const newForm = document.createElement('form');
                            newForm.method = 'POST';
                            newForm.action = this.action.replace('unlike', 'like');
                            newForm.className = 'inline like-form';
                            newForm.setAttribute('data-id', this.getAttribute('data-id'));
                            newForm.innerHTML = `
                                <input type="hidden" name="_token" value="${this.querySelector('[name=_token]').value}">
                                <button type="submit" class="px-5 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-bold shadow transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                    Like
                                </button>
                            `;
                            this.parentNode.replaceChild(newForm, this);
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

{{-- MEsesage --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ...existing code...

    // Chat logic
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const adminId = document.getElementById('admin-id') ? document.getElementById('admin-id').value : null;
    const noMessages = document.getElementById('no-messages');

    function fetchMessages() {
        if (!adminId) return;
        fetch(`/messages/${adminId}`)
            .then(res => res.json())
            .then(messages => {
                chatMessages.innerHTML = '';
                if (messages.length === 0) {
                    chatMessages.innerHTML = '<div class="text-gray-400 text-center text-xs mt-10">No messages yet. Start the conversation!</div>';
                } else {
                    messages.forEach(msg => {
                        const isMe = msg.sender_id == {{ auth()->id() }};
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'my-2 ' + (isMe ? 'text-right' : 'text-left');
                        msgDiv.innerHTML = `<span class="inline-block ${isMe ? 'bg-lime-100 text-green-800' : 'bg-gray-100 text-gray-800'} px-3 py-1 rounded-lg">${msg.message}</span>`;
                        chatMessages.appendChild(msgDiv);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });
    }

    if(chatForm && chatInput && chatMessages && adminId) {
        fetchMessages();

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if(chatInput.value.trim() !== '') {
                fetch(`/messages/${adminId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: chatInput.value })
                })
                .then(res => res.json())
                .then(() => {
                    chatInput.value = '';
                    fetchMessages();
                });
            }
        });

        // Optional: Poll for new messages every 5 seconds
        setInterval(fetchMessages, 5000);
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('chat-message-textarea');
    const picker = document.querySelector('emoji-picker');
    if (picker && textarea) {
        picker.addEventListener('emoji-click', event => {
            textarea.value += event.detail.unicode;
            textarea.dispatchEvent(new Event('input')); // update Alpine x-model
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Flying animation logic
    let fly = false;
    let flySrc = '';
    let flyStyle = '';

    function flyToCartAnimation() {
        const img = document.getElementById('product-image');
        const cartIcon = document.getElementById('cart-icon');
        if (!img || !cartIcon) return;

        const imgRect = img.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();

        // Calculate center of cart icon
        const cartCenterX = cartRect.left + cartRect.width / 2;
        const cartCenterY = cartRect.top + cartRect.height / 2;

        // Create flying image
        const flyImg = document.createElement('img');
        flyImg.src = img.src;
        flyImg.className = 'fly-img';
        flyImg.style.position = 'fixed';
        flyImg.style.left = imgRect.left + 'px';
        flyImg.style.top = imgRect.top + 'px';
        flyImg.style.width = imgRect.width + 'px';
        flyImg.style.height = imgRect.height + 'px';
        flyImg.style.zIndex = 1000;
        flyImg.style.borderRadius = '50%';
        flyImg.style.boxShadow = '0 8px 24px rgba(76,175,80,0.25)';
        flyImg.style.pointerEvents = 'none';
        flyImg.style.transition = 'left 0.7s cubic-bezier(.22,1,.36,1), top 0.7s cubic-bezier(.22,1,.36,1), transform 0.7s, opacity 0.7s, box-shadow 0.7s';
        flyImg.style.opacity = 1;

        document.body.appendChild(flyImg);

        setTimeout(() => {
            // Move to center of cart icon
            flyImg.style.left = (cartCenterX - imgRect.width * 0.15) + 'px';
            flyImg.style.top = (cartCenterY - imgRect.height * 0.15) + 'px';
            flyImg.style.transform = 'scale(0.3) rotate(-25deg)';
            flyImg.style.opacity = 0;
            flyImg.style.boxShadow = '0 2px 8px rgba(76,175,80,0.12)';
        }, 50);

        setTimeout(() => {
            flyImg.remove();
            cartIcon.classList.add('animate-bounce');
            setTimeout(() => cartIcon.classList.remove('animate-bounce'), 400);
        }, 800);
    }

    // Attach to Add to Cart form
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            flyToCartAnimation();
        });
    }
});
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