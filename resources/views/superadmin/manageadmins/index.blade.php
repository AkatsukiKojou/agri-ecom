@extends('superadmin.layout')
@section('title', 'Manage Admins')
@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-8 text-center flex items-center justify-center gap-2">
        <i class="bi bi-person-badge-fill"></i> Manage Admins
    </h1>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <form id="searchForm" class="flex gap-2 w-full md:w-auto" autocomplete="off">
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search ID, FarmOwner, email, or address..." class="w-full md:w-64 px-4 py-2 border border-green-200 rounded-lg focus:ring-green-500 focus:border-green-500 transition text-sm" />
        </form>
        <a href="{{ route('manageadmins.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg font-semibold shadow transition flex items-center gap-2">
            <i class="bi bi-person-plus"></i> Add Admin
        </a>
        <a href="{{ route('manageadmins.blocklist') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition flex items-center gap-2">
            <i class="bi bi-slash-circle"></i> Blocklist
        </a>
    </div>
    <div id="adminsTable" class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-green-100">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Photo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">LSA Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Farm Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase">Phone</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-50">
                @forelse($admins as $admin)
                <tr class="hover:bg-green-50 transition">
                    <td class="px-4 py-3">
                        <img src="{{ $admin->profile && $admin->profile->profile_photo ? asset('storage/' . $admin->profile->profile_photo) : asset('agri-profile.png') }}"
                             class="w-10 h-10 rounded-full object-cover border border-green-200 shadow" alt="Profile">
                    </td>
                    <td class="px-4 py-3 text-green-800 font-mono">{{ $admin->id }}</td>
                    <td class="px-4 py-3 font-semibold text-green-900">
                        {{ $admin->name }}
                    </td>
                                        <td class="px-4 py-3 text-green-800">
                                                {{ $admin->profile->farm_name ?? '-' }}
                                        </td>
                    <td class="px-4 py-3 text-green-800">
                        {{ $admin->email }}
                    </td>
                    <td class="px-4 py-3 text-green-800">
                        <span class="block text-s">{{ $admin->profile->address ?? '' }} {{ $admin->profile->barangay ?? '' }}</span>
                        <span class="block text-s">{{ $admin->profile->city ?? '' }}, {{ $admin->profile->province ?? '' }}</span>

                    </td>
                    <td class="px-4 py-3 text-green-800">
                        {{ $admin->profile->phone_number ?? '' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                            @if($admin->last_login_at && \Carbon\Carbon::parse($admin->last_login_at)->lt(now()->subDays(30)))
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Inactive (30+ days)</span>
                            @else
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                            @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                            <a href="{{ route('manageadmins.show', $admin->id) }}" class="text-blue-600 font-semibold px-2 py-1 rounded" title="View">
                                View
                            </a>
                            <form action="{{ route('manageadmins.blocklistAdmin', $admin->id) }}" method="POST" onsubmit="return confirm('Blocklist this admin?')" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 bg-red-100 px-2 py-1 rounded font-semibold" title="Blocklist">
                                    Block
                                </button>
                            </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">No admins found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $admins->links() }}
        </div>
        <div class="text-sm text-gray-600 mt-2">
            Showing {{ $admins->firstItem() ?? 0 }} to {{ $admins->lastItem() ?? 0 }} of {{ $admins->total() }} admins
        </div>
    </div>
</div>
{{-- Realtime search script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const adminsTable = document.getElementById('adminsTable');
    let timeout = null;

    function fetchAdmins() {
        const search = searchInput.value;
        fetch(`{{ route('manageadmins.index') }}?search=${encodeURIComponent(search)}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('adminsTable').innerHTML;
                adminsTable.innerHTML = newTable;
            });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(fetchAdmins, 300); // debounce for better UX
    });
});
</script>
@if(session('success'))
    <!-- Success toast for index page -->
    <div id="successToast" class="fixed top-6 right-6 z-50 transform transition-all duration-300 opacity-0 translate-y-2">
        <div class="bg-green-600 text-white px-4 py-3 rounded shadow-lg flex items-start gap-3">
            <div class="flex-1">
                <div class="font-semibold">Success</div>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
            <button id="closeToast" class="text-white opacity-90 hover:opacity-100">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const toast = document.getElementById('successToast');
            const close = document.getElementById('closeToast');
            if (toast) {
                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.remove('translate-y-2');
                    toast.classList.add('opacity-100');
                    toast.classList.add('translate-y-0');
                }, 50);
                const hideFn = () => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    toast.classList.add('translate-y-2');
                    setTimeout(() => { try{ toast.remove(); } catch(e){} }, 300);
                };
                const timer = setTimeout(hideFn, 3500);
                if (close) close.addEventListener('click', function(){ clearTimeout(timer); hideFn(); });
            }
        });
    </script>
@endif
@endsection