<script>
document.addEventListener('DOMContentLoaded', function () {
    // GCash modal address dropdowns
    const regionSelect = document.getElementById('gcash_region');
    const provinceSelect = document.getElementById('gcash_province');
    const citySelect = document.getElementById('gcash_city');
    const barangaySelect = document.getElementById('gcash_barangay');

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
                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities-municipalities/`)
                            .then(response3 => {
                                response3.data.forEach(city => {
                                    let opt = document.createElement('option');
                                    opt.value = city.name;
                                    opt.text = city.name;
                                    citySelect.add(opt);
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
                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities-municipalities/`)
                            .then(response3 => {
                                let city = response3.data.find(c => c.name === citySelect.value);
                                if (!city) return;
                                axios.get(`https://psgc.gitlab.io/api/cities-municipalities/${city.code}/barangays/`)
                                    .then(response4 => {
                                        response4.data.forEach(barangay => {
                                            let opt = document.createElement('option');
                                            opt.value = barangay.name;
                                            opt.text = barangay.name;
                                            barangaySelect.add(opt);
                                        });
                                    });
                            });
                    });
            });
    };
});
</script>
@extends('user.layout')

@section('title', $service->service_name)

@section('content')
<br><br>
<div class="max-w-6xl mx-auto px-2 py-8 space-y-10">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        <!-- LEFT: Service Info (Redesigned) -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-green-100 flex flex-col gap-4">
            <!-- Service Name & Type -->
            <div class="flex flex-col gap-1 items-start">
                <div class="text-green-700 text-3xl font-extrabold leading-tight">{{ $service->service_name ?? '' }}</div>
                <div class="flex items-center gap-2">
                    
                    @if(now()->diffInDays($service->created_at) < 14)
                        <span class="bg-green-200 text-green-800 text-xs font-bold px-2 py-0.5 rounded-full shadow">New</span>
                    @endif
                    <span class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full shadow">Popular</span>
                </div>
            </div>

            <!-- Description (above image) -->
            @php
                $desc = $service->description ?: 'NA';
                $words = str_word_count(strip_tags($desc), 2);
                $wordCount = count($words);
                $first20 = $wordCount > 20 ? array_slice($words, 0, 20, true) : $words;
                $first20Text = implode(' ', $first20);
            @endphp
            <div class="mt-2">
                <h2 class="text-base font-bold text-green-800 mb-1 flex items-center gap-1 border-b border-green-100 pb-1"><i class="bi bi-info-circle-fill"></i> Description</h2>
                <p class="text-gray-800 text-sm leading-relaxed text-center lg:text-left" id="descShort">
                    {{ $first20Text }}@if($wordCount > 20)... <a href="#" id="seeMoreDesc" class="text-green-600 underline">See more</a>@endif
                </p>
                @if($wordCount > 20)
                <p class="text-gray-800 text-sm leading-relaxed text-center lg:text-left hidden" id="descFull">{{ $desc }} <a href="#" id="seeLessDesc" class="text-green-600 underline">See less</a></p>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var seeMore = document.getElementById('seeMoreDesc');
                        var seeLess = document.getElementById('seeLessDesc');
                        var short = document.getElementById('descShort');
                        var full = document.getElementById('descFull');
                        if(seeMore) seeMore.onclick = function(e){ e.preventDefault(); short.style.display='none'; full.style.display='block'; };
                        if(seeLess) seeLess.onclick = function(e){ e.preventDefault(); full.style.display='none'; short.style.display='block'; };
                    });
                </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const regionSelect = document.getElementById('gcash_region');
        const provinceSelect = document.getElementById('gcash_province');
        const citySelect = document.getElementById('gcash_city');
        const barangaySelect = document.getElementById('gcash_barangay');

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
                <script>
                </script>
                @endif
            </div>

            <!-- Gallery/Carousel -->
            <div class="w-full h-44 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center shadow relative border border-green-100">
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
                @if(!empty($images) && count($images) > 1)
                    <div id="gallery" class="w-full h-full relative">
                        <img id="galleryImg" src="{{ asset('storage/' . ltrim($images[0], '/')) }}" alt="{{ $service->service_name }}" class="w-full h-full object-cover transition-all duration-300 ease-in-out rounded-xl" />
                        <button type="button" id="prevImg" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-green-100"><i class="bi bi-chevron-left text-xl"></i></button>
                        <button type="button" id="nextImg" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-green-100"><i class="bi bi-chevron-right text-xl"></i></button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const images = @json(array_map(fn($img) => asset('storage/' . ltrim($img, '/')), $images));
                            let idx = 0;
                            const img = document.getElementById('galleryImg');
                            document.getElementById('prevImg').onclick = function(e) { e.stopPropagation(); idx = (idx - 1 + images.length) % images.length; img.src = images[idx]; };
                            document.getElementById('nextImg').onclick = function(e) { e.stopPropagation(); idx = (idx + 1) % images.length; img.src = images[idx]; };
                        });
                    </script>
                    @elseif(!empty($images) && isset($images[0]) && $images[0])
                    <img src="{{ asset('storage/' . ltrim($images[0], '/')) }}" alt="{{ $service->service_name }}" class="w-full h-full object-cover transition-all duration-300 ease-in-out hover:scale-105 rounded-xl" />
                @else
                    <img src="{{ asset('default-service.png') }}" alt="No Image" class="w-full h-full object-cover transition-all duration-300 ease-in-out hover:scale-105 rounded-xl" />
                @endif
            </div>

            <!-- Ratings/Reviews -->
            <div class="flex items-center gap-2 mb-2 justify-center text-sm">
                <span class="text-yellow-400 flex items-center">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                    <i class="bi bi-star"></i>
                </span>
                <span class="text-xs text-gray-500">4.5 (12 reviews)</span>
            </div>

            <!-- Price, Unit, Duration -->
            <div class="bg-gradient-to-r from-green-50 to-lime-100 px-4 py-2 rounded-xl shadow-inner flex flex-col items-center border border-green-100">
                <span class="text-xl font-extrabold text-green-800 tracking-wide">
                    ₱{{ number_format($service->price, 2) }}
                </span>
                <span class="text-gray-600 text-xs font-normal">/ per {{ $service->unit ?? 'Wala pa' }}</span>
                <span class="text-xs text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded-full mt-1 flex items-center gap-1">
                    <i class="bi bi-hourglass-split"></i>
                    {{ $service->duration ?? 'Wala pa' }}
                </span>
            </div>

               <!-- Schedule, Duration, Location, Contact, Map -->
            <div class="bg-white rounded-2xl shadow-lg p-5 mb-8 border border-gray-100">
                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-green-700"><i class="bi bi-clock-history mr-1"></i>Start Time:</span>
                                    <span class="text-gray-700">
                                        {{ $service->start_time ? (\Carbon\Carbon::parse($service->start_time)->format('h:i A')) : 'Wala pa' }}
                                    </span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-green-700"><i class="bi bi-hourglass-split mr-1"></i>Duration:</span>
                    <span class="text-gray-700">{{ $service->duration ?? 'NA' }}</span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-green-700"><i class="bi bi-geo-alt-fill mr-1"></i>Location:</span>
                    <span class="text-gray-700">
                        @php
                            $profile = $service->admin->profile ?? null;
                            $region = trim($profile->region ?? '');
                            $province = trim($profile->province ?? '');
                            $municipality = trim($profile->city ?? '');
                            $barangay = trim($profile->barangay ?? '');
                             $address = trim($profile->address ?? '');
                            $parts = array_filter([$address ,$barangay, $municipality, $province, $region], function($v) { return !empty($v); });
                            $locationString = implode(', ', $parts);
                        @endphp
                        {{ $locationString ?: 'NA' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-green-700"><i class="bi bi-person-fill mr-1"></i>Contact Person:</span>
                    <span class="text-gray-700">
                        {{ $service->admin->profile->farm_owner ?? 'NA' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="font-semibold text-green-700"><i class="bi bi-telephone-fill mr-1"></i>Contact Info:</span>
                    <span class="text-gray-700">
                        {{ $service->admin->profile->phone_number ?? 'NA' }}
                    </span>
                </div>
               
            </div>
        </div>

        <!-- RIGHT: Description + Booking Form -->
        <div class="flex flex-col gap-8">
           
          
            <!-- Reserve and Pay Card -->
            <div class="bg-gradient-to-br from-green-50 via-lime-50 to-green-100 p-8 rounded-2xl border border-green-200 shadow-xl">
                <h2 class="text-xl font-bold text-green-700 mb-4 text-center flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Reserve and Pay
                </h2>

                  @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-900 rounded-md shadow-inner text-center font-semibold text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-900 rounded-md shadow-inner text-center font-semibold text-xs">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.services.otp', $service->id) }}" method="POST" class="space-y-4 text-xs">
                    <div>
                        <label for="attendees" class="block mb-1 font-semibold text-green-800">Number of Attendees</label>
                        <input type="number" name="attendees" id="attendees" min="1" value="1" required class="w-full p-2 border border-green-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                        <label for="booking_start" class="block mb-1 font-semibold text-green-800">Start Date</label>
                        <input type="date" name="booking_start" id="booking_start"
                            class="w-full p-2 border border-green-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-400 focus:outline-none"
                            min="{{ $nextAvailableDate }}" required>
                        <div class="mt-2 text-sm text-green-700">
                            Earliest available booking date: <span class="font-bold">{{ $nextAvailableDate }}</span>
                        </div>
                   
                    </div>

                    <div class="text-center space-y-1">
                        <p class="text-green-900 font-semibold">Duration: <span id="duration">{{ $service->duration ?? 'N/A' }}</span></p>
                        <p class="text-lg font-extrabold text-green-800">Total Price: ₱<span id="totalPrice">0.00</span></p>
                        <p class="text-sm font-semibold text-green-700">Downpayment (20%): ₱<span id="downpayment">0.00</span></p>
                        <input type="hidden" name="total_price" id="total_price_hidden" value="0">
                        <input type="hidden" name="downpayment" id="downpayment_hidden" value="0">
                        <!-- Computed booking end date (auto-filled from start date + service duration) -->
                        <input type="hidden" name="booking_end" id="booking_end_hidden" value="">
                    </div>

                    <div>
                        @php
                            $qr = $service->admin && $service->admin->profile && $service->admin->profile->gcash_qr ? $service->admin->profile->gcash_qr : null;
                        @endphp
                        <label for="payment_method" class="block mb-1 font-semibold text-green-800">Payment Method</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full p-2 border border-green-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                            <option value="" disabled selected>Select a payment method</option>
                            <option value="onsite">Cash On Site</option>
                            @if($qr)
                                <option value="gcash">GCash</option>
                            @endif
                        </select>
                    </div>

                    <!-- Downloadable Brochure (if available) -->
                    @if($service->brochure ?? false)
                    <div class="mb-8 flex justify-center">
                        <a href="{{ asset('storage/' . ltrim($service->brochure, '/')) }}" download class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-full font-semibold flex items-center gap-2"><i class="bi bi-file-earmark-arrow-down"></i> Download Brochure</a>
                    </div>
                    @endif
 <button type="submit" id="reservePayBtn"
                class="w-full bg-gradient-to-r from-green-700 to-lime-600 hover:from-green-800 hover:to-lime-700 text-white font-extrabold py-2 rounded-md shadow-lg transition duration-300 text-xs flex items-center justify-center gap-2 opacity-50 cursor-not-allowed"
                disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reserve
                    </button>
            
            </form>

                    <!-- Cash On Site Modal -->
                    <div id="onsiteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-12 relative border border-green-100 flex flex-col items-center overflow-y-auto" style="max-height:90vh;">
                            <button type="button" onclick="document.getElementById('onsiteModal').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-green-700 text-2xl leading-none font-bold focus:outline-none">&times;</button>
                            <h3 class="text-xl font-bold text-green-700 mb-4">Cash On Site - Customer Info</h3>
                            <!-- Booking summary -->
                            <div class="w-full mb-8 bg-green-50 rounded-2xl p-6 text-base text-green-900 shadow-lg border border-green-200">
                                <div><span class="font-semibold">Service Name:</span> <span id="onsite_service_name_display">-</span></div>
                                <div><span class="font-semibold">Number of Attendees:</span> <span id="onsite_attendees_display">-</span></div>
                                <div><span class="font-semibold">Booking Date:</span> <span id="onsite_booking_start_display">-</span></div>
                                <div><span class="font-semibold">Duration:</span> <span id="onsite_duration_display">-</span></div>
                                 <div><span class="font-semibold">Downpayment (20%):</span> <span id="onsite_downpayment_display">-</span></div>
                            </div>
                            <form id="onsitePaymentForm" action="{{ route('user.services.otp', $service->id) }}" method="POST" class="w-full flex flex-col items-center gap-3">
                                @csrf
                                <input type="hidden" name="attendees" id="onsite_attendees">
                                <input type="hidden" name="booking_start" id="onsite_booking_start">
                                <input type="hidden" name="booking_end" id="onsite_booking_end">
                                <input type="hidden" name="payment_method" value="onsite">
                                <input type="hidden" name="total_price" id="onsite_total_price">
                                <input type="hidden" name="downpayment" id="onsite_downpayment">
                                <div class="w-full">
                                                                    <label for="downpayment_visit_date" class="block text-sm font-semibold text-green-800">Date of Downpayment Visit</label>
                                                                    <input type="date" name="downpayment_visit_date" id="downpayment_visit_date" required class="w-full p-2 border border-green-300 rounded-md shadow-sm">
                                                                    <span class="text-xs text-gray-600">* Must not be later than your booking date. Must not be earlier than today.</span>
                                                                    <script>
                                                                        document.addEventListener('DOMContentLoaded', function() {
                                                                            var downpaymentVisitDate = document.getElementById('downpayment_visit_date');
                                                                            if (downpaymentVisitDate) {
                                                                                var today = new Date();
                                                                                var yyyy = today.getFullYear();
                                                                                var mm = String(today.getMonth() + 1).padStart(2, '0');
                                                                                var dd = String(today.getDate()).padStart(2, '0');
                                                                                var minDate = yyyy + '-' + mm + '-' + dd;
                                                                                downpaymentVisitDate.min = minDate;
                                                                            }
                                                                        });
                                                                    </script>
                                </div>
                                <div class="w-full">
                                    <label for="full_name" class="block text-sm font-semibold text-green-800">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" required class="w-full p-2 border border-green-300 rounded-md shadow-sm">
                                </div>
                                <div class="w-full">
                                    <label for="phone" class="block text-sm font-semibold text-green-800">Phone Number</label>
                                <input type="text" name="phone" id="phone" required class="w-full p-2 border border-green-300 rounded-md shadow-sm" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                    <div class="w-full">
                                        <label for="email" class="block text-sm font-semibold text-green-800">Email</label>
                                        <input type="email" name="email" id="onsite_email" required class="w-full p-2 border border-green-300 rounded-md shadow-sm">
                                </div>
                                <!-- Address Dropdowns -->
                                <div class="w-full mb-2">
                                    <h2 class="text-base font-bold text-green-700 mb-2">Address</h2>
                                    <div class="flex gap-2 mb-2">
                                        <div class="flex-1 min-w-[120px]">
                                            <label for="region" class="block mb-1 font-semibold text-green-800 text-sm">Region</label>
                                            <select name="region" id="region" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                                <option value="">Select Region</option>
                                            </select>
                                        </div>
                                        <div class="flex-1 min-w-[120px]">
                                            <label for="province" class="block mb-1 font-semibold text-green-800 text-sm">Province</label>
                                            <select name="province" id="province" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                                <option value="">Select Province</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-1 min-w-[120px]">
                                            <label for="city" class="block mb-1 font-semibold text-green-800 text-sm">City/Municipality</label>
                                            <select name="city" id="city" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                                <option value="">Select City/Municipality</option>
                                            </select>
                                        </div>
                                        <div class="flex-1 min-w-[120px]">
                                            <label for="barangay" class="block mb-1 font-semibold text-green-800 text-sm">Barangay</label>
                                            <select name="barangay" id="barangay" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                                <option value="">Select Barangay</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <label for="customer_note" class="block text-sm font-semibold text-green-800">Add Note (optional)</label>
                                    <textarea name="customer_note" id="customer_note" rows="2" class="w-full p-2 border border-green-300 rounded-md shadow-sm"></textarea>
                                </div>
                                       <div class="flex items-start gap-2 mt-4">
                        <input type="checkbox" id="termsGcash" name="terms" required class="mt-1 accent-green-700">
                        <label for="termsGcash" class="text-xs text-gray-700">
                            I agree to the 
                            <a href="#" class="text-green-700 underline hover:text-green-900" id="gcashTermsLink">Terms and Conditions</a>
                            of this service booking.
                        </label>
                    </div>
                    <!-- Terms and Conditions Modal for GCash -->
                    <div id="termsModalGcash" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 relative border border-green-100">
        <h3 class="text-xl font-bold text-green-700 mb-6">Terms and Conditions</h3>
        <div class="text-base text-gray-700 max-h-64 overflow-y-auto mb-8 space-y-4">
            <ol class="list-decimal list-inside space-y-2">
                <li><strong>Payment Policy:</strong> Full payment is required on the day of service. Failure to pay on site may result in cancellation of your booking and forfeiture of any downpayment.</li>
                <li><strong>GCash Payment Requirement:</strong> If you select GCash as your payment method, you are required to pay the downpayment via GCash and upload proof of payment. Failure to do so may result in cancellation of your booking and forfeiture of any downpayment.</li>
                <li><strong>Downpayment Policy:</strong> A downpayment of 20% is required to secure your booking. Downpayment is non-refundable except in cases where the provider cancels the service.</li>
                <li><strong>Cancellation/Reschedule Policy:</strong> You may cancel or reschedule your booking up to 24 hours before the scheduled date. Cancellations made after this period may result in forfeiture of your downpayment.</li>
                <li><strong>No-Show Policy:</strong> If you do not show up on the scheduled date, your booking will be cancelled and any downpayment will be forfeited.</li>
                <li><strong>Service Guarantee:</strong> If the provider is unable to deliver the service on the agreed date, you will be notified and any downpayment will be refunded.</li>
                <li><strong>Customer Responsibilities:</strong> You must provide accurate information and ensure the service location is accessible and prepared as required.</li>
                <li><strong>Privacy Statement:</strong> Your personal information will be used only for booking and communication purposes and will be protected according to our privacy policy.</li>
                <li><strong>Contact Information:</strong> For questions or disputes, please contact our support team or the service provider directly.</li>
            </ol>
            <p class="mt-4 text-xs text-gray-500">By booking this service, you agree to these terms and conditions.</p>
        </div>
        <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-green-700 text-2xl leading-none font-bold focus:outline-none" id="gcashTermsClose">&times;</button>
    </div>
                    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dedicated script for GCash Terms modal
        var gcashTermsLink = document.getElementById('gcashTermsLink');
        var gcashTermsModal = document.getElementById('termsModalGcash');
        var gcashTermsClose = document.getElementById('gcashTermsClose');
        if (gcashTermsLink && gcashTermsModal) {
            gcashTermsLink.addEventListener('click', function(e) {
                e.preventDefault();
                gcashTermsModal.classList.remove('hidden');
            });
        }
        if (gcashTermsClose && gcashTermsModal) {
            gcashTermsClose.addEventListener('click', function() {
                gcashTermsModal.classList.add('hidden');
            });
        }
    });
    </script>

                                <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-2 rounded shadow mt-2">Submit Info</button>

                            </form>
                        </div>
                    </div>

                <!-- GCash Modal -->
                <div id="gcashModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all duration-300 hidden">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-12 relative border border-green-100 flex flex-col items-center overflow-y-auto" style="max-height:90vh;">
                        <button type="button" onclick="document.getElementById('gcashModal').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-green-700 text-2xl leading-none font-bold focus:outline-none">&times;</button>
                        <h3 class="text-xl font-bold text-green-700 mb-4">GCash Payment - Customer Info</h3>
                        @php
                            $qr = $service->admin && $service->admin->profile && $service->admin->profile->gcash_qr ? $service->admin->profile->gcash_qr : null;
                        @endphp
                        @if($qr)
                            <div class="flex flex-col items-center mb-4">
                                <span class="text-xs text-gray-600 mb-1">Scan to pay via GCash:</span>
                                <img src="{{ asset('storage/' . $qr) }}" alt="GCash QR Code" class="h-32 w-32 object-contain border rounded shadow bg-white mb-2">
                                <button type="button" onclick="window.location.href='gcash://';" class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow text-xs font-semibold">Open GCash App</button>
                            </div>
                        @else
                            <div class="text-xs text-red-500 mb-2">No GCash QR code available.</div>
                        @endif
                        <div class="mb-2 text-green-800 text-sm font-semibold">Required Downpayment: <span id="modalDownpaymentDisplay">0.00</span></div>
                        <form id="gcashPaymentForm" action="{{ route('user.services.otp', $service->id) }}" method="POST" enctype="multipart/form-data" class="w-full flex flex-col items-center gap-3">
                            <input type="hidden" name="attendees" id="modal_attendees">
                            @csrf
                            <input type="hidden" name="booking_start" id="modal_booking_start">
                            <input type="hidden" name="booking_end" id="modal_booking_end">
                            <input type="hidden" name="payment_method" value="gcash">
                            <input type="hidden" name="total_price" id="modal_total_price">
                            <input type="hidden" name="downpayment" id="modal_downpayment">
                            <!-- Booking summary for GCash modal -->
                            <div class="w-full mb-8 bg-green-50 rounded-2xl p-6 text-base text-green-900 shadow-lg border border-green-200">
                                <div><span class="font-semibold">Service Name:</span> <span id="gcash_service_name_display">-</span></div>
                                <div><span class="font-semibold">Service:</span> <span id="gcash_service_name_display">-</span></div>
                                <div><span class="font-semibold">Number of Attendees:</span> <span id="gcash_attendees_display">-</span></div>
                                <div><span class="font-semibold">Booking Date:</span> <span id="gcash_booking_start_display">-</span></div>
                                <div><span class="font-semibold">Duration:</span> <span id="gcash_duration_display">-</span></div>
                            </div>
                            <label class="block w-full">
                                <span class="text-xs font-semibold text-green-800">Upload GCash Payment Screenshot/Reference</span>
                                <input type="file" name="gcash_payment" accept="image/*" class="mt-1 block w-full border border-green-300 rounded-md shadow-sm focus:ring-2 focus:ring-green-400 focus:outline-none text-xs" required>
                            </label>
                            <div class="w-full">
                                <label for="full_name" class="block text-sm font-semibold text-green-800">Full Name</label>
                                <input type="text" name="full_name" id="full_name" required class="w-full p-2 border border-green-300 rounded-md shadow-sm">
                            </div>
                            <div class="w-full">
                                <label for="phone" class="block text-sm font-semibold text-green-800">Phone Number</label>
                                <input type="text" name="phone" id="phone" required class="w-full p-2 border border-green-300 rounded-md shadow-sm" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                            </div>
                             <div class="w-full">
                                        <label for="email" class="block text-sm font-semibold text-green-800">Email</label>
                                        <input type="email" name="email" id="onsite_email" required class="w-full p-2 border border-green-300 rounded-md shadow-sm">
                                </div>
                            <!-- Address Dropdowns -->
                            <div class="w-full mb-2">
                                <h2 class="text-base font-bold text-green-700 mb-2">Address</h2>
                                <div class="flex gap-2 mb-2">
                                    <div class="flex-1 min-w-[120px]">
                                        <label for="gcash_region" class="block mb-1 font-semibold text-green-800 text-sm">Region</label>
                                        <select name="region" id="gcash_region" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                            <option value="">Select Region</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-[120px]">
                                        <label for="gcash_province" class="block mb-1 font-semibold text-green-800 text-sm">Province</label>
                                        <select name="province" id="gcash_province" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                            <option value="">Select Province</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1 min-w-[120px]">
                                        <label for="gcash_city" class="block mb-1 font-semibold text-green-800 text-sm">City/Municipality</label>
                                        <select name="city" id="gcash_city" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                            <option value="">Select City/Municipality</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-[120px]">
                                        <label for="gcash_barangay" class="block mb-1 font-semibold text-green-800 text-sm">Barangay</label>
                                        <select name="barangay" id="gcash_barangay" class="w-full p-2 border border-green-300 rounded-md text-sm font-normal" required>
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full">
                                <label for="customer_note" class="block text-sm font-semibold text-green-800">Add Note (optional)</label>
                                <textarea name="customer_note" id="customer_note" rows="2" class="w-full p-2 border border-green-300 rounded-md shadow-sm"></textarea>
                            </div>
                            <div class="flex items-start gap-2 mt-4">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1 accent-green-700">
                        <label for="terms" class="text-xs text-gray-700">
                            I agree to the 
                            <a href="#" class="text-green-700 underline hover:text-green-900" onclick="event.preventDefault();document.getElementById('termsModal').classList.remove('hidden')">Terms and Conditions</a>
                            of this service booking.
                        </label>
                    </div>
                    <!-- Terms and Conditions Modal -->
                    <div id="termsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 relative border border-green-100">
        <h3 class="text-xl font-bold text-green-700 mb-6">Terms and Conditions</h3>
        <div class="text-base text-gray-700 max-h-64 overflow-y-auto mb-8 space-y-4">
            <ol class="list-decimal list-inside space-y-2">
                <li><strong>Payment Policy:</strong> Full payment is required on the day of service. Failure to pay on site may result in cancellation of your booking and forfeiture of any downpayment.</li>
                <li><strong>Cash On Site Requirement:</strong> If you select Cash On Site as your payment method, you are required to visit the service location on your scheduled date and pay the full amount before the service begins. Failure to do so may result in cancellation of your booking and forfeiture of any downpayment.</li>
                <li><strong>Downpayment Policy:</strong> A downpayment of 20% is required to secure your booking. Downpayment is non-refundable except in cases where the provider cancels the service.</li>
                <li><strong>Cancellation/Reschedule Policy:</strong> You may cancel or reschedule your booking up to 24 hours before the scheduled date. Cancellations made after this period may result in forfeiture of your downpayment.</li>
                <li><strong>No-Show Policy:</strong> If you do not show up on the scheduled date, your booking will be cancelled and any downpayment will be forfeited.</li>
                <li><strong>Service Guarantee:</strong> If the provider is unable to deliver the service on the agreed date, you will be notified and any downpayment will be refunded.</li>
                <li><strong>Customer Responsibilities:</strong> You must provide accurate information and ensure the service location is accessible and prepared as required.</li>
                <li><strong>Privacy Statement:</strong> Your personal information will be used only for booking and communication purposes and will be protected according to our privacy policy.</li>
                <li><strong>Contact Information:</strong> For questions or disputes, please contact our support team or the service provider directly.</li>
            </ol>
            <p class="mt-4 text-xs text-gray-500">By booking this service, you agree to these terms and conditions.</p>
        </div>
        <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-green-700 text-2xl leading-none font-bold focus:outline-none" onclick="document.getElementById('termsModal').classList.add('hidden')">&times;</button>
    </div>
                    </div>
                            <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-2 rounded shadow mt-2">Submit Payment</button>
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
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const reserveBtn = document.getElementById('reservePayBtn');
                    const paymentMethod = document.getElementById('payment_method');
                    const gcashModal = document.getElementById('gcashModal');
                    const onsiteModal = document.getElementById('onsiteModal');
                    const bookingStart = document.getElementById('booking_start');
                    const totalPrice = document.getElementById('total_price_hidden');
                    const downpayment = document.getElementById('downpayment_hidden');
                    const computedEndDate = document.getElementById('computedEndDate');
                    const modalBookingStart = document.getElementById('modal_booking_start');
                    const modalBookingEnd = document.getElementById('modal_booking_end');
                    const modalTotalPrice = document.getElementById('modal_total_price');
                    const modalDownpayment = document.getElementById('modal_downpayment');
                    const modalDownpaymentDisplay = document.getElementById('modalDownpaymentDisplay');
                    const attendees = document.getElementById('attendees');
                    const modalAttendees = document.getElementById('modal_attendees');
                    // Onsite modal fields
                    const onsiteBookingStart = document.getElementById('onsite_booking_start');
                    const onsiteBookingEnd = document.getElementById('onsite_booking_end');
                    const onsiteTotalPrice = document.getElementById('onsite_total_price');
                    const onsiteDownpayment = document.getElementById('onsite_downpayment');
                    const onsiteAttendees = document.getElementById('onsite_attendees');
                    const onsiteDownpaymentDisplay = document.getElementById('onsite_downpayment_display');

                    // Enable Reserve & Pay only if date is selected
                    function checkEnableReserveBtn() {
                        if (bookingStart.value) {
                            reserveBtn.disabled = false;
                            reserveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        } else {
                            reserveBtn.disabled = true;
                            reserveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                    bookingStart.addEventListener('change', checkEnableReserveBtn);
                    checkEnableReserveBtn();

                    reserveBtn.addEventListener('click', function(e) {
                        if(paymentMethod.value === 'gcash') {
                            // Show modal, fill hidden fields
                            gcashModal.classList.remove('hidden');
                            modalBookingStart.value = bookingStart.value;
                            if (computedEndDate && computedEndDate.textContent && computedEndDate.textContent !== '-') {
                                modalBookingEnd.value = computedEndDate.textContent;
                            }
                            modalTotalPrice.value = totalPrice.value;
                            modalDownpayment.value = downpayment.value;
                            if (modalDownpaymentDisplay && downpayment.value) {
                                modalDownpaymentDisplay.textContent = `₱${parseFloat(downpayment.value).toLocaleString(undefined, {minimumFractionDigits:2})}`;
                            }
                            // Set attendees in modal
                            if (attendees && modalAttendees) {
                                modalAttendees.value = attendees.value;
                                document.getElementById('gcash_attendees_display').textContent = attendees.value;
                            }
                            document.getElementById('gcash_booking_start_display').textContent = bookingStart.value;
                            // Set duration display
                            var durationText = document.getElementById('duration') ? document.getElementById('duration').textContent : '-';
                            document.getElementById('gcash_duration_display').textContent = durationText;
                            // Set service name and type of service
                            var serviceName = "{{ $service->service_name ?? '' }}";
                            var typeOfService = "{{ $service->service_name ?? '' }}";
                            document.getElementById('gcash_service_name_display').textContent = serviceName;
                            document.getElementById('gcash_service_name_display').textContent = typeOfService;
                            e.preventDefault();
                        } else if(paymentMethod.value === 'onsite') {
                            // Show onsite modal, fill hidden fields
                            onsiteModal.classList.remove('hidden');
                            onsiteBookingStart.value = bookingStart.value;
                            // Get computed end date from the UI (span)
                            if (computedEndDate && computedEndDate.textContent && computedEndDate.textContent !== '-') {
                                onsiteBookingEnd.value = computedEndDate.textContent;
                            }
                            onsiteTotalPrice.value = totalPrice.value;
                            onsiteDownpayment.value = downpayment.value;
                            if (attendees && onsiteAttendees) {
                                onsiteAttendees.value = attendees.value;
                                document.getElementById('onsite_attendees_display').textContent = attendees.value;
                            }
                            document.getElementById('onsite_booking_start_display').textContent = bookingStart.value;
                            // Set duration display
                            var durationText = document.getElementById('duration') ? document.getElementById('duration').textContent : '-';
                            document.getElementById('onsite_duration_display').textContent = durationText;
                            // Set service name and type of service
                            var serviceName = "{{ $service->service_name ?? '' }}";
                            var typeOfService = "{{ $service->service_name ?? '' }}";
                            document.getElementById('onsite_service_name_display').textContent = serviceName;
                            document.getElementById('onsite_service_name_display').textContent = typeOfService;
                            // Set max for downpayment visit date
                            var downpaymentVisitDate = document.getElementById('downpayment_visit_date');
                            if (downpaymentVisitDate) {
                                downpaymentVisitDate.max = bookingStart.value;
                            }
                            // Update downpayment display in modal when opening
                            if (onsiteDownpaymentDisplay && onsiteDownpayment) {
                                onsiteDownpaymentDisplay.textContent = onsiteDownpayment.value ? `₱${parseFloat(onsiteDownpayment.value).toLocaleString(undefined, {minimumFractionDigits:2})}` : '-';
                            }
                            e.preventDefault();
                        }
                    });
                });
                </script>
                </form>
            </div>
            <!-- Map Integration (OpenStreetMap + Leaflet.js) -->
                <div class="w-full bg-white rounded-2xl shadow-lg border border-green-100 p-4 mt-4 flex flex-col items-center">
                    <h2 class="text-base font-bold text-green-700 mb-3 flex items-center gap-1"><i class="bi bi-geo-alt-fill"></i> Location Map</h2>
                    <div id="osmMap" class="w-full max-w-md h-52 rounded-lg border shadow mb-2" style="position:relative;z-index:0;"></div>
                </div>
                @push('scripts')
                <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var address = @json($locationString);
                    var mapDiv = document.getElementById('osmMap');
                    if (!mapDiv) return;
                    if (!address || address.trim() === '') {
                        mapDiv.innerHTML = '<div class="text-center text-gray-400 pt-10">Location unavailable</div>';
                        return;
                    }
                    var map = L.map('osmMap').setView([13.41, 122.56], 13); // Default center (Philippines)
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    // Geocode address using Nominatim
                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                var lat = parseFloat(data[0].lat);
                                var lon = parseFloat(data[0].lon);
                                map.setView([lat, lon], 15);
                                L.marker([lat, lon]).addTo(map).bindPopup(address).openPopup();
                            } else {
                                mapDiv.innerHTML = '<div class="text-center text-gray-400 pt-10">Location not found</div>';
                            }
                        })
                        .catch(() => {
                            mapDiv.innerHTML = '<div class="text-center text-gray-400 pt-10">Map unavailable</div>';
                        });
                });
                </script>
                @endpush
            </div>
           
        </div>
        <!-- Seller Card (centered below main grid) -->
    <div class="flex justify-center w-full">
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 w-full mt-6 mb-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $service->admin->profile && $service->admin->profile->profile_photo 
                                ? asset('storage/' . $service->admin->profile->profile_photo) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($service->admin->name) }}"
                         alt="{{ $service->admin->name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-lime-200 shadow">
                    <div>
                        <div class="flex items-center flex-wrap gap-2">
                            <p class="text-lg font-semibold text-green-800">{{ $service->admin->name }}</p>
                            <span class="text-xs bg-lime-100 text-green-700 px-2 py-1 rounded">Shop Badge</span>
                        </div>
                        <a href="{{route('user.profiles.show', $service->admin->profile->id ?? '')  }}" class="text-sm text-blue-600 hover:underline cursor-pointer">Visit shop</a>
                    </div>
                </div>
                <div class="flex gap-3" x-data="{ openChat: false }">
                          <a href="{{route('user.profiles.show', $service->admin->profile->id ?? '')  }}"
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
                            adminId: '{{ $service->admin_id }}',
                            adminName: '{{ $service->admin->name }}',
                            adminPhoto: '{{ $service->admin->profile && $service->admin->profile->profile_photo ? asset('storage/' . $service->admin->profile->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($service->admin->name) }}',
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
                                                    <a href="{{route('user.profiles.show', $service->admin->profile->id ?? '')  }}"
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
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 mt-6 text-center">
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $service->admin->products ? $service->admin->products->count() : 0 }}</p>
                    <p class="text-xs text-gray-500">Products</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $service->admin->services ? $service->admin->services->count() : 0 }}</p>
                    <p class="text-xs text-gray-500">Services</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $service->admin->profile && $service->admin->profile->average_rating ? number_format($service->admin->profile->average_rating, 1) : '0.0' }}</p>
                    <p class="text-xs text-gray-500">Rating</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800 followers-count">{{ $service->admin->profile ? \App\Models\ProfileFollower::where('profile_id', $service->admin->profile->id)->count() : 0 }}</p>
                    <p class="text-xs text-gray-500">Followers</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800 likes-count">{{ $service->admin->profile ? \App\Models\ProfileLike::where('profile_id', $service->admin->profile->id)->count() : 0 }}</p>
                    <p class="text-xs text-gray-500">Likes</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">Within hours</p>
                    <p class="text-xs text-gray-500">Response Time</p>
                </div>
            </div>
            <div class="mt-6 text-gray-700">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div>
                        <p class="font-semibold">Phone:</p>
                        <p>{{ $service->admin->profile->phone_number ?? 'Not Available' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Email:</p>
                        <p>{{ $service->admin->profile->email ?? 'Not Available' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Address:</p>
                       <p>
                            {{ $service->admin->profile->region ?? 'Region not available' }},
                            {{ $service->admin->profile->barangay ?? 'Barangay not available' }} 
                            {{ $service->admin->profile->city ?? 'City not available' }},
                            {{ $service->admin->profile->province ?? 'Province not available' }}
                        </p>
                    </div>
                </div>
                @if($service->admin->profile)
        <div id="follow-like-section">
            <div class="flex gap-4 mt-6">
                @php
                    $alreadyFollowed = false;
                    $alreadyLiked = false;
                    $followersCount = \App\Models\ProfileFollower::where('profile_id', $service->admin->profile->id)->count();
                    $likesCount = \App\Models\ProfileLike::where('profile_id', $service->admin->profile->id)->count();
                    if(auth()->check()) {
                        $alreadyFollowed = \App\Models\ProfileFollower::where('profile_id', $service->admin->profile->id)
                            ->where('user_id', auth()->id())->exists();
                        $alreadyLiked = \App\Models\ProfileLike::where('profile_id', $service->admin->profile->id)
                            ->where('user_id', auth()->id())->exists();
                    }
                @endphp
                <div>
                    @if($alreadyFollowed)
                        <form method="POST" action="{{ route('user.profiles.unfollow', $service->admin->profile->id) }}" class="inline unfollow-form" data-id="{{ $service->admin->profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 016 6c0 4.418-6 10-6 10S4 12.418 4 8a6 6 0 016-6z"/></svg>
                                Unfollow
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.follow', $service->admin->profile->id) }}" class="inline follow-form" data-id="{{ $service->admin->profile->id }}">
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
                        <form method="POST" action="{{ route('user.profiles.unlike', $service->admin->profile->id) }}" class="inline unlike-form" data-id="{{ $service->admin->profile->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-5 py-2 bg-pink-400 hover:bg-pink-500 text-white rounded-lg font-bold shadow flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                Unlike
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('user.profiles.like', $service->admin->profile->id) }}" class="inline like-form" data-id="{{ $service->admin->profile->id }}">
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
           
        </div>
        
    </div>
   

    <!-- FAQ Section (collapsible) -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h2 class="text-lg font-bold text-green-700 mb-4 flex items-center gap-2 border-b border-green-100 pb-2"><i class="bi bi-question-circle-fill"></i> Frequently Asked Questions</h2>
                <div class="space-y-2" id="faqSection">
                    <button type="button" class="faq-btn w-full text-left bg-gray-50 hover:bg-green-50 px-4 py-3 rounded flex justify-between items-center font-semibold transition" >How do I book this service? <i class="bi bi-chevron-down faq-arrow transition-transform duration-300"></i></button>
                    <div class="hidden px-4 py-2 text-sm text-gray-700">To book this service, simply select your preferred dates, fill out the required booking form, and click the <span class='font-bold text-green-700
                    <button type="button" class="faq-btn w-full text-left bg-gray-50 hover:bg-green-50 px-4 py-3 rounded flex justify-between items-center font-semibold transition" >What payment methods are accepted? <i class="bi bi-chevron-down faq-arrow transition-transform duration-300"></i></button>
                    <div class="hidden px-4 py-2 text-sm text-gray-700">We accept <span class='font-bold text-green-700'>Cash On Site</span> and <span class='font-bold text-green-700'>GCash</span> as payment methods. For GCash, you may be asked to upload a payment screenshot or reference. Please ensure you follow the payment instructions provided during booking to avoid any delays in confirmation.</div>
                    <button type="button" class="faq-btn w-full text-left bg-gray-50 hover:bg-green-50 px-4 py-3 rounded flex justify-between items-center font-semibold transition" >Can I cancel my booking? <i class="bi bi-chevron-down faq-arrow transition-transform duration-300"></i></button>
                                        <div class="hidden px-4 py-2 text-sm text-gray-700">You can cancel your booking <span class="font-bold text-green-700">within 24 hours after making the booking</span> for a full refund. Cancellations made beyond this period are not allowed, and your downpayment may be forfeited. If you need to reschedule, please contact the service provider directly to discuss your options. All cancellations and changes are subject to approval and the provider's policies.</div>
                    <button type="button" class="faq-btn w-full text-left bg-gray-50 hover:bg-green-50 px-4 py-3 rounded flex justify-between items-center font-semibold transition" >How do I contact the service provider? <i class="bi bi-chevron-down faq-arrow transition-transform duration-300"></i></button>
                    <div class="hidden px-4 py-2 text-sm text-gray-700">You can reach the service provider by using the <span class='font-bold text-green-700'>Quick Inquiry</span> form below or by sending a direct message through the platform's chat feature. For urgent concerns, you may also find the provider's contact information in the service details section. Providers aim to respond promptly to all inquiries and messages.</div>
                    <button type="button" class="faq-btn w-full text-left bg-gray-50 hover:bg-green-50 px-4 py-3 rounded flex justify-between items-center font-semibold transition" >Is my personal information safe? <i class="bi bi-chevron-down faq-arrow transition-transform duration-300"></i></button>
                    <div class="hidden px-4 py-2 text-sm text-gray-700">Yes, your personal information is protected and will only be used for booking and communication purposes. We follow strict privacy policies to ensure your data is safe and will not be shared with third parties except as required by law. If you have concerns about privacy or data handling, please review our privacy policy or contact support for more details.</div>
                 </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.faq-btn').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            const answer = btn.nextElementSibling;
                            const arrow = btn.querySelector('.faq-arrow');
                            answer.classList.toggle('hidden');
                            arrow.classList.toggle('rotate-180');
                        });
                    });
                });
                </script>
            </div>
          
    </div>

   
