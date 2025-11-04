@extends('user.layout')

@section('content')


<div class="pt-4">
    <div class="flex items-center justify-between mb-10 mt-4 w-full px-6">
        <!-- Left: Arrow + Label -->
        <div class="flex items-center gap-2">
            <span class="text-4xl font-extrabold text-green-900 tracking-tight drop-shadow-lg">Learning Site for Agriculture Profiles</span>
        </div>
        <!-- Right: Search Bar -->
    <div class="relative w-full max-w-xl ml-4">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            </span>
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search by Address, Farm Owner, or Farm Name..." 
                class="w-full border border-green-300 rounded-lg p-3 pl-10 focus:outline-none focus:ring-2 focus:ring-green-600 shadow"
            />
        </div>
    </div>

    <div class="w-full px-4 mt-10 relative">
        <div id="profile-carousel" class="w-full relative overflow-hidden rounded-2xl shadow-xl border border-green-200 bg-gradient-to-br from-green-50 to-white">

            @foreach ($profiles as $index => $profile)
                @php
                    $profileId = 'profile-' . $profile->id;
                    $photos = $profile->farm_photos ?? [$profile->farm_photo];
                    $mainPhoto = $photos[0];
                @endphp

                <div 
                    class="profile-card absolute inset-0 transition-opacity duration-700 ease-in-out cursor-pointer {{ $index === 0 ? 'opacity-100 relative' : 'opacity-0 pointer-events-none' }} w-full"
                    id="{{ $profileId }}"
                    style="background-image: url('{{ asset('storage/' . $mainPhoto) }}'); background-size: cover; background-position: center;"
                    data-main-photo="{{ asset('storage/' . $mainPhoto) }}"
                    data-url="{{ route('user.profiles.show', $profile->id) }}"
                    data-address="{{ strtolower($profile->region) }}, {{ strtolower($profile->province) }}, {{ strtolower($profile->city) }}, {{ strtolower($profile->barangay) }}"
                    data-farm-owner="{{ strtolower($profile->farm_owner) }}"
                    data-farm-name="{{ strtolower($profile->farm_name) }}"
                >
                    <!-- Glassmorphism Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-black/60 to-green-900/40 backdrop-blur-md"></div>
                    <!-- Inner content -->
                    <div class="relative z-10 p-8 profile-content text-white">
                        <h2 class="text-4xl font-extrabold text-center mb-6 drop-shadow-lg">{{ $profile->farm_name }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                @if($profile->profile_photo)
                                    <div class="mb-4 flex justify-center">
                                        <img src="{{ asset('storage/' . $profile->profile_photo) }}" class="rounded-lg w-36 h-36 object-cover border-4 border-green-200 shadow-lg">
                                    </div>
                                @endif
                                <p class="mb-1"><strong>Name:</strong> {{ $profile->farm_owner }}</p>
                                <p class="mb-1"><strong>Address:</strong> {{ $profile->address }} {{ $profile->barangay }} {{ $profile->city }} {{ $profile->province }}, {{ $profile->region }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $profile->phone_number }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $profile->email }}</p>
                                
                            </div>
                            <div>
                                <p class="mb-2"><strong>Description:</strong> {{ $profile->description }}</p>
                                @if($profile->certificate)
                                    <div class="mb-2">
                                        <p class="font-semibold">Certificate</p>
                                        <a href="{{ asset('storage/' . $profile->certificate) }}" target="_blank" class="underline hover:text-green-200">View Certificate</a>
                                    </div>
                                @endif
                                @if($profile->documentary)
                                    <div>
                                        <p class="font-semibold">Documentary Requirements</p>
                                        <a href="{{ asset('storage/' . $profile->documentary) }}" target="_blank" class="underline hover:text-green-200">View File</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Bottom-right overlay for stats and buttons -->
                    <div class="absolute bottom-4 right-4 z-20 flex items-center gap-3 bg-white/90 rounded-lg px-4 py-2 shadow text-green-900 text-sm font-semibold">
                        <!-- Followers -->
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-green-700" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                            <span class="followers-count">{{ \App\Models\ProfileFollower::where('profile_id', $profile->id)->count() }}</span>
                            @if(isset($followed) && in_array($profile->id, $followed))
                                <button class="ml-1 px-2 py-0.5 bg-green-300 text-green-700 rounded text-xs font-bold transition cursor-not-allowed" disabled>Followed</button>
                            @else
                                <form method="POST" action="{{ route('user.profiles.follow', $profile->id) }}" class="inline follow-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    <button type="submit" class="ml-1 px-2 py-0.5 bg-green-100 hover:bg-green-200 text-green-700 rounded text-xs font-bold transition">Follow</button>
                                </form>
                            @endif
                        </div>
                        <!-- Rating -->
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.382 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.382-2.455a1 1 0 00-1.175 0l-3.382 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z"/></svg>
                            <span>{{ $profile->average_rating ?? '0.0' }}</span>
                        </div>
                        <!-- Likes -->
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                            <span class="likes-count">{{ \App\Models\ProfileLike::where('profile_id', $profile->id)->count() }}</span>
                            @if(isset($liked) && in_array($profile->id, $liked))
                                <button class="ml-1 px-2 py-0.5 bg-pink-300 text-pink-700 rounded text-xs font-bold transition cursor-not-allowed" disabled>Liked</button>
                            @else
                                <form method="POST" action="{{ route('user.profiles.like', $profile->id) }}" class="inline like-form" data-id="{{ $profile->id }}">
                                    @csrf
                                    <button type="submit" class="ml-1 px-2 py-0.5 bg-pink-100 hover:bg-pink-200 text-pink-600 rounded text-xs font-bold transition">Like</button>
                                </form>
                            @endif
                        </div>
                        <!-- Visit Button -->
                        <a href="{{ route('user.profiles.show', $profile->id) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow font-bold text-sm transition ml-2">
                            Visit
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- Carousel navigation -->
            <button id="prevProfile" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-green-600 hover:text-white text-green-700 p-3 rounded-full shadow-lg border border-green-200 transition z-20 flex items-center justify-center text-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="nextProfile" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-green-600 hover:text-white text-green-700 p-3 rounded-full shadow-lg border border-green-200 transition z-20 flex items-center justify-center text-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Top Likes & Top Followers Side by Side --}}
    @if(isset($topLikes) || isset($topFollowers))
    <div class="max-w-6xl mx-auto mt-16 mb-16">
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Top Likes Left --}}
            @if(isset($topLikes))
            <div class="w-full md:w-1/2">
                <h2 class="text-2xl font-bold text-pink-700 mb-4">Top Likes</h2>
                <div class="bg-white rounded-xl shadow p-4">
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($topLikes as $profile)
                            <div class="flex items-center gap-4 bg-pink-50 rounded-lg p-4 shadow-sm">
                                <img src="{{ $profile->profile_photo ? asset('storage/' . $profile->profile_photo) : asset('images/default-profile.png') }}"
                                     alt="Profile Photo"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-pink-300">
                                <div class="flex-1">
                                    <div class="font-bold text-pink-700 text-lg">{{ $profile->farm_name }}</div>
                                    <div class="text-sm text-gray-600">{{ $profile->likes_count }} Likes</div>
                                </div>
                                <a href="{{ route('user.profiles.show', $profile->id) }}"
                                   class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $topLikes->links() }}
                    </div>
                </div>
            </div>
            @endif
            {{-- Top Followers Right --}}
            @if(isset($topFollowers))
            <div class="w-full md:w-1/2">
                <h2 class="text-2xl font-bold text-green-800 mb-4">Top Followers</h2>
                <div class="bg-white rounded-xl shadow p-4">
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($topFollowers as $profile)
                            <div class="flex items-center gap-4 bg-green-50 rounded-lg p-4 shadow-sm">
                                <img src="{{ $profile->profile_photo ? asset('storage/' . $profile->profile_photo) : asset('images/default-profile.png') }}"
                                     alt="Profile Photo"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-green-300">
                                <div class="flex-1">
                                    <div class="font-bold text-green-800 text-lg">{{ $profile->farm_name }}</div>
                                    <div class="text-sm text-gray-600">{{ $profile->followers_count }} Followers</div>
                                </div>
                                <a href="{{ route('user.profiles.show', $profile->id) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $topFollowers->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profiles = document.querySelectorAll('.profile-card');
        let currentIndex = 0;

        function showProfile(index) {
            const visibleProfiles = Array.from(profiles).filter(p => !p.classList.contains('hidden'));
            if (visibleProfiles.length === 0) return;
            if (index >= visibleProfiles.length) currentIndex = 0;
            if (index < 0) currentIndex = visibleProfiles.length - 1;

            visibleProfiles.forEach((profile, i) => {
                if(i === currentIndex) {
                    profile.classList.remove('opacity-0', 'pointer-events-none', 'absolute');
                    profile.classList.add('opacity-100', 'relative');
                } else {
                    profile.classList.add('opacity-0', 'pointer-events-none', 'absolute');
                    profile.classList.remove('opacity-100', 'relative');
                }
            });
        }


        // Arrow navigation (always visible)
        document.getElementById('nextProfile').addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex++;
            showProfile(currentIndex);
            resetAutoSlide();
        });

        document.getElementById('prevProfile').addEventListener('click', (e) => {
            e.stopPropagation();
            currentIndex--;
            showProfile(currentIndex);
            resetAutoSlide();
        });

        // Auto-slide (train style)
        let autoSlideInterval = null;
        function startAutoSlide() {
            autoSlideInterval = setInterval(() => {
                currentIndex++;
                showProfile(currentIndex);
            }, 3500); // 3.5 seconds per slide
        }
        function resetAutoSlide() {
            if (autoSlideInterval) clearInterval(autoSlideInterval);
            startAutoSlide();
        }
        startAutoSlide();

        profiles.forEach(profile => {
            profile.addEventListener('click', (e) => {
                if (e.target.tagName.toLowerCase() !== 'a' && e.target.tagName.toLowerCase() !== 'button' && e.target.type !== 'submit') {
                    const url = profile.getAttribute('data-url');
                    if (url) {
                        window.location.href = url;
                    }
                }
            });
        });

        // Search filter
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase().trim();
                profiles.forEach(profile => {
                    const address = (profile.getAttribute('data-address') || '').toLowerCase();
                    const farmOwner = (profile.getAttribute('data-farm-owner') || '').toLowerCase();
                    const farmName = (profile.getAttribute('data-farm-name') || '').toLowerCase();
                    if (
                        address.includes(query) ||
                        farmOwner.includes(query) ||
                        farmName.includes(query) ||
                        query === ''
                    ) {
                        profile.classList.remove('hidden');
                    } else {
                        profile.classList.add('hidden');
                    }
                });
                currentIndex = 0;
                showProfile(currentIndex);
            });

        showProfile(currentIndex);

        // AJAX for Follow
        document.querySelectorAll('.follow-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const profileId = this.getAttribute('data-id');
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
                        this.parentElement.querySelector('.followers-count').textContent = data.followers;
                        // Replace the form with a disabled "Followed" button
                        this.outerHTML = '<button class="ml-1 px-2 py-0.5 bg-green-300 text-green-700 rounded text-xs font-bold transition cursor-not-allowed" disabled>Followed</button>';
                    }
                });
            });
        });

        // AJAX for Like
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const profileId = this.getAttribute('data-id');
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
                        this.parentElement.querySelector('.likes-count').textContent = data.likes;
                        // Replace the form with a disabled "Liked" button
                        this.outerHTML = '<button class="ml-1 px-2 py-0.5 bg-pink-300 text-pink-700 rounded text-xs font-bold transition cursor-not-allowed" disabled>Liked</button>';
                    }
                });
            });
        });

    });
</script>
@endpush