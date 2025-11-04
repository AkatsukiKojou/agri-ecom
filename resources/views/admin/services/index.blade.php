{{-- filepath: resources/views/admin/services/index.blade.php --}}
@extends('admin.layout')
@section('title', 'Service Page')

@section('content')
<!-- Toast Notification Container -->
<div id="toast-container" style="position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;"></div>
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .img-preview-multi {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    .img-preview-multi img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px #0001;
    }
</style>
@endpush

<div >
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6">
        <div class="flex items-center gap-4">
            <i class="bi bi-gear-fill text-green-600 text-4xl"></i>
            <h2 class="text-3xl font-extrabold tracking-tight text-green-800 drop-shadow">Training Services</h2>
        </div>
        <div class="flex flex-wrap items-center gap-3 mt-2 md:mt-0">
            <input type="text" id="search"
                placeholder="Search by name..."
                class="border border-green-300 focus:ring-2 focus:ring-green-400 focus:border-green-500 px-4 py-2 rounded-lg shadow-sm transition w-64 text-base"
                onkeyup="filterServices()">
            <button onclick="document.getElementById('serviceModal').classList.remove('hidden')"
                class="flex items-center gap-2 bg-green-600 text-white px-5 py-2 rounded-lg shadow hover:bg-green-700 transition font-semibold text-base">
                <i class="bi bi-plus-circle text-xl"></i>
                Add Service
            </button>
            <button onclick="location.href='{{ route('services.archived.index') }}'"
                class="flex items-center gap-2 bg-yellow-500 text-white px-5 py-2 rounded-lg shadow hover:bg-yellow-600 transition font-semibold text-base">
                <i class="bi bi-archive text-xl"></i>
                Archive Services
            </button>
        </div>
    </div>
    {{-- Success Message --}}
   {{-- Success Message --}}
@if(session()->has('message'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast('success', @json(session('message')));
    });
    </script>
@endif

@if(session()->has('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast('success', @json(session('success')));
    });
    </script>
@endif


<!-- ADD Modal (Table-based, multi-row like products) -->
<div id="serviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-none p-8 relative animate-fade-in" style="margin-left:4vw;margin-right:4vw;">
        <button type="button" onclick="document.getElementById('serviceModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold focus:outline-none z-10">&times;</button>
        <h3 class="text-2xl font-bold mb-6 text-green-700 flex items-center gap-2">
            <i class="bi bi-plus-circle text-green-500"></i> Add New Training Services
        </h3>
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border-l-4 border-red-500">
                <strong>Whoops!</strong> There were some issues with your input.<br><br>
                <ul class="list-disc ml-5 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="addServiceForm" action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="overflow-y-auto max-h-96 border rounded mb-4">
                <table class="w-full table-auto border" id="serviceTable">
                    <thead class="bg-gray-200 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Images (max 6)</th>
                            <th class="px-4 py-2">Service Name</th>
                            <th class="px-4 py-2">Unit</th>
                            <th class="px-4 py-2">Price</th>
                            <th class="px-4 py-2">StartTime (optional)</th>
                            <th class="px-4 py-2">Duration</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="img-preview-multi" id="add_img_preview_0"></div>
                                    <input type="file" name="images[]" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewImages(this, 'add_img_preview_0')" multiple={false}>
                                <small class="text-xs text-gray-400">Only 1 image allowed</small>
                            </td>
                            <td>
                                <input type="text" name="service_name[]" class="border px-2 py-1 w-full rounded" placeholder="Service Name" required>
                            </td>
                            <td>
                                <select name="unit[]" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                                    <option value="session">Session</option>
                                    <option value="day">Day</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="training">Training</option>
                                    <option value="program">Program</option>
                                    <option value="others">Others</option>
                                </select>
                                <input type="text" name="unit_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
                            </td>
                            <td><input type="number" name="price[]" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
                            <td><input type="time" name="start_time[]" class="border px-2 py-1 w-full rounded" required></td>
                            <td>
                                <div class="flex gap-2">
                                        <input type="number" name="duration_value[]" min="1" class="border px-2 py-1 rounded w-2/3" placeholder="e.g. 2">
                                        <!-- Replaced select with readonly text input that will be synced from the unit selection -->
                                        <input type="text" name="duration_unit[]" class="border px-2 py-1 rounded w-32 bg-white text-gray-800 duration-unit-input" readonly placeholder="unit">
                                </div>
                            </td>
                            <td><textarea name="description[]" class="border px-2 py-1 w-full rounded" rows="2" required></textarea></td>
                            <td><button type="button" class="text-red-500 hover:text-red-700 font-semibold" onclick="removeRow(this)">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button id="addRowBtn" type="button" onclick="addRow()" class="bg-blue-500 text-white px-3 py-1 rounded mb-4 hover:bg-blue-600 transition font-semibold shadow">
                <i class="bi bi-plus-circle"></i> Add More
            </button>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('serviceModal').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>


