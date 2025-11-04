@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Trashed Services</h2>

    <a href="{{ route('services.index') }}" class="text-blue-600 hover:underline">&larr; Back to Services</a>

    @if (session('success'))
        <div class="p-4 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Deleted At</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td class="border px-4 py-2">{{ $service->service_name }}</td>
                    <td class="border px-4 py-2">{{ $service->deleted_at->format('Y-m-d H:i') }}</td>
                    <td class="border px-4 py-2 space-x-2">
                        <form action="{{ route('services.restore', $service->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline">Restore</button>
                        </form>

                        <form action="{{ route('services.forceDelete', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this service?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete Permanently</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="border px-4 py-4 text-center text-gray-500">No trashed services found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $services->links() }}
    </div>
</div>
@endsection
