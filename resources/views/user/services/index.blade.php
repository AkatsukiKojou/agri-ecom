@extends('user.layout')

@section('title', 'Services')

@section('content')
<br>
<div class="min-h-screen bg-white pt-0.5 pb-8">
<div class="w-full mx-0 p-0">


    <!-- Search, Filter, Sort, Bookings -->
    <div class="mb-8 w-full flex flex-col gap-3">
    <div class="w-full flex flex-col md:flex-row md:items-center md:justify-center gap-6">
        <!-- Hidden typeFilter select for JS compatibility -->
        <select id="typeFilter" style="display:none;">
            <option value="">All Types</option>
        </select>
            <!-- Left: Filters -->
            <div class="flex flex-wrap gap-2 items-center w-full md:w-1/4 justify-center">
                <div class="flex flex-col md:flex-row gap-2 w-full">
                    <div class="relative w-40 md:w-44">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </span>
                        <select id="sortFilter" class="w-full border border-green-300 rounded-lg p-3 pl-10 focus:outline-none focus:ring-2 focus:ring-green-600 shadow text-sm">
                            <option value="">Sort By</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="newest">Newest</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Center: Search (Product-style) -->
            <div class="flex justify-center w-full md:w-1/3">
                <div class="relative w-full max-w-xl">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </span>
                    <input 
                        type="text" 
                        id="searchInput" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search by Service Name, Farmname, Location..." 
                        class="w-full border border-green-300 rounded-lg p-3 pl-10 focus:outline-none focus:ring-2 focus:ring-green-600 shadow text-sm text-center" 
                    />
                </div>
            </div>
            <!-- Right: Bookings Button -->
           <div class="flex justify-center w-full md:w-1/4 md:justify-end md:pr-8 mt-1">
    <a href="{{ route('user.bookings.index') }}"
       class="relative text-green-800 font-extrabold text-lg hover:text-lime-600 flex items-center gap-2 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>Bookings</span>
    </a>
