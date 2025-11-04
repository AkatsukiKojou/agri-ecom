<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Farm Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-100 via-mint-100 to-green-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-3xl mx-auto p-8 bg-white/90 rounded-3xl shadow-2xl border border-green-300">
        <h1 class="text-3xl font-extrabold text-green-800 mb-8 flex items-center gap-2 justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M6.343 17.657l-1.414 1.414M17.657 17.657l-1.414-1.414M6.343 6.343L4.929 4.929"></path>
            </svg>
            Create Your Farm Profile
        </h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profiles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Farm Name <span class="text-red-500">*</span></label>
                    <input type="text" name="farm_name" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Farm Owner <span class="text-red-500">*</span></label>
                    <input type="text" name="farm_owner" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                </div>

                {{-- Location Dropdowns --}}
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Region <span class="text-red-500">*</span></label>
                    <select id="region" name="region" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                        <option value="">Select Region</option>
                    </select>
                </div>
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Province <span class="text-red-500">*</span></label>
                    <select id="province" name="province" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                        <option value="">Select Province</option>
                    </select>
                </div>
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Municipality/City <span class="text-red-500">*</span></label>
                    <select id="city" name="city" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                        <option value="">Select Municipality/City</option>
                    </select>
                </div>
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Barangay <span class="text-red-500">*</span></label>
                    <select id="barangay" name="barangay" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                        <option value="">Select Barangay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Street / House No</label>
                    <input type="text" name="address" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" placeholder="Street name, house/building number">
                </div>
<br>
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Phone Number</label>
                    <input type="text" name="phone_number" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                </div>
            </div>

            <div>
                <label class="block text-green-700 font-semibold mb-2">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-green-700 font-semibold mb-2">Profile Photo/Logo</label>
                    <input type="file" name="profile_photo" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" accept="image/*">
                    <span class="text-xs text-gray-500">Optional. JPG, PNG, or GIF.</span>
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Certificate <span class="text-red-500">*</span></label>
                    <input type="file" name="certificate" required class="w-full border-2 border-green-200 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" accept="application/pdf,image/*">
                    <span class="text-xs text-gray-500">PDF or image file.</span>
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Farm Photo <span class="text-red-500">*</span></label>
                    <input type="file" name="farm_photo" required class="w-full border-2 border-green-200 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" accept="image/*">
                    <span class="text-xs text-gray-500">JPG, PNG, or GIF.</span>
                </div>

                <div>
                    <label class="block text-green-700 font-semibold mb-2">Documentary Requirements</label>
                    <input type="file" name="documentary" class="w-full border-2 border-green-200 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" accept="application/pdf,image/*">
                    <span class="text-xs text-gray-500">Optional. PDF or image file.</span>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white px-8 py-3 rounded-xl shadow-lg font-bold text-lg transition-all duration-200">
                    <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Profile
                </button>
            </div>
        </form>
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
</body>
</html>