<!-- EDIT Modal (Table-based like products, robust and clean) -->
<div id="editServiceModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-none p-8 relative animate-fade-in" style="margin-left:4vw;margin-right:4vw;">
        <button type="button" onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold focus:outline-none z-10">&times;</button>
        <h3 class="text-2xl font-bold mb-6 text-blue-700 flex items-center gap-2">
            <i class="bi bi-pencil-square text-blue-500"></i> Edit Service
        </h3>
    <form action="{{ route('services.update', ['id' => '__ID__']) }}" method="POST" id="editServiceForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <table class="w-full table-auto border mb-4" id="editServiceTable">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Image</th>
                        <th class="px-4 py-2">Training Service Name</th>
                        <th class="px-4 py-2">Unit</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">StartTime (optional)</th>
                        <th class="px-4 py-2">Duration</th>
                        <th class="px-4 py-2">Description</th>
                    </tr>
                </thead>
                <tbody>
                            <tr>
                        <td>
                            <div class="img-preview-multi mb-2" id="edit_img_preview">
                                {{-- Existing images preview --}}
                                @if(isset($service) && !empty($service->images))
                                    @php
                                        $editImagesArr = [];
                                        $decoded = json_decode($service->images, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                                            $editImagesArr = $decoded;
                                        } else {
                                            $editImagesArr = is_array($service->images) ? $service->images : explode(',', $service->images);
                                        }
                                    @endphp
                                    @foreach($editImagesArr as $img)
                                        <img src="{{ asset('storage/' . ltrim($img, '/')) }}" alt="Current Image" />
                                    @endforeach
                                @endif
                            </div>
                            <input type="file" name="images" id="edit_images" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewImages(this, 'edit_img_preview')">
                            <input type="hidden" name="old_images" id="old_images">
                        </td>
                        <td>
                            <input type="text" name="service_name" id="edit_service_name" class="border px-2 py-1 w-full rounded" placeholder="Service Name" required>
                        </td>
                     
                        <td>
                            <select name="unit" id="edit_unit" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                                <option value="session">Session</option>
                                <option value="day">Day</option>
                                <option value="seminar">Seminar</option>
                                <option value="training">Training</option>
                                <option value="program">Program</option>
                                <option value="others">Others</option>
                            </select>
                            <input type="text" id="edit_custom_unit" name="custom_unit" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
                        </td>
                        <td><input type="number" name="price" id="edit_price" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
                        <td><input type="time" name="start_time" id="edit_start_time" class="border px-2 py-1 w-full rounded" required></td>
                        <td>
                            <div class="flex gap-2">
                                <input type="number" name="duration_value" id="edit_duration_value" min="1" class="border px-2 py-1 rounded w-2/3" placeholder="e.g. 2">
                                <!-- duration unit is auto-filled from unit selection or custom unit -->
                                <input type="text" name="duration_unit" id="edit_duration_unit" class="border px-2 py-1 rounded w-32 bg-white text-gray-800 duration-unit-input" readonly placeholder="unit">
                            </div>
                        </td>
                        <td><textarea name="description" id="edit_description" class="border px-2 py-1 w-full rounded" rows="2" required></textarea></td>
                    </tr>
                </tbody>
            </table>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition font-semibold">Save Changes</button>
            </div>
        </form>
    </div>