</div>
        </div>
    </div>


    {{-- <!-- Category Filter Bar -->

    <div class="flex flex-wrap justify-center gap-3 mb-8">
        @php
            $categories = [
                'All',
                'Accommodations',
                'Campsite',
                'Catering',
                'Crop Monitoring',
                'Techno Demo/Field Tour',
                'Training Hall',
            ];
            $selectedCategory = isset($category) ? $category : request('category');
        @endphp
        @php
            $categoryColors = [
                'All' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Accommodations' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Campsite' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Catering' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Crop Monitoring' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Techno Demo/Field Tour' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
                'Training Hall' => 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200',
            ];
        @endphp
        @foreach($categories as $cat)
            <a href="?category={{ urlencode($cat) }}"
               class="px-4 py-2 rounded-full border font-semibold transition-all duration-200 shadow-sm {{ $categoryColors[$cat] ?? 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200' }} {{ ($selectedCategory === $cat || (!$selectedCategory && $cat === 'All')) ? 'ring-2 ring-green-500 scale-105' : '' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div> --}}

    <h1 class="text-4xl font-extrabold text-center mb-10 text-green-900 tracking-tight drop-shadow-lg">Explore Our Services</h1>

    <!-- Services Grid -->
    <div id="servicesGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 px-4 md:px-12 lg:px-20">
    @php $hasServices = count($services) > 0; @endphp
    @forelse ($services as $service)
            <div 
                class="bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-[1.03] cursor-pointer flex flex-col border border-green-100 ring-1 ring-green-100 hover:ring-green-300 group relative overflow-hidden"
                data-type="{{ $service->service_name }}" data-price="{{ $service->price }}" data-date="{{ $service->created_at }}"
                data-barangay="{{ $service->admin && $service->admin->profile ? strtolower($service->admin->profile->barangay ?? '') : '' }}"
                data-city="{{ $service->admin && $service->admin->profile ? strtolower($service->admin->profile->city ?? '') : '' }}"
                data-province="{{ $service->admin && $service->admin->profile ? strtolower($service->admin->profile->province ?? '') : '' }}"
                data-region="{{ $service->admin && $service->admin->profile ? strtolower($service->admin->profile->region ?? '') : '' }}"
                onclick="window.location='{{ url('/user/services/' . $service->id) }}'">

                <!-- Service Image -->
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

                <div class="w-full h-28 rounded-2xl overflow-hidden mb-3 mt-3 bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center relative">
                    <!-- Favorite/Bookmark -->
                    <button class="absolute top-2 right-2 z-10 bg-white/80 rounded-full p-1 favorite-btn" title="Add to favorites" onclick="event.stopPropagation(); this.classList.toggle('text-red-500');">
                        <i class="bi bi-heart"></i>
                    </button>
                    @if(!empty($images) && isset($images[0]) && $images[0])
                        <img src="{{ asset('storage/' . ltrim($images[0], '/')) }}" alt="{{ $service->service_name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition" />
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


                <!-- Service Name -->
                <h2 class="text-lg font-extrabold text-green-800 mb-1 text-center drop-shadow flex items-center justify-center gap-2"
                    style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                    {{ $service->service_name }}
                    @if($loop->first)
                        <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full">New</span>
                    @elseif($loop->iteration % 3 == 0)
                        <span class="bg-pink-200 text-pink-800 text-xs font-bold px-2 py-0.5 rounded-full">Popular</span>
                    @endif
                </h2>
              
                <!-- Short Description -->
                <p class="text-gray-700 mb-2 text-xs sm:text-sm leading-snug min-h-[2.5rem] text-justify px-2">
                    {{ \Illuminate\Support\Str::words($service->description, 14, '...') }}
                </p>

                <!-- Price and Unit -->
                <div class="flex flex-col items-center gap-1 mb-2 justify-center">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-green-700 text-lg drop-shadow">₱{{ number_format($service->price, 2) }}</span>
                        <span class="text-gray-500 text-xs font-semibold bg-green-50 px-2 py-0.5 rounded-full">/ {{ $service->unit }}</span>
                    </div>
                    <!-- Start time and end time removed as requested -->
                </div>

                <!-- Durarion & Location -->
                <div class="flex flex-col gap-1 mb-1 text-xs text-gray-600 text-center">
                    <span>Duration
                        <i class="bi bi-hourglass-split"></i>:
                        <span class="font-bold">{{ $service->duration ?? '-' }}</span>
                    </span>
                    <span>
                        <i class="bi bi-geo-alt-fill text-blue-700" style="font-size:1.1em;"></i>
                            <span class="align-middle">
                                    @php
                                        $profile = $service->admin->profile ?? null;
                                        $location = 'N/A';
                                        if ($profile) {
                                            $location = $profile->barangay . ', ' . $profile->city . ', ' . $profile->province;
                                            if (!empty($profile->region)) {
                                                $location .= ', ' . $profile->region;
                                            }
                                        }
                                    @endphp
                                    {{ $location }}
                            </span>
                    </span>
                </div>
                <!-- Ratings/Reviews (mockup) -->
                <div class="flex items-center justify-center gap-1 mb-2">
                    <span class="text-yellow-400">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                        <i class="bi bi-star"></i>
                    </span>
                    <span class="text-xs text-gray-500">4.5 (12 reviews)</span>
                </div>

                <!-- Created By with Admin Photo -->
                <div class="flex items-center gap-2 mb-4 justify-center">
                    @php
                        $adminPhoto = null;
                        $debugProfilePhotoPath = $service->admin && $service->admin->profile ? $service->admin->profile->profile_photo : 'NO PROFILE PHOTO';
                        if ($service->admin && $service->admin->profile && $service->admin->profile->profile_photo) {
                            $adminPhoto = asset('storage/' . ltrim($service->admin->profile->profile_photo, '/'));
                        }
                    @endphp
                    <img src="{{ $adminPhoto ?? asset('default-profile.png') }}" alt="Admin Photo"
                        class="w-8 h-8 rounded-full object-cover border-2 border-green-200 shadow"
                        onerror="this.onerror=null; this.src='{{ asset('default-profile.png') }}';" />
                    <span class="text-xs text-gray-500"><span class="font-medium text-gray-700">
                        {{ $service->admin && $service->admin->profile && $service->admin->profile->farm_owner ? $service->admin->profile->farm_owner : 'Unknown' }}
                    </span></span>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-auto px-2 pb-2">
                        <button
                            class="flex-1 bg-lime-600 hover:bg-lime-700 text-white font-semibold py-2 rounded-lg shadow transition text-sm flex items-center justify-center gap-1 inquire-btn"
                            style="min-width: 0;"
                            onclick="event.stopPropagation(); openChatModal({
                                serviceId: '{{ $service->id }}',
                                serviceName: '{{ $service->service_name }}',
                                serviceImage: '{{ asset('storage/' . ltrim($images[0] ?? '', '/')) }}',
                                serviceType: '{{ $service->service_name }}',
                                servicePrice: '{{ $service->price }}',
                                adminId: '{{ $service->admin ? $service->admin->id : '' }}',
                                adminProfileId: '{{ $service->admin && $service->admin->profile ? $service->admin->profile->id : '' }}',
                                adminName: '{{ $service->admin && $service->admin->profile && $service->admin->profile->farm_owner ? $service->admin->profile->farm_owner : 'Unknown' }}',
                                adminPhoto: '{{ $service->admin && $service->admin->profile && $service->admin->profile->profile_photo ? asset('storage/' . ltrim($service->admin->profile->profile_photo, '/')) : asset('default-profile.png') }}'
                            });">
                            <i class="bi bi-chat-dots"></i> Inquire
                        </button>
                    <button
                        class="flex-1 bg-green-800 hover:bg-green-900 text-white font-semibold py-2 rounded-lg shadow transition text-sm flex items-center justify-center gap-1"
                        style="min-width: 0;"
                        onclick="event.stopPropagation(); window.location='{{ url('/user/services/' . $service->id) }}';">
                        <i class="bi bi-calendar-check"></i> Reserve
                    </button>
                </div>
            </div>
    @empty
    @endforelse
        <div id="noServicesMsg" class="flex flex-col items-center justify-center col-span-full mt-6" style="display: {{ $hasServices ? 'none' : '' }};">
            <p class="text-gray-500 text-lg">No Training Services found.</p>
        </div>
