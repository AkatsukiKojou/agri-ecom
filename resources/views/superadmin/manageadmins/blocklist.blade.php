@extends('superadmin.layout')
@section('title', 'Blocklisted Admins')
@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-red-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-slash-circle"></i> Blocklisted Admins
    </h1>
    <div class="mb-6">
        <form id="blocklistSearchForm" class="flex items-center gap-2" autocomplete="off">
            <input type="text" name="search" id="blocklistSearchInput" value="{{ request('search') }}" placeholder="Search ID, name, farm name, or address..." class="border px-3 py-2 rounded w-64">
        </form>
    </div>
    <div id="adminsTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-red-100">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Photo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">FarmOnwer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Farm Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Phone</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-50">
                @forelse($admins as $admin)
                <tr class="hover:bg-red-50 transition">
                    <td class="px-4 py-3">
                        <img src="{{ $admin->profile && $admin->profile->profile_photo ? asset('storage/' . $admin->profile->profile_photo) : asset('agri-profile.png') }}"
                             class="w-10 h-10 rounded-full object-cover border border-red-200 shadow" alt="Profile">
                    </td>
                    <td class="px-4 py-3 text-red-800 font-mono">{{ $admin->id }}</td>
                    <td class="px-4 py-3 font-semibold text-red-900">
                        {{ $admin->name }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $admin->profile->farm_name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $admin->email }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        <span class="block text-s">{{ $admin->profile->address ?? '' }} {{ $admin->profile->barangay ?? '' }}</span>
                        <span class="block text-s">{{ $admin->profile->city ?? '' }}, {{ $admin->profile->province ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $admin->profile->phone_number ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('manageadmins.unblocklist', $admin->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800 mx-1 font-semibold bg-green-100 px-3 py-1 rounded transition" title="Unblocklist" onclick="return confirm('Are you sure you want to remove this admin from blocklist?')">
                                <i class="bi bi-check-circle"></i> Unblocklist
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">No blocklisted admins found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $admins->links() }}
        </div>
        <div class="text-sm text-gray-600 mt-2">
            Showing {{ $admins->firstItem() ?? 0 }} to {{ $admins->lastItem() ?? 0 }} of {{ $admins->total() }} blocklisted admins
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('blocklistSearchInput');
    const tableWrapper = document.getElementById('adminsTable');
    let timeout = null;

    function fetchAdmins() {
        const params = new URLSearchParams({ search: input.value }).toString();
        fetch(`{{ route('manageadmins.blocklist') }}?${params}`)
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('adminsTable');
                if (newTable) tableWrapper.innerHTML = newTable.innerHTML;
            })
            .catch(err => console.error('Blocklist admins fetch error', err));
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(fetchAdmins, 300);
    });
    input.addEventListener('change', fetchAdmins);
});
</script>
@endsection
