@extends('superadmin.layout')
@section('title', 'Blocklisted Services')
@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-red-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-archive"></i> Blocklisted Services
    </h1>
    <div class="mb-6">
        <form method="GET" action="{{ route('superadmin.services.blocklist') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, name or unit..." class="border px-3 py-2 rounded w-64">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Search</button>
        </form>
    </div>
    <div id="blocklistTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-red-100">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Service ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Service Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Duration</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Farm Owner</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-50">
                @forelse($services as $service)
                <tr class="hover:bg-red-50 transition">
                    <td class="px-4 py-3">
                        @if($service->images)
                            <img src="{{ asset('storage/' . $service->images) }}" alt="Service Image" class="h-12 w-12 object-cover rounded-full border" />
                        @else
                            <span class="text-gray-400">No Image</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-red-800 font-mono">{{ $service->id }}</td>
                    <td class="px-4 py-3 font-semibold text-red-900">{{ $service->service_name }}</td>
                    <td class="px-4 py-3 text-red-800">{{ $service->service_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-red-800">{{ $service->unit ?? '-' }}</td>
                    <td class="px-4 py-3 text-red-800">{{ $service->duration ?? '-' }}</td>
                    <td class="px-4 py-3 text-red-800">{{ $service->price ? number_format($service->price, 2) : '-' }}</td>
                    <td class="px-4 py-3 text-red-800">
                        @php
                            $owner = $service->admin_id ? \App\Models\User::find($service->admin_id) : null;
                        @endphp
                        {{ $owner ? $owner->name : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('superadmin.services.restore', $service->id) }}" class="text-green-600 font-semibold px-2 py-1 rounded hover:underline" onclick="return confirm('Restore this service?')">Restore</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">No blocklisted services found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $services->links() }}
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('superadmin.services.blocklist') }}"]');
    if (!form) return;
    const input = form.querySelector('input[name="search"]');
    const tableWrapper = document.getElementById('blocklistTable');
    let timeout = null;

    function fetchBlocklist() {
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch(`{{ route('superadmin.services.blocklist') }}?${params}`)
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('blocklistTable');
                if (newTable) tableWrapper.innerHTML = newTable.innerHTML;
            })
            .catch(err => console.error('Blocklist fetch error', err));
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(fetchBlocklist, 300);
    });
    input.addEventListener('change', fetchBlocklist);
});
</script>
@endsection