@endsection

@push('scripts')
<!-- Map Integration (OpenStreetMap + Leaflet.js) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var address = @json($locationString);
    var map = L.map('osmMap').setView([13.41, 122.56], 13); // Default center (Philippines)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    // Geocode address using Nominatim
    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lon = parseFloat(data[0].lon);
                map.setView([lat, lon], 15);
                L.marker([lat, lon]).addTo(map).bindPopup(address).openPopup();
            } else {
                document.getElementById('osmMap').innerHTML = '<div class="text-center text-gray-400 pt-10">Map unavailable</div>';
            }
        })
        .catch(() => {
            document.getElementById('osmMap').innerHTML = '<div class="text-center text-gray-400 pt-10">Map unavailable</div>';
        });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Booking price calculation
    const bookingStart = document.getElementById('booking_start');
    const totalPriceElem = document.getElementById('totalPrice');
    const totalPriceHidden = document.getElementById('total_price_hidden');
    const downpaymentElem = document.getElementById('downpayment');
    const downpaymentHidden = document.getElementById('downpayment_hidden');
    const computedEndDate = document.getElementById('computedEndDate');
    const price = {{ $service->price }};
    const durationStr = @json($service->duration ?? '1 day');

    function parseDuration(duration) {
        duration = duration.toLowerCase();
        let days = 1;
        if(duration.includes('day')) {
            days = parseInt(duration) || 1;
        } else if(duration.includes('week')) {
            days = (parseInt(duration) || 1) * 7;
        } else if(duration.includes('month')) {
            days = (parseInt(duration) || 1) * 30;
        }
        return days;
    }

    function parseDurationNumber(duration) {
        // Extract the first integer found in the duration string, default to 1
        const m = String(duration).match(/(\d+)/);
        return m ? parseInt(m[0], 10) : 1;
    }

    function updateTotal() {
        const attendeesElem = document.getElementById('attendees');
        const attendees = attendeesElem ? parseInt(attendeesElem.value) || 1 : 1;

        // normalize service unit and allowed duration-like units
        const serviceUnit = @json(strtolower($service->unit ?? ''));
        const allowedUnits = ['session', 'day', 'seminar', 'training', 'program'];

        if (bookingStart.value) {
            // Total = price * attendees (ignore duration in total calculation)
            const total = price * attendees;
            const downpayment = total * 0.20;
            totalPriceElem.textContent = total.toFixed(2);
            downpaymentElem.textContent = downpayment.toFixed(2);
            totalPriceHidden.value = total.toFixed(2);
            downpaymentHidden.value = downpayment.toFixed(2);

            // Only compute booking_end when the service unit is a duration-like unit
            if (allowedUnits.includes(serviceUnit)) {
                const durationDays = parseDuration(durationStr);
                const startDate = new Date(bookingStart.value);
                if (!isNaN(startDate.getTime())) {
                    const endDate = new Date(startDate);
                    // If duration is 1 day, endDate = startDate (no plus)
                    endDate.setDate(startDate.getDate() + durationDays - 1);
                    computedEndDate.textContent = endDate.toLocaleDateString();

                    // Format yyyy-mm-dd for form submission
                    const y = endDate.getFullYear();
                    const m = String(endDate.getMonth() + 1).padStart(2, '0');
                    const d = String(endDate.getDate()).padStart(2, '0');
                    const iso = y + '-' + m + '-' + d;

                    // Set hidden booking_end fields for main form and modals (use safe lookups)
                    const bookingEndHidden = document.getElementById('booking_end_hidden');
                    if (bookingEndHidden) bookingEndHidden.value = iso;
                    const modalBookingEndEl = document.getElementById('modal_booking_end');
                    if (modalBookingEndEl) modalBookingEndEl.value = iso;
                    const onsiteBookingEndEl = document.getElementById('onsite_booking_end');
                    if (onsiteBookingEndEl) onsiteBookingEndEl.value = iso;
                } else {
                    // Invalid start date
                    computedEndDate.textContent = '-';
                    const bookingEndHidden = document.getElementById('booking_end_hidden');
                    if (bookingEndHidden) bookingEndHidden.value = '';
                    const modalBookingEndEl = document.getElementById('modal_booking_end');
                    if (modalBookingEndEl) modalBookingEndEl.value = '';
                    const onsiteBookingEndEl = document.getElementById('onsite_booking_end');
                    if (onsiteBookingEndEl) onsiteBookingEndEl.value = '';
                }
            } else {
                // unit is not duration-like: do not compute booking_end
                computedEndDate.textContent = '-';
                const bookingEndHidden = document.getElementById('booking_end_hidden');
                if (bookingEndHidden) bookingEndHidden.value = '';
                const modalBookingEndEl = document.getElementById('modal_booking_end');
                if (modalBookingEndEl) modalBookingEndEl.value = '';
                const onsiteBookingEndEl = document.getElementById('onsite_booking_end');
                if (onsiteBookingEndEl) onsiteBookingEndEl.value = '';
            }
        } else {
            totalPriceElem.textContent = '0.00';
            downpaymentElem.textContent = '0.00';
            totalPriceHidden.value = 0;
            downpaymentHidden.value = 0;
            computedEndDate.textContent = '-';
            // ensure booking_end hidden cleared
            const bookingEndHidden = document.getElementById('booking_end_hidden');
            if (bookingEndHidden) bookingEndHidden.value = '';
            const modalBookingEndEl = document.getElementById('modal_booking_end');
            if (modalBookingEndEl) modalBookingEndEl.value = '';
            const onsiteBookingEndEl = document.getElementById('onsite_booking_end');
            if (onsiteBookingEndEl) onsiteBookingEndEl.value = '';
        }
    }

    if (bookingStart) bookingStart.addEventListener('change', updateTotal);
    const attendeesElem = document.getElementById('attendees');
    if (attendeesElem) {
        attendeesElem.addEventListener('input', updateTotal);
    }
    // Initialize totals on load
    updateTotal();
});
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
@endpush