{{-- filepath: resources/views/superadmin/users/index.blade.php --}}
@extends('superadmin.layout')
@section('title', 'Manage Users')
@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-people-fill"></i> Manage Users
    </h1>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <form id="searchForm" class="flex gap-2 w-full md:w-auto" autocomplete="off">
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search name, email, or address..." class="w-full md:w-64 px-4 py-2 border border-green-200 rounded-lg focus:ring-green-500 focus:border-green-500 transition text-sm" />
        </form>
        <a href="{{ route('superadmin.users.blocklist') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition flex items-center gap-2">
            <i class="bi bi-slash-circle"></i> Blocklist
        </a>
    </div>
    <div id="usersTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-green-100">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Photo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Gender</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Birthday</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Date Registered</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-50">
                @forelse($users as $user)
                    <tr class="hover:bg-green-50 transition">
                        <td class="px-4 py-3">
                       <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('agri-profile.png') }}"
                           class="w-10 h-10 rounded-full object-cover border border-green-200 shadow" alt="Profile">
                        </td>
                        <td class="px-4 py-3 text-green-800 font-mono">{{ $user->id }}</td>
                        <td class="px-4 py-3 font-semibold text-green-900">
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            <span class="block text-xs font-semibold">{{ $user->region ?? ($user->profile->region ?? '') }}, {{ $user->province ?? ($user->profile->province ?? '') }}, {{ $user->city ?? ($user->profile->city ?? '') }}</span>
                            <span class="block text-xs">{{ $user->barangay ?? ($user->profile->barangay ?? '') }} {{ $user->address ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->phone ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->gender ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->date_of_birth ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->status ?? 'Active' }}
                        </td>
                        <td class="px-4 py-3 text-green-800">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{-- <a href="{{ route('superadmin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800 mx-1 font-semibold" title="View">
                                View
                            </a> --}}
                            <form action="{{ route('superadmin.users.blocklistUser', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 mx-1 font-semibold bg-red-100 px-3 py-1 rounded transition" title="Blocklist" onclick="return confirm('Are you sure you want to blocklist this user?')">
                                    Block
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Realtime search script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const usersTable = document.getElementById('usersTable');
    let timeout = null;

    function fetchUsers() {
        const search = searchInput.value;
        fetch(`{{ route('superadmin.users') }}?search=${encodeURIComponent(search)}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('usersTable').innerHTML;
                usersTable.innerHTML = newTable;
            });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(fetchUsers, 300); // debounce for better UX
    });
});
</script>
@endsection