</div>
{{-- Filtering --}}
 <div class="flex flex-wrap gap-4 mb-4">
    <select id="unitFilter" class="border px-3 py-2 rounded-lg">
            <option value="">All Units</option>
            @foreach($units as $unit)
                <option value="{{ $unit }}">{{ $unit }}</option>
            @endforeach
        </select>
        <select id="priceSort" class="border px-3 py-2 rounded-lg">
            <option value="">Sort by Price</option>
            <option value="low">Low to High</option>
            <option value="high">High to Low</option>
        </select>
        <select id="availabilityFilter" class="border px-3 py-2 rounded-lg">
            <option value="">All Availability</option>
            <option value="Available">Available</option>
            <option value="Not Available">Not Available</option>
        </select>
    </div>
<!-- Archive Selected Button (sticky left, orange) -->
<form id="bulkArchiveForm" action="{{ route('services.bulkArchive') }}" method="POST" style="position:sticky;top:0;z-index:20;background:white;">
    @csrf
    <div style="display:flex;justify-content:flex-start;align-items:center;min-height:0.5rem;gap:0.5rem;">
        <button type="button" id="bulkArchiveBtn" class="bg-orange-500 text-white px-4 py-2 rounded-lg shadow font-semibold hidden" style="margin-bottom:0;" aria-label="Archive Selected" onclick="openBulkArchiveModal()">
            <span id="bulkArchiveSpinner" class="spinner-border spinner-border-sm me-2" style="display:none;width:1em;height:1em;border-width:2px;"></span>
            <i class="bi bi-archive text-base me-2" style="font-size: 1em;"></i> Archive Selected
        </button>
  </div>
    <div class="overflow-x-auto rounded-3xl shadow-2xl mt-4">
    <table id="servicesTable" class="w-full table-auto border rounded-2xl overflow-hidden text-sm" aria-label="Services Table">
            <thead>
                <tr class="bg-gradient-to-r from-green-700 to-green-400 text-white uppercase tracking-wide text-sm">
                    <th class="px-3 py-2 border"><input type="checkbox" id="selectAllServices" onclick="toggleSelectAll(this)"></th>
                    <th class="px-3 py-2 border">Img</th>
                    <th class="px-3 py-2 border">Service Name</th>
                    <th class="px-3 py-2 border">Unit</th>
                    <th class="px-3 py-2 border">Price</th>
                    <th class="px-3 py-2 border">Schedule</th>
                    <th class="px-3 py-2 border">Duration</th>
                    <th class="px-3 py-2 border">Availability</th>
                    <th class="px-3 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr class="even:bg-gray-50 hover:bg-green-50 transition">
                        <td class="px-2 py-1 border text-center">
                            <input type="checkbox" class="service-select" name="selected_services[]" value="{{ $service->id }}" onchange="toggleBulkArchiveBtn()" aria-label="Select Service {{ $service->id }}">
                        </td>
                        <td class="px-2 py-1 border text-center">
                            <div class="img-preview-multi" style="position: relative; width: 48px; height: 48px; margin: 0 auto;">
                                @php
                                    $imagesArr = [];
                                    if (!empty($service->images)) {
                                        $decoded = json_decode($service->images, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                                            $imagesArr = $decoded;
                                        } else {
                                            $imagesArr = is_array($service->images) ? $service->images : explode(',', $service->images);
                                        }
                                    }
                                    $carouselId = 'carousel_' . $service->id;
                                @endphp
                                <div id="{{ $carouselId }}" style="width:48px; height:48px; position:relative;">
                                    @if(!empty($imagesArr))
                                        <img src="{{ asset('storage/' . ltrim($imagesArr[0], '/')) }}" alt="{{ $service->service_name }}" class="w-12 h-12 object-cover rounded" id="{{ $carouselId }}_img" data-index="0" data-images='@json($imagesArr)' />
                                    @else
                                        <img src="{{ asset('default-service.png') }}" alt="No Image" class="w-12 h-12 object-cover rounded" />
                                    @endif
                                    @if(count($imagesArr) > 1)
                                        <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var imgTag = document.getElementById('{{ $carouselId }}_img');
                                            if (!imgTag) return;
                                            var imagesArr = [];
                                            try {
                                                imagesArr = JSON.parse(imgTag.getAttribute('data-images'));
                                            } catch (e) { imagesArr = []; }
                                            if (!imagesArr.length) return;
                                            var idx = 0;
                                            setInterval(function() {
                                                idx = (idx + 1) % imagesArr.length;
                                                imgTag.src = '/storage/' + imagesArr[idx];
                                                imgTag.setAttribute('data-index', idx);
                                            }, 2000);
                                        });
                                        </script>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-2 py-1 border font-semibold text-gray-800 truncate max-w-[120px]">{{ $service->service_name }}</td>
                        <td class="px-2 py-1 border text-gray-600">{{ $service->unit }}</td>
                        <td class="px-2 py-1 border text-green-700 font-bold">₱{{ number_format($service->price, 2) }}</td>
                        <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($service->start_time)->format('h:i A') }}</td>
                        <td class="px-2 py-1 border">{{ $service->duration ?? '-' }}</td>
                        <td class="px-2 py-1 border">
                            @if($service->is_available)
                                <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold text-xs shadow">Available</span>
                            @else
                                <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold text-xs shadow">Not Available</span>
                            @endif
                        </td>
                        <td class="px-2 py-1 border">
                            <div class="flex flex-row gap-1 items-center justify-center">
                                <button type="button" class="bg-yellow-500 text-white p-1 rounded hover:bg-yellow-600 transition shadow text-base" onclick="window.location.href='{{ route('services.show', $service->id) }}'" aria-label="View Service {{ $service->id }}">
                                    <i class="bi bi-eye" style="font-size: 1.4em;"></i>
                                </button>
                                <button type="button" onclick='openEditModal(@json($service))' class="bg-blue-500 text-white p-1 rounded hover:bg-blue-600 transition shadow text-base" aria-label="Edit Service {{ $service->id }}">
                                    <i class="bi bi-pencil-square" style="font-size: 1.4em;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500 text-lg">No Training Services found.</td>
                    </tr>
                @endforelse
               
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="flex justify-end mt-4">
            <div class="inline-block rounded-lg shadow bg-white px-2 py-1">
                {!! $services->links('pagination::tailwind') !!}
            </div>
        </div>
    </div>
