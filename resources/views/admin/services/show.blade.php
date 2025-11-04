@extends('admin.layout')
@section('title', 'Service Details')

@section('content')
<div >
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('services.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow flex items-center gap-2 font-semibold">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <i class="bi bi-gear-fill text-green-600 text-4xl"></i>
        <h2 class="text-3xl font-extrabold tracking-tight text-green-800 drop-shadow">Service Details</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Service Info Card -->
        <div class="bg-gradient-to-br from-green-100 to-green-50 border-l-4 border-green-400 rounded-2xl shadow p-6 flex flex-col gap-3">
            <h3 class="text-lg font-bold text-green-700 flex items-center gap-2 mb-2"><i class="bi bi-info-circle"></i> Info</h3>
            <div><span class="font-semibold">Type:</span> {{ $service->service_name }}</div>
            <div><span class="font-semibold">Unit:</span> {{ $service->unit }}</div>
            <div><span class="font-semibold">Price:</span> <span class="text-green-700 font-bold">₱{{ number_format($service->price, 2) }}</span></div>
            <div><span class="font-semibold">Availability:</span>
                @if($service->is_available)
                    <span class="inline-block bg-green-200 text-green-800 px-2 py-0.5 rounded-full font-bold text-xs shadow">Available</span>
                @else
                    <span class="inline-block bg-red-200 text-red-800 px-2 py-0.5 rounded-full font-bold text-xs shadow">Not Available</span>
                @endif
            </div>
        </div>
        <!-- Images Card -->
        <div class="bg-gradient-to-br from-pink-100 to-pink-50 border-l-4 border-pink-400 rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-pink-700 flex items-center gap-2 mb-2"><i class="bi bi-images"></i> Images</h3>
            <div class="flex gap-3 flex-wrap">
                @php
                    $imagesArr = [];
                    if (!empty($service->images)) {
                        $decoded = json_decode($service->images, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                            $imagesArr = $decoded;
                        } else {
                            $imagesArr = is_array($service->images) ? $service->images : explode(',', $service->images);
                        }
                    }
                @endphp
                @if(!empty($imagesArr))
                    @foreach($imagesArr as $img)
                        <img src="{{ asset('storage/' . ltrim($img, '/')) }}" alt="Service Image" class="w-24 h-24 object-cover rounded-xl border shadow">
                    @endforeach
                @else
                    <img src="{{ asset('default-service.png') }}" alt="No Image" class="w-24 h-24 object-cover rounded-xl border shadow">
                @endif
            </div>
        </div>
        <!-- Schedule Card -->
        <div class="bg-gradient-to-br from-purple-100 to-purple-50 border-l-4 border-purple-400 rounded-2xl shadow p-6 flex flex-col gap-3">
            <h3 class="text-lg font-bold text-purple-700 flex items-center gap-2 mb-2"><i class="bi bi-calendar-event"></i> Schedule</h3>
            <div><span class="font-semibold">Start:</span> {{ \Carbon\Carbon::parse($service->start_time)->format('h:i A') }}</div>
            <div><span class="font-semibold">End:</span> {{ \Carbon\Carbon::parse($service->end_time)->format('h:i A') }}</div>
            <div><span class="font-semibold">Duration:</span> {{ $service->duration ?? '-' }}</div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <!-- Location Card -->
        <div class="bg-gradient-to-br from-blue-100 to-blue-50 border-l-4 border-blue-400 rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-blue-700 flex items-center gap-2 mb-2"><i class="bi bi-geo-alt"></i> Location</h3>
            <div class="text-lg text-gray-800">{{ $service->location }}</div>
        </div>
        <!-- Trainer Card -->
        <div class="bg-gradient-to-br from-indigo-100 to-indigo-50 border-l-4 border-indigo-400 rounded-2xl shadow p-6 flex flex-col gap-3">
            <h3 class="text-lg font-bold text-indigo-700 flex items-center gap-2 mb-2"><i class="bi bi-person-badge"></i> Trainer</h3>
            <div><span class="font-semibold">Name:</span> {{ $service->trainer_name ?? '-' }}</div>
            <div><span class="font-semibold">Credentials:</span> {{ $service->trainer_credentials ?? '-' }}</div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <!-- Description Card -->
        <div class="bg-gradient-to-br from-orange-100 to-orange-50 border-l-4 border-orange-400 rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-orange-700 flex items-center gap-2 mb-2"><i class="bi bi-file-earmark-text"></i> Description</h3>
            <div class="text-lg text-gray-800">{{ $service->description }}</div>
        </div>
        <!-- Contact Card -->
        <div class="bg-gradient-to-br from-teal-100 to-teal-50 border-l-4 border-teal-400 rounded-2xl shadow p-6 flex flex-col gap-3">
            <h3 class="text-lg font-bold text-teal-700 flex items-center gap-2 mb-2"><i class="bi bi-telephone"></i> Contact</h3>
            <div><span class="font-semibold">Person:</span> {{ $service->contact_person ?? '-' }}</div>
            <div><span class="font-semibold">Info:</span> {{ $service->contact_info ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
