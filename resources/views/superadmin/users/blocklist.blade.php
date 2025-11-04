@extends('superadmin.layout')
@section('title', 'Blocklisted Users')
@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-red-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-slash-circle"></i> Blocklisted Users
    </h1>
    <div class="mb-6">
        <form id="usersBlocklistSearchForm" class="flex items-center gap-2" autocomplete="off">
            <input type="text" id="usersBlocklistSearchInput" name="search" value="{{ request('search') }}" placeholder="Search ID, name or address..." class="border px-3 py-2 rounded w-64">
        </form>
    </div>
    <div id="usersTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-red-100">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Photo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Gender</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Birthday</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase">Date Registered</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-50">
                @forelse($users as $user)
                <tr class="hover:bg-red-50 transition">
                    <td class="px-4 py-3">
                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('agri-profile.png') }}"
                             class="w-10 h-10 rounded-full object-cover border border-red-200 shadow" alt="Profile">
                    </td>
                    <td class="px-4 py-3 text-red-800 font-mono">{{ $user->id }}</td>
                    <td class="px-4 py-3 font-semibold text-red-900">
                        {{ $user->name }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->email }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        <span class="block text-xs font-semibold">{{ $user->region ?? ($user->profile->region ?? '') }}, {{ $user->province ?? ($user->profile->province ?? '') }}, {{ $user->city ?? ($user->profile->city ?? '') }}</span>
                        <span class="block text-xs">{{ $user->barangay ?? ($user->profile->barangay ?? '') }} {{ $user->address ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->phone ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->gender ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->date_of_birth ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->status ?? 'Active' }}
                    </td>
                    <td class="px-4 py-3 text-red-800">
                        {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('superadmin.users.unblocklist', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800 mx-1 font-semibold bg-green-100 px-3 py-1 rounded transition" title="Unblocklist" onclick="return confirm('Are you sure you want to remove this user from blocklist?')">
                                <i class="bi bi-check-circle"></i> Unblocklist
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-4 py-6 text-center text-gray-500">No blocklisted users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $users->links() }}
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('usersBlocklistSearchInput');
    const wrapper = document.getElementById('usersTable');
    let timeout = null;

    function fetchUsers() {
        const params = new URLSearchParams({ search: input.value }).toString();
        fetch(`{{ route('superadmin.users.blocklist') }}?${params}`)
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('usersTable');
                if (newTable) wrapper.innerHTML = newTable.innerHTML;
            })
            .catch(err => console.error('Users blocklist fetch error', err));
    }

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(fetchUsers, 300);
    });
    input.addEventListener('change', fetchUsers);
});
</script>
@endsection
