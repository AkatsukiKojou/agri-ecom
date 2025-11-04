@extends('superadmin.layout')
@section('title', 'Admin Details')
@section('content')
<div class="min-h-screen bg-green-50 py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full relative">
        {{-- Back Button --}}
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('manageadmins.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 text-white rounded-lg shadow hover:bg-green-800 transition font-semibold">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <button type="button" onclick="openPasswordModal()"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-semibold">
                <i class="bi bi-key"></i> Update Password
            </button>
    @include('superadmin.manageadmins._update_password_modal')
        </div>
        <div class="flex flex-col items-center gap-6 mb-8">
            <img src="{{ $admin->profile && $admin->profile->profile_photo ? asset('storage/' . $admin->profile->profile_photo) : asset('agri-profile.png') }}"
                 class="w-32 h-32 rounded-full object-cover border-4 border-green-300 shadow-lg mb-2" alt="Profile">
            <h2 class="text-3xl font-extrabold text-green-800 mb-1">{{ $admin->profile->farm_owner }}</h2>
            <div class="grid grid-cols-2 gap-4 w-full">
                <div>
                    <span class="font-semibold text-green-900">Farm Name:</span>
                    <div class="text-green-800">{{ $admin->profile->farm_name ?? 'N/A' }}</div>
                </div>
                <div>
                    <span class="font-semibold text-green-900">Phone:</span>
                    <div class="text-green-800">{{ $admin->profile->phone_number ?? 'N/A' }}</div>
                </div>
                <div>
                    <span class="font-semibold text-green-900">Email:</span>
                    <p class="text-lg text-green-700 mb-1">{{ $admin->profile->email }}</p>

                    <span class="font-semibold text-green-900">Address:</span>
                    <div class="text-green-800">
                        {{ $admin->profile->barangay ?? 'N/A' }}
                        {{ $admin->profile->city ?? 'N/A' }}
                        {{ $admin->profile->province ?? 'N/A' }}
                        {{ $admin->profile->region ?? 'N/A' }}
                    </div>
                </div>
              
            </div>
        </div>


        <div class="mb-8">
            <span class="font-semibold text-green-900 block mb-2">Certificate:</span>
            @if($admin->profile && $admin->profile->certificate)
                <a href="{{ asset('storage/' . $admin->profile->certificate) }}" target="_blank" class="text-blue-700 underline font-semibold">
                    <i class="bi bi-file-earmark-text"></i> View Certificate
                </a>
            @else
                <span class="text-gray-500">No certificate uploaded.</span>
            @endif
        </div>

        {{-- GCash QR Code --}}
        <div class="mb-8">
            <span class="font-semibold text-green-900 block mb-2">GCash QR Code:</span>
            @if($admin->profile && $admin->profile->gcash_qr)
                <img src="{{ asset('storage/' . $admin->profile->gcash_qr) }}" alt="GCash QR Code" class="w-40 h-40 object-contain border rounded-lg shadow mb-2">
            @else
                <span class="text-gray-500">No GCash QR code uploaded.</span>
            @endif
        </div>

        {{-- Farm Photo --}}
        <div class="mb-8">
            <span class="font-semibold text-green-900 block mb-2">Farm Photo:</span>
            @if($admin->profile && $admin->profile->farm_photo)
                <img src="{{ asset('storage/' . $admin->profile->farm_photo) }}" alt="Farm Photo" class="w-full max-w-md object-cover border rounded-lg shadow mb-2">
            @else
                <span class="text-gray-500">No farm photo uploaded.</span>
            @endif
        </div>

        {{-- Farm Gallery --}}
        <div class="mb-8">
            <span class="font-semibold text-green-900 block mb-2">Farm Gallery:</span>
            @if($admin->profile && $admin->profile->farm_gallery && is_array(json_decode($admin->profile->farm_gallery, true)))
                <div class="flex flex-wrap gap-4">
                    @foreach(json_decode($admin->profile->farm_gallery, true) as $galleryImg)
                        <img src="{{ asset('storage/' . $galleryImg) }}" alt="Farm Gallery Image" class="w-32 h-32 object-cover border rounded-lg shadow">
                    @endforeach
                </div>
            @else
                <span class="text-gray-500">No farm gallery images uploaded.</span>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-4">
            <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-xl p-6 flex flex-col items-center shadow hover:scale-105 transition">
                <span class="font-semibold text-green-900 mb-2">Total Products</span>
                <span class="text-3xl font-extrabold text-green-700">{{ $admin->products_count ?? 0 }}</span>
            </div>
            <div class="bg-gradient-to-br from-green-100 to-lime-100 rounded-xl p-6 flex flex-col items-center shadow hover:scale-105 transition">
                <span class="font-semibold text-green-900 mb-2">Total Services</span>
                <span class="text-3xl font-extrabold text-green-700">{{ $admin->services_count ?? 0 }}</span>
            </div>
            <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-xl p-6 flex flex-col items-center shadow hover:scale-105 transition">
                <span class="font-semibold text-green-900 mb-2">Followers</span>
                <span class="text-3xl font-extrabold text-green-700">{{ $admin->profileFollowers->count() }}</span>
            </div>
            <div class="bg-gradient-to-br from-green-100 to-green-400 rounded-xl p-6 flex flex-col items-center shadow hover:scale-105 transition">
                <span class="font-semibold text-green-900 mb-2">Likes</span>
                <span class="text-3xl font-extrabold text-green-700">{{ $admin->likes_count ?? 0 }}</span>
            </div>
        </div>

        {{-- Admin's Products Section --}}
        <div class="mt-12">
            <h3 class="text-2xl font-bold text-green-800 mb-6">Products</h3>
            @php
                $products = $admin->products()->paginate(20);
            @endphp
            @if($products->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white border border-green-100 rounded-xl shadow p-4 flex flex-col items-center hover:shadow-lg transition">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('no-image.png') }}" alt="Product Image" class="w-24 h-24 object-cover rounded mb-2">
                            <div class="font-semibold text-green-900 text-center">{{ $product->name }}</div>
                            <div class="text-green-700 text-sm text-center">₱{{ number_format($product->price, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-gray-500">No products found for this admin.</div>
            @endif
        </div>

        {{-- Admin's Services Section --}}
        <div class="mt-12">
            <h3 class="text-2xl font-bold text-green-800 mb-6">Services</h3>
            @php
                $services = $admin->services ?? ($admin->services() ? $admin->services()->get() : collect());
            @endphp
            @if($services->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($services as $service)
                        <div class="bg-white border border-green-100 rounded-xl shadow p-4 flex flex-col items-center hover:shadow-lg transition">
                            @php
                                $serviceImages = [];
                                if (!empty($service->images)) {
                                    $decoded = json_decode($service->images, true);
                                    if (is_array($decoded)) {
                                        $serviceImages = $decoded;
                                    } elseif (is_string($service->images)) {
                                        $serviceImages = [$service->images];
                                    }
                                }
                                $serviceImgSrc = count($serviceImages) && $serviceImages[0] ? asset('storage/' . $serviceImages[0]) : asset('no-image.png');
                            @endphp
                            <img src="{{ $serviceImgSrc }}" alt="Service Image" class="w-24 h-24 object-cover rounded mb-2">
                            <div class="font-semibold text-green-900 text-center mb-1">{{ $service->service_name }}</div>
                            <div class="text-green-700 text-sm text-center">₱{{ number_format($service->price, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-500">No services found for this admin.</div>
            @endif
        </div>
    </div>
</div>
@endsection