@extends('user.layout')

@section('content')
<div class="w-full min-h-screen bg-white rounded shadow p-6 flex flex-col md:flex-row gap-8">
    <!-- Sidebar/Profile Info (Left Side) -->
    <div class="w-full md:w-1/4 md:border-r md:pr-6 mb-6 md:mb-0 flex flex-col items-center">
        <form action="{{ route('user.profile.uploadPhoto') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center w-full">
            @csrf
            <label for="profile_photo" class="cursor-pointer">
                <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('agri-profile.png') }}"
                     class="w-24 h-24 rounded-full border-2 border-green-400 object-cover mb-2 hover:opacity-80 transition" alt="Profile">
            </label>
            <input type="file" id="profile_photo" name="profile_photo" class="hidden" onchange="this.form.submit()">
            <span class="text-xs text-gray-500 mb-2">Click image to change</span>
        </form>
        <div class="font-semibold text-lg mb-1">{{ $user->username ?? $user->name }}</div>
        <a href="#" class="text-xs text-gray-500 hover:underline flex items-center gap-1 mb-4">
            <i class="bi bi-pencil"></i> Edit Profile
        </a>
        <ul class="space-y-2 w-full">
            <li>
                <a href="{{ route('user.myprofile') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded font-medium
                   {{ request()->routeIs('user.myprofile') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="bi bi-person"></i> My Account
                </a>
            </li>
            <li>
                <a href="{{ route('user.orders') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded font-medium
                   {{ request()->routeIs('user.mypurchases') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="bi bi-clipboard"></i> My Purchase
                </a>
            </li>
            <li>
                <a href="{{ route('user.bookings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded font-medium text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-bell"></i> My Booking
                </a>
            </li>
        
        </ul>
    </div>
    <!-- Main Content: Editable Profile Fields -->
    <div class="flex-1 flex flex-col justify-center">
    <h2 class="text-2xl font-bold mb-6">My Profile</h2>
    <p>Manage and protect your account</p>
    <form action="{{ route('userprofile.update') }}" method="POST" class="space-y-5 w-full max-w-3xl mx-auto">
            @csrf
            <!-- Group 1: Username, Name, Email -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}"
                        class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                </div>
            </div>
            <!-- Group 2: Phone, Gender, Date of Birth -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Gender</label>
                    <select name="gender" class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">-</option>
                        <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                        class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                </div>
            </div>
            <!-- Group 3: Address -->
            <div>
                <label class="block text-gray-600 font-medium mb-1">(Street/House No.)</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}"
                    class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
            </div>
            <!-- Group 4: Region, Province, City, Barangay -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Region</label>
                    <select name="region" id="region" class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Select Region</option>
                        @if(old('region', $user->region))
                            <option value="{{ old('region', $user->region) }}" selected>{{ old('region', $user->region) }}</option>
                        @endif
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Province</label>
                    <select name="province" id="province" class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Select Province</option>
                        @if(old('province', $user->province))
                            <option value="{{ old('province', $user->province) }}" selected>{{ old('province', $user->province) }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Municipality/City</label>
                    <select name="city" id="city" class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Select City/Municipality</option>
                        @if(old('city', $user->city))
                            <option value="{{ old('city', $user->city) }}" selected>{{ old('city', $user->city) }}</option>
                        @endif
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-600 font-medium mb-1">Barangay</label>
                    <select name="barangay" id="barangay" class="w-full bg-gray-50 border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Select Barangay</option>
                        @if(old('barangay', $user->barangay))
                            <option value="{{ old('barangay', $user->barangay) }}" selected>{{ old('barangay', $user->barangay) }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded shadow">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
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
                    regionSelect.add(opt);
                });
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
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            response2.data.forEach(province => {
                                let opt = document.createElement('option');
                                opt.value = province.name;
                                opt.text = province.name;
                                provinceSelect.add(opt);
                            });
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
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === provinceSelect.value);
                            if (!province) return;
                            // Cities
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    response3.data.forEach(city => {
                                        let opt = document.createElement('option');
                                        opt.value = city.name;
                                        opt.text = city.name + " (City)";
                                        citySelect.add(opt);
                                    });
                                })
                                .finally(() => {
                                    // Municipalities
                                    axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                        .then(response4 => {
                                            response4.data.forEach(mun => {
                                                let opt = document.createElement('option');
                                                opt.value = mun.name;
                                                opt.text = mun.name + " (Municipality)";
                                                citySelect.add(opt);
                                            });
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
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === provinceSelect.value);
                            if (!province) return;
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    let city = response3.data.find(c => c.name === citySelect.value.replace(' (City)', ''));
                                    if (city) {
                                        axios.get(`https://psgc.gitlab.io/api/cities/${city.code}/barangays/`)
                                            .then(response4 => {
                                                response4.data.forEach(barangay => {
                                                    let opt = document.createElement('option');
                                                    opt.value = barangay.name;
                                                    opt.text = barangay.name;
                                                    barangaySelect.add(opt);
                                                });
                                            });
                                    } else {
                                        // Try as municipality
                                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                            .then(response5 => {
                                                let mun = response5.data.find(m => m.name === citySelect.value.replace(' (Municipality)', ''));
                                                if (mun) {
                                                    axios.get(`https://psgc.gitlab.io/api/municipalities/${mun.code}/barangays/`)
                                                        .then(response6 => {
                                                            response6.data.forEach(barangay => {
                                                                let opt = document.createElement('option');
                                                                opt.value = barangay.name;
                                                                opt.text = barangay.name;
                                                                barangaySelect.add(opt);
                                                            });
                                                        });
                                                }
                                            });
                                    }
                                });
                        });
                });
        };
    });
    </script>
@endsection