</div>
</script>
<script>
// Robust realtime search, filter, and sort for services
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const sortFilter = document.getElementById('sortFilter');
    const grid = document.getElementById('servicesGrid');
    const noMsg = document.getElementById('noServicesMsg');
    if (!searchInput || !typeFilter || !sortFilter || !grid) return;

    function filterAndSort() {
        const val = searchInput.value.trim().toLowerCase();
        const typeVal = typeFilter.value;
        const sortVal = sortFilter.value;
        let cards = Array.from(grid.querySelectorAll('div.bg-white'));
        let anyVisible = false;

        // Filter
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            const type = card.getAttribute('data-type');
            const barangay = card.getAttribute('data-barangay') || '';
            const city = card.getAttribute('data-city') || '';
            const province = card.getAttribute('data-province') || '';
            const region = card.getAttribute('data-region') || '';
            const serviceName = card.querySelector('h2') ? card.querySelector('h2').textContent.toLowerCase() : '';
            let show = true;
            if (val && !(text.includes(val)
                || barangay.includes(val)
                || city.includes(val)
                || province.includes(val)
                || region.includes(val)
                || serviceName.includes(val))) show = false;
            // If 'All Types' is selected, show all
            if (typeVal && type !== typeVal) show = false;
            card.style.display = show ? '' : 'none';
            if (show) anyVisible = true;
        });

        // Sort
        if (sortVal && anyVisible) {
            let visibleCards = cards.filter(card => card.style.display !== 'none');
            if (sortVal === 'price-asc') {
                visibleCards.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
            } else if (sortVal === 'price-desc') {
                visibleCards.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
            } else if (sortVal === 'newest') {
                visibleCards.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
            }
            visibleCards.forEach(card => grid.appendChild(card));
        }

        // Show/hide empty state
        if (noMsg) {
            noMsg.style.display = anyVisible ? 'none' : '';
        }
    }

    searchInput.addEventListener('input', filterAndSort);
    typeFilter.addEventListener('change', filterAndSort);
    sortFilter.addEventListener('change', filterAndSort);
    // Initial filter on page load
    filterAndSort();
});
</script>
    </div>
