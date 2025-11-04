@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $service->service_name }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 space-y-6">

        {{-- Service Image --}}
        <div>
            <strong class="block text-gray-700 mb-2">Image:</strong>
            @if ($service->image_upload)
                <img src="{{ asset('storage/' . $service->image_upload) }}" 
                     alt="Service Image" 
                     class="rounded-lg w-64 h-auto border shadow">
            @else
                <p class="text-gray-500">No image uploaded.</p>
            @endif
        </div>

        {{-- Description and Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
            <p><strong>Description:</strong><br>{{ $service->description ?? '-' }}</p>
            <p><strong>Category:</strong><br>{{ $service->category ?? '-' }}</p>
            <p><strong>Tags:</strong><br>{{ $service->tags ?? '-' }}</p>
            <p><strong>Price:</strong><br>{{ $service->price ? '₱' . number_format($service->price, 2) : 'Free' }}</p>
            <p><strong>Availability:</strong><br>{{ $service->is_available ? 'Active' : 'Inactive' }}</p>
            <p><strong>Service Mode:</strong><br>{{ $service->service_mode }}</p>
            <p><strong>Schedule:</strong><br>{{ $service->schedule ?? '-' }}</p>
            <p><strong>Requirements:</strong><br>{{ $service->requirements ?? '-' }}</p>
            <p><strong>Eligibility:</strong><br>{{ $service->eligibility ?? '-' }}</p>
        </div>

        {{-- Contact Person --}}
        <div class="text-gray-700">
            <strong class="block mb-2">Contact Person:</strong>
            <p>{{ $service->contact_person_name ?? '-' }}</p>
            <p>{{ $service->contact_person_phone ?? '-' }}</p>
            @if ($service->contact_person_email)
                <a href="mailto:{{ $service->contact_person_email }}" class="text-blue-600 hover:underline">
                    {{ $service->contact_person_email }}
                </a>
            @else
                <p>-</p>
            @endif
        </div>

        {{-- Brochure --}}
        @if ($service->brochure_upload)
            <div>
                <strong class="block text-gray-700 mb-2">Brochure:</strong>
                <a href="{{ asset('storage/' . $service->brochure_upload) }}" target="_blank"
                   class="text-blue-600 hover:underline">
                    View Brochure (PDF)
                </a>
            </div>
        @endif

        {{-- back, Edit and Delete Buttons --}}
        <div class="flex space-x-4 pt-4">
            <a href="{{ route('services.index') }}" 
               class="inline-block bg-gray-700 text-white px-5 py-2 rounded hover:bg-gray-800">
                &larr; Back to Services
            <a href="{{ route('services.edit', $service->id) }}" 
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Edit Service
            </a>

            <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-block bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                    Delete Service
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