</form>
<script>
// Standard units available
var STANDARD_UNITS = ['hour','session','day','package','event','service','others'];

// Local storage key for custom units
var CUSTOM_UNITS_KEY = 'training_custom_units_v1';

function loadCustomUnits() {
    try {
        var raw = localStorage.getItem(CUSTOM_UNITS_KEY);
        if (!raw) return [];
        var arr = JSON.parse(raw);
        if (!Array.isArray(arr)) return [];
        return arr;
    } catch (e) { return []; }
}

function saveCustomUnits(arr) {
    try { localStorage.setItem(CUSTOM_UNITS_KEY, JSON.stringify(arr)); } catch (e) {}
}

// Provide suggestions for custom unit inputs by attaching a datalist (non-intrusive)
function populateCustomUnitsToSelect(selectEl) {
    if (!selectEl) return;
    var cell = selectEl.closest('td');
    if (!cell) return;
    var custom = cell.querySelector('.custom-unit-input');
    if (!custom) return;
    // create or reuse a datalist for suggestions
    var listId = 'custom-units-list';
    var datalist = document.getElementById(listId);
    if (!datalist) {
        datalist = document.createElement('datalist');
        datalist.id = listId;
        document.body.appendChild(datalist);
    }
    // populate datalist options
    var units = loadCustomUnits();
    datalist.innerHTML = '';
    units.forEach(function(u) {
        var opt = document.createElement('option');
        opt.value = u;
        datalist.appendChild(opt);
    });
    // attach datalist to the custom input
    custom.setAttribute('list', listId);
}

