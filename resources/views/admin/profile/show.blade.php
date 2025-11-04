@extends('admin.layout')

@section('content')
<!-- Alpine.js for dropdown show/hide -->
<script src="//unpkg.com/alpinejs" defer></script>
<div class="max-w-6xl mx-auto mt-8 px-6 py-8 bg-white rounded-2xl shadow border border-gray-100">

    @if(session('success'))
        <div class="mb-6 p-3 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(!$profile)
        <div class="py-8 text-center text-red-600">
            <p class="font-semibold">No profile found. Please <a href="{{ route('profiles.create') }}" class="text-green-700 underline">create your profile</a>.</p>
        </div>
    @else
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-green-800">My Profile</h1>
                <p class="text-xs text-gray-500">Manage your farm profile, gallery, and location</p>
            </div>
          
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Profile Card -->
            <aside class="lg:col-span-4 bg-green-50 rounded-lg p-6">
                <div class="flex flex-col items-center">
                    <div class="w-36 h-36 rounded-full overflow-hidden border-2 border-green-200 shadow-sm bg-white flex items-center justify-center mb-4 relative group">
                        <form action="{{ route('profiles.updatePhoto', $profile->id) }}" method="POST" enctype="multipart/form-data" class="w-full h-full absolute inset-0 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                            @csrf
                            @method('PATCH')
                            <label class="w-full h-full flex items-center justify-center cursor-pointer">
                                <input type="file" name="profile_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                                <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded absolute bottom-2 left-1/2 transform -translate-x-1/2 opacity-80">Change Photo</span>
                            </label>
                        </form>
                        @if($profile->profile_photo)
                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile Photo" class="object-cover w-full h-full">
                        @else
                            <span class="text-gray-400 text-4xl">👤</span>
                        @endif
                    </div>
                    <h2 class="text-lg font-bold text-green-900 text-center">{{ $profile->farm_owner }}</h2>
                    <p class="text-sm text-gray-700 text-center">{{ $profile->farm_name }}</p>
                </div>

                <div class="mt-4 text-sm text-left space-y-3">
                    <div x-data="{ editPhone: false, editEmail: false }" class="text-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2a2 2 0 012 2v2a1 1 0 01-.293.707L7.414 9.414a16.001 16.001 0 006.172 6.172l1.707-1.707A1 1 0 0116 13h2a2 2 0 012 2v2a2 2 0 01-2 2h-1a18 18 0 01-12-12V5z"/></svg>

                            <template x-if="!editPhone">
                                <div class="flex items-center gap-3">
                                    <a :href="`tel:${'{{ $profile->phone_number }}'}`" class="text-green-700">{{ $profile->phone_number ?? '-' }}</a>
                                    <button @click.prevent="editPhone = true" class="text-xs text-green-600">Edit</button>
                                </div>
                            </template>

                            <template x-if="editPhone">
                                <form x-cloak method="POST" action="{{ route('profiles.updatePhone', $profile->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="tel" name="phone_number" value="{{ $profile->phone_number }}" class="border rounded px-2 py-1 text-xs w-40" required>
                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded">Save</button>
                                    <button type="button" @click.prevent="editPhone = false" class="px-2 py-1 text-xs">Cancel</button>
                                </form>
                            </template>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>

                            <template x-if="!editEmail">
                                <div class="flex items-center gap-3">
                                    <a :href="`mailto:${'{{ $profile->email }}'}`" class="text-green-700">{{ $profile->email }}</a>
                                    <button @click.prevent="editEmail = true" class="text-xs text-green-600">Edit</button>
                                </div>
                            </template>

                            <template x-if="editEmail">
                                <form x-cloak method="POST" action="{{ route('profiles.updateEmail', $profile->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="email" name="email" value="{{ $profile->email }}" class="border rounded px-2 py-1 text-xs w-48" required>
                                    <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded">Save</button>
                                    <button type="button" @click.prevent="editEmail = false" class="px-2 py-1 text-xs">Cancel</button>
                                </form>
                            </template>
                        </div>
                    </div>

                    <div x-data="{ showEdit: false }" class="pt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Address</span>
                            <button @click="showEdit = !showEdit" class="text-xs text-green-600">Edit</button>
                        </div>
                        <div class="text-sm text-gray-800 mt-1" x-show="!showEdit">{{ $profile->address }}, {{ $profile->barangay }}, {{ $profile->city }}, {{ $profile->province }}, {{ $profile->region }}</div>
                        <form x-show="showEdit" x-cloak action="{{ route('profiles.updateAddress', $profile->id) }}" method="POST" class="mt-2 space-y-2">
                            @csrf
                            @method('PATCH')
                            @if($errors->any())
                                <div class="text-sm text-red-600">
                                    <ul class="list-disc ml-4">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="flex gap-2 flex-wrap">
                                <input type="text" name="address" value="{{ $profile->address }}" placeholder="Street/House No." class="border rounded px-2 py-1 text-xs w-48" required>
                                <select name="region" id="region" class="border rounded px-2 py-1 text-xs w-40" required>
                                    <option value="">Region</option>
                                    @if($profile->region)
                                        <option value="{{ $profile->region }}" selected>{{ $profile->region }}</option>
                                    @endif
                                </select>
                                <select name="province" id="province" class="border rounded px-2 py-1 text-xs w-40" required>
                                    <option value="">Province</option>
                                    @if($profile->province)
                                        <option value="{{ $profile->province }}" selected>{{ $profile->province }}</option>
                                    @endif
                                </select>
                                <select name="city" id="city" class="border rounded px-2 py-1 text-xs w-40" required>
                                    <option value="">City</option>
                                    @if($profile->city)
                                        <option value="{{ $profile->city }}" selected>{{ $profile->city }}</option>
                                    @endif
                                </select>
                                <select name="barangay" id="barangay" class="border rounded px-2 py-1 text-xs w-40" required>
                                    <option value="">Barangay</option>
                                    @if($profile->barangay)
                                        <option value="{{ $profile->barangay }}" selected>{{ $profile->barangay }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded">Save</button>
                                <button type="button" @click="showEdit = false" class="px-3 py-1 text-xs">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="flex gap-2 mt-3">
                        @if($profile->certificate)
                            <a href="{{ asset('storage/' . $profile->certificate) }}" target="_blank" class="px-2 py-1 bg-white border rounded text-xs">Certificate</a>
                        @endif
                        @if($profile->documentary)
                            <a href="{{ asset('storage/' . $profile->documentary) }}" target="_blank" class="px-2 py-1 bg-white border rounded text-xs">Documentary</a>
                        @endif
                    </div>

                    <form action="{{ route('profiles.uploadGcashQr', $profile->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <label class="inline-block bg-blue-600 text-white px-3 py-1 rounded text-xs cursor-pointer">Upload GCash QR
                            <input type="file" name="gcash_qr" accept="image/*" class="hidden" onchange="this.form.submit()" required>
                        </label>
                    </form>


                    @if($profile->gcash_qr)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $profile->gcash_qr) }}" alt="GCash QR" class="h-28 w-28 object-contain border rounded shadow bg-white">
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded">Organic Certified</span>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded">Award-winning</span>
                    </div>
                   
                </div>
            </aside>

            <!-- Main content -->
            <main class="lg:col-span-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="p-4 bg-white rounded shadow text-center">
                        <div class="text-lg font-bold text-green-700">{{ $products_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Products</div>
                    </div>
                    <div class="p-4 bg-white rounded shadow text-center">
                        <div class="text-lg font-bold text-green-700">{{ $training_services_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Training Services</div>
                    </div>
                    <div class="p-4 bg-white rounded shadow text-center">
                        <div class="text-lg font-bold text-yellow-500">★ {{ $average_rating ?? '0.0' }}</div>
                        <div class="text-xs text-gray-500">Avg. Rating</div>
                    </div>
                    <div class="p-4 bg-white rounded shadow text-center followers-card cursor-pointer" role="button" aria-pressed="false" title="View followers">
                        <div class="text-lg font-bold text-blue-600">{{ $followers_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Followers</div>
                    </div>
                    <div class="p-4 bg-white rounded shadow text-center likers-card cursor-pointer" role="button" aria-pressed="false" title="View likers">
                        <div class="text-lg font-bold text-pink-600">{{ $likes_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Likes</div>
                    </div>
                </div>
        
                <section class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-semibold text-green-700 mb-2">Description</h3>
                    <div class="text-sm text-gray-700 whitespace-pre-line">{{ $profile->description }}</div>
                </section>

                <section class="bg-white p-4 rounded shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-green-700">Farm Gallery <span class="text-xs text-gray-400">(max 6)</span></h3>
                        @if(count($galleryImages ?? []) < 6)
                            <form action="{{ route('profiles.gallery.add', $profile->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="cursor-pointer text-xs text-green-700">Add Photo <input type="file" name="gallery_photo" class="hidden" accept="image/*" onchange="this.form.submit()"></label>
                            </form>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-2">
                            {{-- Farm Photo --}}
                            @if($profile->profile_photo)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $profile->profile_photo) }}" class="h-32 w-full object-cover rounded border-2 border-green-400" alt="Farm Photo">
                                    <span class="absolute top-1 left-1 bg-green-600 text-white text-xs px-2 py-0.5 rounded">Profile Photo</span>
                                </div>
                            @endif
                            {{-- Certificate --}}
                            @if($profile->certificate)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $profile->certificate) }}" class="h-32 w-full object-cover rounded border-2 border-yellow-400" alt="Certificate">
                                    <span class="absolute top-1 left-1 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded">Certificate</span>
                                </div>
                            @endif
                             @if($profile->farm_photo)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $profile->farm_photo) }}" class="h-32 w-full object-cover rounded border-2 border-yellow-400" alt="Certificate">
                                    <span class="absolute top-1 left-1 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded">Farm Photo</span>
                                </div>
                            @endif
                        @foreach($galleryImages ?? [] as $img)
                            <img src="{{ asset('storage/' . $img) }}" class="h-32 w-full object-cover rounded" alt="Gallery Image">
                            
                        @endforeach
                    </div>
                </section>

                @if($profile->map_link)
                <section class="bg-white p-4 rounded shadow">
                    <h3 class="text-sm font-semibold text-green-700 mb-2">Farm Location Map</h3>
                    <div class="rounded overflow-hidden border">
                        <iframe src="{{ $profile->map_link }}" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </section>
                @endif
            </main>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const regionSelect = document.getElementById('region');
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');

    // Populate regions
    axios.get('https://psgc.gitlab.io/api/regions/')
        .then(response => {
            response.data.forEach(region => {
                let opt = document.createElement('option');
                opt.value = region.name;
                opt.text = region.name;
                opt.dataset.code = region.code;
                regionSelect.add(opt);
            });
            // Set selected region if exists
            @if($profile->region)
                let regionOption = Array.from(regionSelect.options).find(opt => opt.value === @json($profile->region));
                if(regionOption) regionOption.selected = true;
            @endif
        });

    regionSelect.onchange = function() {
        provinceSelect.length = 1;
        citySelect.length = 1;
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(https://psgc.gitlab.io/api/regions/${region.code}/provinces/)
                    .then(response2 => {
                        response2.data.forEach(province => {
                            let opt = document.createElement('option');
                            opt.value = province.name;
                            opt.text = province.name;
                            opt.dataset.code = province.code;
                            provinceSelect.add(opt);
                        });
                        // Set selected province if exists
                        @if($profile->province)
                            let provinceOption = Array.from(provinceSelect.options).find(opt => opt.value === @json($profile->province));
                            if(provinceOption) provinceOption.selected = true;
                        @endif
                    });
            });
    };

    provinceSelect.onchange = function() {
        citySelect.length = 1;
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(https://psgc.gitlab.io/api/regions/${region.code}/provinces/)
                    .then(response2 => {
                        let province = response2.data.find(p => p.name === provinceSelect.value);
                        if (!province) return;
                        // Cities
                        axios.get(https://psgc.gitlab.io/api/provinces/${province.code}/cities/)
                            .then(response3 => {
                                response3.data.forEach(city => {
                                    let opt = document.createElement('option');
                                    opt.value = city.name;
                                    opt.text = city.name + " (City)";
                                    opt.dataset.code = city.code;
                                    citySelect.add(opt);
                                });
                            })
                            .finally(() => {
                                // Municipalities
                                axios.get(https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/)
                                    .then(response4 => {
                                        response4.data.forEach(mun => {
                                            let opt = document.createElement('option');
                                            opt.value = mun.name;
                                            opt.text = mun.name + " (Municipality)";
                                            opt.dataset.code = mun.code;
                                            citySelect.add(opt);
                                        });
                                        // Set selected city if exists
                                        @if($profile->city)
                                            let cityOption = Array.from(citySelect.options).find(opt => opt.value === @json($profile->city));
                                            if(cityOption) cityOption.selected = true;
                                        @endif
                                    });
                            });
                    });
            });
    };

    citySelect.onchange = function() {
        barangaySelect.length = 1;
        if (!this.value) return;
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                let region = response.data.find(r => r.name === regionSelect.value);
                if (!region) return;
                axios.get(https://psgc.gitlab.io/api/regions/${region.code}/provinces/)
                    .then(response2 => {
                        let province = response2.data.find(p => p.name === provinceSelect.value);
                        if (!province) return;
                        axios.get(https://psgc.gitlab.io/api/provinces/${province.code}/cities/)
                            .then(response3 => {
                                let city = response3.data.find(c => c.name === citySelect.value.replace(' (City)', ''));
                                if (city) {
                                    axios.get(https://psgc.gitlab.io/api/cities/${city.code}/barangays/)
                                        .then(response4 => {
                                            response4.data.forEach(barangay => {
                                                let opt = document.createElement('option');
                                                opt.value = barangay.name;
                                                opt.text = barangay.name;
                                                barangaySelect.add(opt);
                                            });
                                            // Set selected barangay if exists
                                            @if($profile->barangay)
                                                let barangayOption = Array.from(barangaySelect.options).find(opt => opt.value === @json($profile->barangay));
                                                if(barangayOption) barangayOption.selected = true;
                                            @endif
                                        });
                                } else {
                                    // Try as municipality
                                    axios.get(https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/)
                                        .then(response5 => {
                                            let mun = response5.data.find(m => m.name === citySelect.value.replace(' (Municipality)', ''));
                                            if (mun) {
                                                axios.get(https://psgc.gitlab.io/api/municipalities/${mun.code}/barangays/)
                                                    .then(response6 => {
                                                        response6.data.forEach(barangay => {
                                                            let opt = document.createElement('option');
                                                            opt.value = barangay.name;
                                                            opt.text = barangay.name;
                                                            barangaySelect.add(opt);
                                                        });
                                                        // Set selected barangay if exists
                                                        @if($profile->barangay)
                                                            let barangayOption = Array.from(barangaySelect.options).find(opt => opt.value === @json($profile->barangay));
                                                            if(barangayOption) barangayOption.selected = true;
                                                        @endif
                                                    });
                                            }
                                        });
                                }
                            });
                    });
            });
    };

    // Auto-trigger change for pre-selected values
    @if($profile->region)
        setTimeout(() => { regionSelect.dispatchEvent(new Event('change')); }, 500);
    @endif
    @if($profile->province)
        setTimeout(() => { provinceSelect.dispatchEvent(new Event('change')); }, 1000);
    @endif
    @if($profile->city)
        setTimeout(() => { citySelect.dispatchEvent(new Event('change')); }, 1500);
    @endif
});
</script>
<!-- Followers Modal -->
<div id="followers-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-11/12 max-w-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-lg">Followers</h3>
            <button id="followers-modal-close" class="text-gray-500">✕</button>
        </div>
        <div id="followers-list" class="flex flex-wrap gap-3 max-h-80 overflow-auto">
            @if(!empty($followersList) && count($followersList) > 0)
                @foreach($followersList as $f)
                    <div class="w-full sm:w-1/2 md:w-1/3 flex items-center gap-3 p-2 bg-gray-50 rounded">
                        <img src="{{ $f['photo_url'] ?? asset('/images/default-avatar.png') }}" alt="{{ $f['name'] ?? 'User' }}" class="w-10 h-10 rounded-full object-cover border">
                        <div class="text-sm text-gray-800">{{ $f['name'] ?? 'User' }}</div>
                    </div>
                @endforeach
            @else
                <div class="text-sm text-gray-500">No followers yet.</div>
            @endif
        </div>
        <div class="mt-3 text-right">
            <button id="followers-modal-close-2" class="px-3 py-1 bg-gray-100 rounded">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const followersCard = document.querySelector('.followers-card');
    const followersModal = document.getElementById('followers-modal');
    const followersListEl = document.getElementById('followers-list');
    const followersClose = document.getElementById('followers-modal-close');
    const followersClose2 = document.getElementById('followers-modal-close-2');

    // Data passed from controller
    const followersData = @json($followersList ?? []);
    console.log('followersData', followersData);

    function openModal() {
        followersModal.classList.remove('hidden');
        followersModal.classList.add('flex');
    }

    function closeModal() {
        followersModal.classList.add('hidden');
        followersModal.classList.remove('flex');
    }

    if (followersCard) followersCard.addEventListener('click', openModal);
    if (followersClose) followersClose.addEventListener('click', closeModal);
    if (followersClose2) followersClose2.addEventListener('click', closeModal);
    followersModal.addEventListener('click', function (e) {
        if (e.target === followersModal) closeModal();
    });
});
</script>
<!-- Likers Modal -->
<div id="likers-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-11/12 max-w-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-lg">Likes</h3>
            <button id="likers-modal-close" class="text-gray-500">✕</button>
        </div>
        <div id="likers-list" class="flex flex-wrap gap-3 max-h-80 overflow-auto">
            @if(!empty($likesList) && count($likesList) > 0)
                @foreach($likesList as $f)
                    <div class="w-full sm:w-1/2 md:w-1/3 flex items-center gap-3 p-2 bg-gray-50 rounded">
                        <img src="{{ $f['photo_url'] ?? asset('/images/default-avatar.png') }}" alt="{{ $f['name'] ?? 'User' }}" class="w-10 h-10 rounded-full object-cover border">
                        <div class="text-sm text-gray-800">{{ $f['name'] ?? 'User' }}</div>
                    </div>
                @endforeach
            @else
                <div class="text-sm text-gray-500">No likes yet.</div>
            @endif
        </div>
        <div class="mt-3 text-right">
            <button id="likers-modal-close-2" class="px-3 py-1 bg-gray-100 rounded">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const likersCard = document.querySelector('.likers-card');
    const likersModal = document.getElementById('likers-modal');
    const likersListEl = document.getElementById('likers-list');
    const likersClose = document.getElementById('likers-modal-close');
    const likersClose2 = document.getElementById('likers-modal-close-2');

    const likesData = @json($likesList ?? []);
    console.log('likesData', likesData);

    function openModal() {
        likersModal.classList.remove('hidden');
        likersModal.classList.add('flex');
    }

    function closeModal() {
        likersModal.classList.add('hidden');
        likersModal.classList.remove('flex');
    }

    if (likersCard) likersCard.addEventListener('click', openModal);
    if (likersClose) likersClose.addEventListener('click', closeModal);
    if (likersClose2) likersClose2.addEventListener('click', closeModal);
    likersModal.addEventListener('click', function (e) {
        if (e.target === likersModal) closeModal();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followBtn = document.getElementById('follow-btn');
    const likeBtn = document.getElementById('like-btn');
    const followersCountEl = document.getElementById('followers-count');
    const likesCountEl = document.getElementById('likes-count');

    const profileId = @json($profile->id);

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    }

    if (followBtn) {
        followBtn.addEventListener('click', function () {
            // Determine current state
            const following = followBtn.textContent.trim().toLowerCase() === 'following';
            if (!following) {
                // follow
                fetch({{ url('/profiles') }}/${profileId}/follow, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                }).then(r => r.json()).then(json => {
                    if (json.success) {
                        followBtn.textContent = 'Following';
                        followBtn.classList.remove('bg-green-600','text-white');
                        followBtn.classList.add('bg-gray-200','text-gray-700');
                        if (followersCountEl) followersCountEl.textContent = json.followers;
                    }
                });
            } else {
                // unfollow
                fetch({{ url('/profiles') }}/${profileId}/unfollow, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                }).then(r => r.json()).then(json => {
                    if (json.success) {
                        followBtn.textContent = 'Follow';
                        followBtn.classList.remove('bg-gray-200','text-gray-700');
                        followBtn.classList.add('bg-green-600','text-white');
                        if (followersCountEl) followersCountEl.textContent = json.followers;
                    }
                });
            }
        });
    }

    if (likeBtn) {
        likeBtn.addEventListener('click', function () {
            const liked = likeBtn.textContent.trim().toLowerCase() === 'liked';
            if (!liked) {
                fetch({{ url('/profiles') }}/${profileId}/like, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                }).then(r => r.json()).then(json => {
                    if (json.success) {
                        likeBtn.textContent = 'Liked';
                        likeBtn.classList.remove('bg-pink-600','text-white');
                        likeBtn.classList.add('bg-pink-100','text-pink-700');
                        if (likesCountEl) likesCountEl.textContent = json.likes;
                    }
                });
            } else {
                fetch({{ url('/profiles') }}/${profileId}/unlike, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                }).then(r => r.json()).then(json => {
                    if (json.success) {
                        likeBtn.textContent = 'Like';
                        likeBtn.classList.remove('bg-pink-100','text-pink-700');
                        likeBtn.classList.add('bg-pink-600','text-white');
                        if (likesCountEl) likesCountEl.textContent = json.likes;
                    }
                });
            }
        });
    }
});
</script>
@endsection