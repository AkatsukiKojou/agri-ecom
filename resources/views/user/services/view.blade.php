@extends('user.layout')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded shadow space-y-4">
    <h1 class="text-3xl font-bold text-gray-800">{{ $service->service_name }}</h1>

    <div class="grid md:grid-cols-2 gap-6">
        @if($service->image_upload)
            <img src="{{ asset('storage/' . $service->image_upload) }}" alt="Service Image" class="w-full rounded shadow">
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500 rounded">
                No Image Available
            </div>
        @endif

        <div class="space-y-2">
            <p><strong>Category:</strong> {{ $service->category }}</p>
            <p><strong>Mode:</strong> {{ $service->service_mode }}</p>
            <p><strong>Price:</strong> {{ $service->price ? '₱' . number_format($service->price, 2) : 'Free' }}</p>
            <p>
                <strong>Status:</strong> 
                @if($service->is_available)
                    <span class="text-green-600 font-semibold">Available</span>
                @else
                    <span class="text-red-600 font-semibold">Not Available</span>
                @endif
            </p>

            @if ($service->brochure_upload)
                <a href="{{ asset('storage/' . $service->brochure_upload) }}" target="_blank" class="text-blue-600 underline">
                    View Brochure
                </a>
            @endif
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mt-4">Description</h2>
        <p class="mt-2 text-gray-700">{{ $service->description }}</p>
    </div>
</div>
@endsection