function addCustomUnit(unit) {
    if (!unit) return;
    unit = unit.trim();
    if (!unit) return;
    var existing = loadCustomUnits();
    // case-insensitive check
    var found = existing.find(u => u.toLowerCase() === unit.toLowerCase());
    if (found) return found; // already present
    existing.push(unit);
    saveCustomUnits(existing);
    // Do NOT inject into selects anymore — keep custom units stored but submitted separately
    return unit;
}
// Toggle custom unit input when 'others' is selected (or show when custom input has value)
function toggleCustomUnit(selectEl) {
    if (!selectEl) return;
    var cell = selectEl.closest('td');
    if (!cell) return;
    var custom = cell.querySelector('.custom-unit-input');
    if (!custom) return;
    // find the duration unit input in the same row (if present)
    var row = selectEl.closest('tr');
    var durationInput = row ? row.querySelector('.duration-unit-input') : null;
    // If the user selects a default/standard unit, hide and clear any custom input immediately.
    if (selectEl.value !== 'others') {
        custom.style.display = 'none';
        custom.required = false;
        custom.value = '';
        // set the duration unit to the selected unit
        if (durationInput) durationInput.value = selectEl.value;
        return;
    }

    // Otherwise (select is 'others') show the custom input and make it required
    custom.style.display = '';
    custom.required = true;
    // If duration input exists and custom has a value, set it
    if (durationInput && custom.value) durationInput.value = custom.value;
    if (!custom.__hasHandlers) {
        custom.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var v = custom.value.trim();
                if (v) {
                    addCustomUnit(v); // saves to localStorage but does NOT inject into selects
                    custom.blur();
                    if (durationInput) durationInput.value = v;
                }
            }
        });
        // also update duration when custom input changes (blur/change)
        custom.addEventListener('change', function() {
            var v = custom.value.trim();
            if (durationInput) durationInput.value = v;
        });
        custom.__hasHandlers = true;
    }
}

// Initialize on DOM ready: ensure custom inputs are visible where needed
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select.unit-select').forEach(function(sel) {
        try { toggleCustomUnit(sel); } catch (e) {}
    });
    // Initialize duration-unit inputs for existing rows based on current unit select or custom unit
    document.querySelectorAll('select.unit-select').forEach(function(sel) {
        var row = sel.closest('tr');
        if (!row) return;
        var durationInput = row.querySelector('.duration-unit-input');
        var custom = row.querySelector('.custom-unit-input');
        if (durationInput) {
            if (sel.value && sel.value !== 'others') durationInput.value = sel.value;
            else if (custom && custom.value) durationInput.value = custom.value;
        }
    });
});