</div>
@endsection


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
            adminPhoto: '',
            adminProfileId: '',
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
            openBox(serviceId, serviceName, serviceImage, serviceType, servicePrice, adminId, adminProfileId, adminName, adminPhoto) {
                this.openChat = true;
                this.minimized = false;
                this.serviceId = serviceId;
                this.serviceName = serviceName;
                this.serviceImage = serviceImage;
                this.serviceType = serviceType;
                this.servicePrice = servicePrice;
                this.adminId = adminId;
                this.adminProfileId = adminProfileId;
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
    x-init="window.openChatModal = (opts) => openBox(opts.serviceId, opts.serviceName, opts.serviceImage, opts.serviceType, opts.servicePrice, opts.adminId, opts.adminProfileId, opts.adminName, opts.adminPhoto)"
    >
        <div
            x-show="openChat"
            x-transition
            :style="minimized 
                ? 'width:3rem;height:6rem;bottom:1rem;right:2.5rem;padding:2;overflow:hidden;' 
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
                <div class="flex flex-col h-[26rem] w-[22rem] rounded-xl overflow-hidden bg-white">
                    <!-- Header -->
                    <div class="flex flex-col">
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
                                        <a :href="'/user/profiles/' + adminProfileId" class="block px-4 py-2 hover:bg-green-100 text-sm">
                                            <i class="bi bi-person-circle"></i> View Profile
                                        </a>
                                        <form method="POST" action="#">
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
                        <!-- Service Name/Type -->
                        <div class="bg-green-50 text-green-800 px-4 py-2 text-sm font-semibold border-b border-green-100">
                            <span>Inquiring about: </span>
                            <span x-text="serviceName"></span>
                        </div>
                    </div>
                    {{-- <!-- Service Info Card (above messages) -->
                    <div class="p-4 bg-green-50 rounded-lg mb-2 flex items-center gap-4" x-show="serviceImage || serviceName || servicePrice">
                        <img :src="serviceImage" alt="Service Image" class="w-16 h-16 object-cover rounded-lg border border-green-200" x-show="serviceImage">
                        <div class="flex flex-col gap-1">
                            <span class="font-bold text-green-800 text-lg" x-text="serviceName"></span>
                            <span class="text-sm text-gray-700" x-text="serviceType"></span>
                            <span class="text-green-700 font-semibold">₱<span x-text="servicePrice"></span></span>
                        </div>
                        <button type="button" @click="message = 'Service: ' + serviceName + '\nType: ' + serviceType + '\nPrice: ₱' + servicePrice; sendMessage();" class="ml-auto bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded shadow text-sm">Send Service Info</button>
                    </div> --}}
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

    <script>
    function openChatModal({serviceId, serviceName, adminId, adminName}) {
        document.getElementById('chatServiceId').value = serviceId;
        document.getElementById('chatAdminId').value = adminId;
        document.getElementById('chatServiceName').textContent = serviceName;
        document.getElementById('chatAdminName').textContent = adminName;
        document.getElementById('chatMessage').value = '';
        document.getElementById('chatStatus').textContent = '';
        document.getElementById('chatModal').classList.remove('hidden');
    }
    function closeChatModal() {
        document.getElementById('chatModal').classList.add('hidden');
    }
    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const serviceId = document.getElementById('chatServiceId').value;
        const adminId = document.getElementById('chatAdminId').value;
        const message = document.getElementById('chatMessage').value.trim();
        if (!message) {
            document.getElementById('chatStatus').textContent = 'Please enter a message.';
            return;
        }
        // Send AJAX request to backend (adjust route as needed)
        fetch('/user/services/inquire', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ service_id: serviceId, admin_id: adminId, message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('chatStatus').textContent = 'Inquiry sent successfully!';
                setTimeout(closeChatModal, 1500);
            } else {
                document.getElementById('chatStatus').textContent = data.error || 'Failed to send inquiry.';
            }
        })
        .catch(() => {
            document.getElementById('chatStatus').textContent = 'Error sending inquiry.';
        });
    });
    </script>