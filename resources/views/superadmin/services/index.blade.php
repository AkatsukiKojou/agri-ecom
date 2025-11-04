@extends('superadmin.layout')
@section('title', 'Manage Services')
@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-gear-wide-connected"></i> Manage Services
    </h1>
 
    <!-- Search and Filter Form -->
    <div class="flex justify-between items-center mb-6">
        <form id="filterForm" class="flex flex-wrap gap-2 items-end" autocomplete="off">
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search category, service name, unit..." class="border px-4 py-2 rounded w-64">

            {{-- Category Filter --}}
            <select name="category" id="categorySelect" class="border px-4 py-2 rounded">
                <option value="">All Categories</option>
                @php $categories = \App\Models\Service::select('service_name')->distinct()->pluck('service_name'); @endphp
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Unit Filter --}}
            <select name="unit" id="unitSelect" class="border px-4 py-2 rounded">
                <option value="">All Units</option>
                @php $units = \App\Models\Service::select('unit')->distinct()->pluck('unit'); @endphp
                @foreach($units as $unit)
                    <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('superadmin.services.blocklist') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition flex items-center gap-2">
            <i class="bi bi-archive"></i> View Blocklist
        </a>
    </div>
    <div id="servicesTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-green-100">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Service ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Service Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Duration</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Farm Owner</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-50">
                @forelse($services as $service)
                <tr class="hover:bg-green-50 transition">
                    <td class="px-4 py-3">
                        @if($service->images)
                            <img src="{{ asset('storage/' . $service->images) }}" alt="Service Image" class="h-12 w-12 object-cover rounded-full border" />
                        @else
                            <span class="text-gray-400">No Image</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-green-800 font-mono">{{ $service->id }}</td>
                    <td class="px-4 py-3 font-semibold text-green-900">{{ $service->service_name }}</td>
                    <td class="px-4 py-3 text-green-800">{{ $service->service_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-green-800">{{ $service->unit ?? '-' }}</td>
                    <td class="px-4 py-3 text-green-800">{{ $service->duration ?? '-' }}</td>
                    <td class="px-4 py-3 text-green-800">{{ $service->price ? number_format($service->price, 2) : '-' }}</td>
                    <td class="px-4 py-3 text-green-800">{{ $service->status ?? 'Active' }}</td>
                    <td class="px-4 py-3 text-green-800">
                        @php
                            $owner = $service->admin_id ? \App\Models\User::find($service->admin_id) : null;
                        @endphp
                        {{ $owner ? $owner->name : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('superadmin.services.block', $service->id) }}" class="text-red-600 font-semibold px-2 py-1 rounded hover:underline" onclick="return confirm('Block this service?')">Block</a>
    
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-6 text-center text-gray-500">No services found.</td>
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
    const filterForm = document.getElementById('filterForm');
    const servicesTable = document.getElementById('servicesTable');
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const unitSelect = document.getElementById('unitSelect');
    let timeout = null;

    function fetchServices() {
        const params = new URLSearchParams(new FormData(filterForm)).toString();
        fetch(`{{ route('superadmin.services') }}?${params}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('servicesTable').innerHTML;
                servicesTable.innerHTML = newTable;
            });
    }

    [searchInput, categorySelect, unitSelect].forEach(el => {
        el.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(fetchServices, 300);
        });
        el.addEventListener('change', fetchServices);
    });
});
</script>
@endsection