// Toast notification function
function showToast(type, message) {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `flex items-center gap-2 px-4 py-3 rounded shadow-lg text-white animate-fade-in ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} text-xl"></i><span class="font-medium">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 4000);
}

// Edit Modal Functions
function openEditModal(service) {
    // Show the modal
    document.getElementById('editServiceModal').classList.remove('hidden');
    // Set the form action dynamically using the correct service ID
    var form = document.getElementById('editServiceForm');
    var originalAction = form.getAttribute('action');
    // Replace __ID__ with the actual service id
    form.setAttribute('action', originalAction.replace('__ID__', service.id));

    // Populate form fields
    // populate service_name instead
    if (document.getElementById('edit_service_name')) {
        document.getElementById('edit_service_name').value = service.service_name || '';
    }
    // Handle unit: if it's one of standard units, select it; otherwise select 'others' and prefill custom input
    var editUnitSelect = document.getElementById('edit_unit');
    var editCustom = document.getElementById('edit_custom_unit');
    if (editUnitSelect) {
        if (service.unit && STANDARD_UNITS.indexOf(service.unit) !== -1) {
            editUnitSelect.value = service.unit;
            if (editCustom) { editCustom.style.display = 'none'; editCustom.required = false; editCustom.value = ''; }
        } else if (service.unit) {
            // unknown unit -> choose others and prefill
            editUnitSelect.value = 'others';
            if (editCustom) { editCustom.style.display = ''; editCustom.required = true; editCustom.value = service.unit; }
        } else {
            editUnitSelect.value = '';
            if (editCustom) { editCustom.style.display = 'none'; editCustom.required = false; editCustom.value = ''; }
        }
    }
    document.getElementById('edit_price').value = service.price || '';
    document.getElementById('edit_start_time').value = service.start_time ? service.start_time.substring(0,5) : '';
    // Handle duration parsing
    let durationValue = '';
    let durationUnit = '';
    if (service.duration_value && service.duration_unit) {
        durationValue = service.duration_value;
        durationUnit = service.duration_unit;
    } else if (service.duration) {
        // Try to parse duration string, e.g., '2 hours', '1 day'
        const match = service.duration.match(/(\d+)\s*(\w+)/);
        if (match) {
            durationValue = match[1];
            durationUnit = match[2].toLowerCase();
        }
    }
    document.getElementById('edit_duration_value').value = durationValue;
    document.getElementById('edit_duration_unit').value = durationUnit;
    document.getElementById('edit_description').value = service.description || '';
    // Images preview (fetch from database and show in modal)
    const preview = document.getElementById('edit_img_preview');
    preview.innerHTML = '';
    let imagesArr = [];
    if (service.images) {
        if (Array.isArray(service.images)) {
            imagesArr = service.images;
        } else if (typeof service.images === 'string') {
            try {
                const decoded = JSON.parse(service.images);
                if (Array.isArray(decoded)) {
                    imagesArr = decoded;
                } else {
                    imagesArr = service.images.split(',');
                }
            } catch (e) {
                imagesArr = service.images.split(',');
            }
        }
    }
    imagesArr.forEach(function(imgUrl) {
        if (!imgUrl) return;
        const img = document.createElement('img');
        img.src = '/storage/' + imgUrl.replace(/^\/+/, '');
        img.style.width = '60px';
        img.style.height = '60px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '0.5rem';
        img.style.marginRight = '4px';
        preview.appendChild(img);
    });
}

function closeEditModal() {
    document.getElementById('editServiceModal').classList.add('hidden');
}

// Bulk archive confirmation modal
function openBulkArchiveModal() {
    if (!document.getElementById('bulkArchiveModal')) {
        const modal = document.createElement('div');
        modal.id = 'bulkArchiveModal';
        modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full relative">
                <button onclick="closeBulkArchiveModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
                <div class="flex flex-col items-center gap-3">
                    <i class="bi bi-archive text-orange-500 text-4xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">Archive Selected Services?</h3>
                    <p class="text-gray-600 text-center">Are you sure you want to archive the selected services? This action can be undone from the archive page.</p>
                    <div class="flex gap-4 mt-4">
                        <button onclick="submitBulkArchive()" class="bg-orange-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-orange-600">Yes, Archive</button>
                        <button onclick="closeBulkArchiveModal()" class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-semibold hover:bg-gray-400">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
}
function closeBulkArchiveModal() {
    const modal = document.getElementById('bulkArchiveModal');
    if (modal) modal.remove();
}
function submitBulkArchive() {
    // Show spinner
    document.getElementById('bulkArchiveSpinner').style.display = '';
    document.getElementById('bulkArchiveBtn').disabled = true;
    closeBulkArchiveModal();
    document.getElementById('bulkArchiveForm').submit();
}

// Bulk set availability
function bulkSetAvailability(isAvailable) {
    const form = document.getElementById('bulkArchiveForm');
    const selected = Array.from(form.querySelectorAll('.service-select:checked'));
    if (!selected.length) return;
    // For demo, just show a toast. In real app, send AJAX to update availability.
    showToast('success', isAvailable ? 'Set selected services as Available.' : 'Set selected services as Not Available.');
}

function toggleBulkArchiveBtn() {
    const checkboxes = document.querySelectorAll('.service-select');
    const bulkBtn = document.getElementById('bulkArchiveBtn');
    const bulkAvail = document.getElementById('bulkAvailableBtn');
    const bulkNotAvail = document.getElementById('bulkNotAvailableBtn');
    let anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    bulkBtn.classList.toggle('hidden', !anyChecked);
    bulkAvail.classList.toggle('hidden', !anyChecked);
    bulkNotAvail.classList.toggle('hidden', !anyChecked);
    // Update select all checkbox state
    const selectAll = document.getElementById('selectAllServices');
    if (selectAll) {
        const allChecked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;
    }
}
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.service-select');
    checkboxes.forEach(cb => { cb.checked = source.checked; });
    toggleBulkArchiveBtn();
}
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.service-select');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBulkArchiveBtn);
    });
    document.getElementById('selectAllServices').addEventListener('change', function() {
        toggleSelectAll(this);
    });
    // Populate custom units into all existing selects
    document.querySelectorAll('select.unit-select').forEach(function(sel) {
        populateCustomUnitsToSelect(sel);
    });
    // Archive form submit: redirect after archive
    // Regular form submit, no AJAX, will redirect after bulk archive
});
</script>
<script>
    function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


    function addRow() {
        const tableBody = document.querySelector("#serviceTable tbody");
        // Find the next row index for images[]
        let rowIndex = tableBody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <div class="img-preview-multi" id="add_img_preview_${rowIndex}"></div>
                <input type="file" name="images_${rowIndex}" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewImages(this, 'add_img_preview_${rowIndex}')">
                <small class="text-xs text-gray-400">Only 1 image allowed</small>
            </td>
            <td>
                <input type="text" name="service_name[]" class="border px-2 py-1 w-full rounded" placeholder="Service Name" required>
            </td>
            <td>
                <select name="unit[]" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                    <option value="session">Session</option>
                    <option value="day">Day</option>
                    <option value="seminar">Seminar</option>
                    <option value="training">Training</option>
                    <option value="program">Program</option>
                    <option value="others">Others</option>
                </select>
                <input type="text" name="unit_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
            </td>
            <td><input type="number" name="price[]" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
            <td><input type="time" name="start_time[]" class="border px-2 py-1 w-full rounded" required></td>
            <td>
                <div class="flex gap-2">
                    <input type="number" name="duration_value[]" min="1" class="border px-2 py-1 rounded w-2/3" placeholder="e.g. 2">
                    <input type="text" name="duration_unit[]" class="border px-2 py-1 rounded w-1/3 duration-unit-input" readonly placeholder="unit">
                </div>
            </td>
            <td><textarea name="description[]" class="border px-2 py-1 w-full rounded" rows="2" required></textarea></td>
            <td><button type="button" class="text-red-500 hover:text-red-700 font-semibold" onclick="removeRow(this)">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
        // After appending, initialize the unit select and duration input for the new row
        const sel = newRow.querySelector('select.unit-select');
        const custom = newRow.querySelector('.custom-unit-input');
        const durationInput = newRow.querySelector('.duration-unit-input');
        if (sel && durationInput) {
            // set default duration unit to the selected value (first option)
            durationInput.value = sel.value || '';
            // ensure onchange wiring
            sel.addEventListener('change', function() { toggleCustomUnit(sel); });
        }
        if (custom && durationInput) {
            custom.addEventListener('change', function() {
                if (sel.value === 'others') durationInput.value = custom.value || '';
            });
        }
    }

    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
    }

    function filterServices() {
        const searchValue = document.getElementById('search').value.toLowerCase();
    const typeValue = ''; // legacy placeholder for compatibility
        const unitValue = document.getElementById('unitFilter').value;
        const priceSort = document.getElementById('priceSort').value;
        const availabilityValue = document.getElementById('availabilityFilter').value;
        const rows = document.querySelectorAll('#servicesTable tbody tr');
        let filteredRows = Array.from(rows);

        filteredRows.forEach(row => {
            // Column mapping: 0=checkbox,1=img,2=serviceName,3=unit,4=price,5=schedule,6=duration,7=availability,8=actions
            const serviceNameCell = row.cells[2] ? row.cells[2].textContent.trim().toLowerCase() : '';
            const unit = row.cells[3] ? row.cells[3].textContent.trim() : '';
            const price = row.cells[4] ? parseFloat(row.cells[4].textContent.replace(/[^\d.]/g, '')) : 0;
            const availability = row.cells[7] ? row.cells[7].textContent.trim() : '';
            let isMatch = true;
            if (searchValue && !(serviceNameCell.includes(searchValue) || unit.toLowerCase().includes(searchValue))) isMatch = false;
            if (unitValue && unit !== unitValue) isMatch = false;
            if (availabilityValue) {
                if (availabilityValue === "Available" && !availability.includes("Available")) isMatch = false;
                if (availabilityValue === "Not Available" && !availability.includes("Not Available")) isMatch = false;
            }
            row.style.display = isMatch ? '' : 'none';
        });

        // Show/hide 'No Training Services found' row
        // Only count visible data rows (exclude the static empty state and the dynamic noServicesRow)
        const noRow = document.getElementById('noServicesRow');
        let visibleDataRows = Array.from(rows).filter(row => {
            // Exclude the dynamic noServicesRow and any static empty state row
            return row !== noRow && row.querySelector('input[type="checkbox"],button[aria-label^="View"],button[aria-label^="Edit"]') && row.style.display !== 'none';
        });
        if (noRow) {
            noRow.style.display = visibleDataRows.length === 0 ? '' : 'none';
        }

        // Price sorting
        if (priceSort) {
            let visibleRowsSorted = Array.from(rows).filter(row => row.style.display !== 'none');
            visibleRowsSorted.sort((a, b) => {
                const priceA = parseFloat(a.cells[4].textContent.replace(/[^\d.]/g, ''));
                const priceB = parseFloat(b.cells[4].textContent.replace(/[^\d.]/g, ''));
                return priceSort === 'low' ? priceA - priceB : priceB - priceA;
            });
            const tbody = document.querySelector('#servicesTable tbody');
            visibleRowsSorted.forEach(row => tbody.appendChild(row));
        }
    }

    // Ensure all filter dropdowns and search input trigger realtime filtering
    document.getElementById('unitFilter').addEventListener('change', filterServices);
    document.getElementById('priceSort').addEventListener('change', filterServices);
    document.getElementById('availabilityFilter').addEventListener('change', filterServices);
    document.getElementById('search').addEventListener('keyup', filterServices);
    // Initial filter on page load (optional, for consistency)
    filterServices();

  
</script>
@push('scripts')
<script>
function previewImages(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
}

// Show current images in edit modal
function showEditImages(images) {
    const preview = document.getElementById('edit_img_preview');
    preview.innerHTML = '';
    if (Array.isArray(images)) {
        images.forEach(function(img) {
            const imageTag = document.createElement('img');
            imageTag.src = '/storage/' + img.replace(/^\/+/, '');
            preview.appendChild(imageTag);
        });
    }
}

</script>
@endpush

@endsection