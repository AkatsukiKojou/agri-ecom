  @if(session('success'))
    <div class="p-4 bg-green-100 text-green-800 mb-4 rounded flex items-center gap-2">
      <i class="bi bi-check-circle-fill text-2xl"></i>
      <span class="font-medium">{{ session('success') }}</span>
    </div>
  @endif
@extends('admin.layout')

@section('title', 'Archived Services')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
  <h2 class="text-2xl font-semibold mb-6">Archived Services</h2>
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-lg shadow hover:bg-blue-200 transition">
      <i class="bi bi-arrow-left-circle text-lg"></i>
      <span class="font-semibold">Back to Active Services</span>
    </a>
    <div class="w-full md:w-1/2 md:ml-auto">
      <input type="text" id="archivedSearch" placeholder="Search archived services..." class="border border-blue-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-500 px-4 py-2 rounded-lg shadow-sm transition w-full text-base" onkeyup="filterArchivedServices()">
    </div>
  </div>

  @if(session('message'))
    <div class="p-4 bg-green-100 text-green-800 mb-4 rounded">
        {{ session('message') }}
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 bg-red-100 text-red-800 mb-4 rounded">
        {{ session('error') }}
    </div>
  @endif

  <!-- Search bar moved above, aligned with back button -->
  <div class="overflow-x-auto rounded-xl shadow">
    <table class="min-w-full table-auto border rounded-xl overflow-hidden text-sm">
      <thead>
        <tr class="bg-gradient-to-r from-gray-200 to-gray-100 text-gray-700 uppercase tracking-wide text-xs">
          <th class="px-3 py-2 border">Image</th>
          <th class="px-3 py-2 border">Service</th>
          <th class="px-3 py-2 border">Unit</th>
          <th class="px-3 py-2 border">Price</th>
          <th class="px-3 py-2 border">Location</th>
          <th class="px-3 py-2 border">Schedule</th>
          <th class="px-3 py-2 border">Duration</th>
          <th class="px-3 py-2 border">Deleted At</th>
          <th class="px-3 py-2 border">Actions</th>
        </tr>
      </thead>
      <tbody id="archivedTableBody">
        @forelse($archived as $p)
        <tr class="even:bg-gray-50 hover:bg-yellow-50 transition">
          <td class="px-3 py-2 border text-center">
            @if($p->images)
              <img src="{{ asset('storage/' . (is_array(json_decode($p->images, true)) ? ltrim(json_decode($p->images, true)[0], '/') : ltrim($p->images, '/')) ) }}" alt="{{ $p->service_name }}" class="w-14 h-14 object-cover rounded shadow">
            @else
              <span class="text-gray-400 italic">No Image</span>
            @endif
          </td>
          <td class="px-3 py-2 border font-semibold text-gray-800 truncate max-w-[90px]">{{ $p->service_name }}</td>
          <td class="px-3 py-2 border text-gray-600">{{ $p->unit }}</td>
          <td class="px-3 py-2 border text-green-700 font-bold">₱{{ number_format($p->price, 2) }}</td>
          <td class="px-3 py-2 border text-gray-700 truncate max-w-[80px]">{{ $p->location }}</td>
          <td class="px-3 py-2 border">{{ $p->start_time ? \Carbon\Carbon::parse($p->start_time)->format('h:i A') : '-' }} - {{ $p->end_time ? \Carbon\Carbon::parse($p->end_time)->format('h:i A') : '-' }}</td>
          <td class="px-3 py-2 border">{{ $p->duration ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $p->deleted_at ? $p->deleted_at->format('Y-m-d H:i') : 'N/A' }}</td>
          <td class="px-3 py-2 border text-center">
            <form action="{{ route('services.restore', $p->id) }}" method="POST" class="inline restore-service-form">
              @csrf
              <button type="button" title="Restore" class="inline-flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 rounded-full p-2 mx-1 shadow transition restore-service-btn">
                <i class="bi bi-arrow-counterclockwise text-lg"></i>
              </button>
            </form>
<!-- Custom Restore Confirmation Modal -->
<div id="restoreServiceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-xl shadow-xl p-8 max-w-sm w-full text-center">
    <i class="bi bi-arrow-counterclockwise text-4xl text-green-500 mb-4"></i>
    <h3 class="text-xl font-bold mb-2 text-gray-800">Restore Service?</h3>
    <p class="mb-6 text-gray-600">Are you sure you want to restore this service?</p>
    <div class="flex justify-center gap-4">
      <button id="cancelRestoreService" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold">Cancel</button>
      <button id="confirmRestoreService" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 font-semibold">Restore</button>
    </div>
  </div>
</div>
@push('scripts')
<script>
// Restore confirmation modal logic
document.addEventListener('DOMContentLoaded', function() {
  let restoreFormToSubmit = null;
  document.querySelectorAll('.restore-service-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      restoreFormToSubmit = btn.closest('form');
      document.getElementById('restoreServiceModal').classList.remove('hidden');
    });
  });
  document.getElementById('cancelRestoreService').addEventListener('click', function() {
    document.getElementById('restoreServiceModal').classList.add('hidden');
    restoreFormToSubmit = null;
  });
  document.getElementById('confirmRestoreService').addEventListener('click', function() {
    if (restoreFormToSubmit) {
      restoreFormToSubmit.submit();
    }
    document.getElementById('restoreServiceModal').classList.add('hidden');
    restoreFormToSubmit = null;
  });
});
</script>
@endpush
            <form action="{{ route('archived-services.forceDelete', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this Training Services?');">
              @csrf
              @method('DELETE')
              <button title="Delete Permanently" class="inline-flex items-center justify-center bg-red-100 text-red-700 hover:bg-red-200 rounded-full p-2 mx-1 shadow transition">
                <i class="bi bi-trash3 text-lg"></i>
              </button>
            </form>
          </td>
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
        </tr>
        @empty
        <tr><td colspan="9" class="p-4 text-center text-gray-500">No archived Services found.</td></tr>
        @endforelse
      </tbody>
@push('scripts')
<script>
function filterArchivedServices() {
  const searchValue = document.getElementById('archivedSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#archivedTableBody tr');
  rows.forEach(row => {
    // Combine all cell text for search
    const rowText = row.textContent.toLowerCase();
    row.style.display = rowText.includes(searchValue) ? '' : 'none';
  });
}
</script>
@endpush
    </table>
  </div>
</div>
@endsection
