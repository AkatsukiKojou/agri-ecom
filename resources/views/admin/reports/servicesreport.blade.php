@extends('admin.layout')

@section('content')
<div class="flex flex-col items-center gap-2 mb-6">
	<h1 class="text-2xl md:text-4xl lg:text-4xl font-extrabold text-green-800 tracking-tight">Training Services Report</h1>
	<p class="text-sm md:text-base text-gray-500">Overview and analytics for your training services</p>
</div>
	<script>
		// Export PDF: open current filters in a new tab with export=pdf param
		document.addEventListener('DOMContentLoaded', function () {
			const exportBtn = document.getElementById('exportPdfBtn');
			const form = document.getElementById('filtersForm');
			const loadingIndicator = document.getElementById('loadingIndicator');
			if (exportBtn && form) {
				exportBtn.addEventListener('click', function (e) {
					e.preventDefault();
					const params = new URLSearchParams();
					Array.from(form.elements).forEach(el => {
						if (!el.name) return;
						if (el.type === 'checkbox' || el.type === 'radio') {
							if (!el.checked) return;
						}
						const val = el.value;
						if (val !== null && String(val).trim() !== '') {
							params.append(el.name, val);
						}
					});
					params.set('export', 'pdf');
					const url = window.location.pathname + '?' + params.toString();
					if (loadingIndicator) loadingIndicator.classList.remove('hidden');
					window.open(url, '_blank');
					setTimeout(() => { if (loadingIndicator) loadingIndicator.classList.add('hidden'); }, 800);
				});
			}
		});
	</script>
<div class="container mx-auto p-4">
	<!-- Summary Cards -->
	<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
		
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Total Bookings Sales (Completed only)</div>
			<div class="text-2xl">₱{{ number_format($totalBookingSales ?? 0, 2) }}</div>
		</div>
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Total Bookings</div>
			<div class="text-2xl">{{ number_format($totalBookings ?? 0) }}</div>
		</div>
        
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Top Service</div>
			@php
				// Determine requested top-N from request or controller-provided variable
				$topNRaw = old('top_n', request('top_n') ?? null);
				$topN = is_numeric($topNRaw) ? (int) $topNRaw : 1; // default show Top 1
			@endphp

			@if(!empty($topServices) && isset($topServices) && is_iterable($topServices) && count($topServices))
				@if($topN <= 1)
					@php $first = $topServices[0] ?? (is_object($topServices) ? $topServices->first() : null); @endphp
					<div class="text-2xl">{{ $first->name ?? ($topService->name ?? ($topServiceName ?? '—')) }}</div>
					@if(!empty($first->unit) || isset($first->price))
						<div class="text-sm text-gray-600 mt-1">
							@if(!empty($first->unit))
								per {{ $first->unit }}
							@endif
							@if(isset($first->price))
								<span class="ml-2">₱{{ number_format($first->price ?? 0, 2) }}</span>
							@endif
						</div>
					@endif
				@else
					<div class="text-left max-h-40 overflow-auto">
						<ul class="divide-y">
							@foreach($topServices as $index => $svc)
								@if($index >= $topN) @break @endif
								<li class="py-1 flex justify-between items-center">
									<div class="truncate">
										<div class="font-medium">{{ $svc->name ?? '—' }}@if(!empty($svc->unit)) · <span class="text-sm text-gray-600">{{ $svc->unit }}</span>@endif</div>
										<div class="text-xs text-gray-500">{{ $svc->category ?? ($svc->type ?? 'Unspecified') }} · ₱{{ number_format($svc->price ?? 0, 2) }}</div>
									</div>
									<span class="font-semibold">x{{ $svc->total_qty ?? ($svc->bookings_count ?? 0) }}</span>
								</li>
							@endforeach
						</ul>
					</div>
				@endif
			@elseif(!empty($topService) || !empty($topServiceName))
				{{-- fallback to single top service from controller --}}
				<div class="text-2xl">{{ $topService->name ?? ($topServiceName ?? '—') }}</div>
				@if(!empty($topService->unit) || isset($topService->price))
					<div class="text-sm text-gray-600 mt-1">
						@if(!empty($topService->unit))
							per {{ $topService->unit }}
						@endif
						@if(isset($topService->price))
							<span class="ml-2">₱{{ number_format($topService->price ?? 0, 2) }}</span>
						@endif
					</div>
				@endif
			@else
				<div class="text-2xl">—</div>
			@endif
		</div>
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Top Booker</div>
			@if(!empty($topBooker))
				<div class="text-xl font-semibold mt-2 truncate">{{ $topBooker }}</div>
				<div class="text-sm text-gray-600 mt-1">Total bookings: <span class="font-medium text-gray-800">{{ $topBookerCount ?? $topBookerQty ?? 0 }}</span></div>
				
			@else
				<div class="text-sm text-gray-500 mt-2">No bookers found for the current filters.</div>
			@endif
		</div>
	</div>
	<!-- Date Filter & Export -->
	
<br>

	<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
		<form method="GET" action="{{ route('admin.reports.servicesreport') }}" class="flex gap-2 items-center" id="filtersForm">
			<label for="date_from" class="font-semibold">From:</label>
			<input type="date" id="date_from" name="date_from" value="{{ old('date_from', $filters['date_from'] ?? request('date_from')) }}" class="border rounded px-2 py-1">
			<label for="date_to" class="font-semibold ml-2">To:</label>
			<input type="date" id="date_to" name="date_to" value="{{ old('date_to', $filters['date_to'] ?? request('date_to')) }}" class="border rounded px-2 py-1">
			

			<input type="text" name="q" id="search" placeholder="Search Service Name or Category" value="{{ request('q') ?? '' }}" class="border rounded px-2 py-1 ml-2">
			<select name="status" class="border rounded px-2 py-1 ml-2">
				<option value="">All Status</option>
				<option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
				<option value="ongoing" @if(request('status')=='ongoing') selected @endif>Ongoing</option>
				<option value="approved" @if(request('status')=='approved') selected @endif>Approved</option>
				<option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
				<option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
				<option value="reject" @if(request('status')=='reject') selected @endif>Reject</option>
				<option value="no_show" @if(request('status')=='no_show') selected @endif>No Show</option>
			</select>

			<button type="submit" id="applyFiltersBtn" class="bg-green-700 text-white px-3 py-1 rounded">Filter</button>
		</form>
		<div id="loadingIndicator" class="ml-2 hidden flex items-center gap-2 text-sm text-gray-600">
			<svg class="animate-spin h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
				<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
			</svg>
			<span>Loading...</span>
		</div>

		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const form = document.getElementById('filtersForm');
				const monthSelect = form.querySelector('select[name="month"]');
				const yearSelect = form.querySelector('select[name="year"]');
				const dateFrom = form.querySelector('input[name="date_from"]');
				const dateTo = form.querySelector('input[name="date_to"]');
				const loadingIndicator = document.getElementById('loadingIndicator');

				const debounce = (fn, ms) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), ms); }; };

				// Prevent submitting by pressing Enter in any input field — require explicit Filter button click
				const inputs = form.querySelectorAll('input, select');
				inputs.forEach(el => {
					el.addEventListener('keydown', function (e) {
						if (e.key === 'Enter') {
							e.preventDefault();
						}
					});
				});

				// Apply filters only when the Filter button is clicked
				const applyBtn = document.getElementById('applyFiltersBtn');
				if (applyBtn) {
					// button is now submit-type; show loading indicator on form submit
					form.addEventListener('submit', function () {
						if (loadingIndicator) loadingIndicator.classList.remove('hidden');
					});
				}

				// When month is selected, populate date_from and date_to with that month's bounds (using selected year if present)
				const pad = 👎 => String(n).padStart(2, '0');
				function applyMonthToDates() {
					if (!monthSelect) return;
					const mVal = monthSelect.value ? parseInt(monthSelect.value, 10) : null;
					if (!mVal) {
						if (yearSelect && yearSelect.value && yearSelect.value !== '') {
							const yVal = parseInt(yearSelect.value, 10);
							const firstYear = ${yVal}-01-01;
							const lastYear = ${yVal}-12-31;
							if (dateFrom) dateFrom.value = firstYear;
							if (dateTo) dateTo.value = lastYear;
						} else {
							if (dateFrom) dateFrom.value = '';
							if (dateTo) dateTo.value = '';
						}
						return;
					}
					let yVal = yearSelect && yearSelect.value ? parseInt(yearSelect.value, 10) : (new Date()).getFullYear();
					const first = ${yVal}-${pad(mVal)}-01;
					const lastDay = new Date(yVal, mVal, 0).getDate();
					const last = ${yVal}-${pad(mVal)}-${pad(lastDay)};
					if (dateFrom) dateFrom.value = first;
					if (dateTo) dateTo.value = last;
				}

				if (monthSelect) {
					monthSelect.addEventListener('change', function () {
						applyMonthToDates();
					});
					if (monthSelect.value) applyMonthToDates();
				}

				if (yearSelect) {
					yearSelect.addEventListener('change', function () {
						applyMonthToDates();
					});
				}

				window.addEventListener('pageshow', function () { if (loadingIndicator) setTimeout(() => loadingIndicator.classList.add('hidden'), 300); });
			});
		</script>
	</div>



	<!-- Charts -->
	<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
		<div class="bg-white shadow rounded p-4">
			<div class="font-bold mb-2">Bookings by TrainingServiceName</div>
			<div style="height:260px;">
				<canvas id="categoryPieChart" style="width:100%; height:100%; display:block;"></canvas>
			</div>
		</div>
		<div class="bg-white shadow rounded p-4">
			<div class="font-bold mb-2">Booking Status Distribution</div>
			<div style="height:260px;">
				<canvas id="statusPieChart" style="width:100%; height:100%; display:block;"></canvas>
			</div>
		</div>
	</div>
<div class="flex justify-end items-center gap-3 mb-2">
  <!-- Primary export button with icon and hover state -->
  <button id="exportPdfBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-lg shadow-sm transition-colors" title="Export current view as PDF">
    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l-3-3m3 3l3-3" />
      <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-width="0"></rect>
    </svg>
    <span class="font-medium">Export PDF</span>
  </button>
</div>
	<!-- Services Table -->
	<div class="overflow-x-auto bg-white shadow rounded">
		<table class="min-w-full">
			<thead class="bg-gray-100">
				<tr>
					<th class="py-2 px-4">TrainingService Name</th>
					<th class="py-2 px-4">Unit</th>
					<th class="py-2 px-4">Booked By</th>
					<th class="py-2 px-4">Price</th>
					<th class="py-2 px-4">Attendees</th>
					<th class="py-2 px-4">Booking Start</th>
					<th class="py-2 px-4">Booking End</th>
					<th class="py-2 px-4">Status</th>
				</tr>
			</thead>
			<tbody>
				@forelse($services as $booking)
					<tr>
						<td class="py-2 px-4">{{ $booking->service->service_name ?? $booking->service_name ?? '—' }}</td>
						<td class="py-2 px-4">{{ $booking->service->unit ?? $booking->unit ?? '—' }}</td>
						<td class="py-2 px-4">{{ $booking->user->name ?? $booking->full_name ?? '—' }}</td>
						<td class="py-2 px-4">₱{{ number_format($booking->total_price ?? ($booking->service->price ?? 0), 2) }}</td>
						<td class="py-2 px-4">{{ $booking->attendees ?? $booking->quantity ?? 0 }}</td>
						<td class="py-2 px-4">{{ !empty($booking->booking_start) ? \Carbon\Carbon::parse($booking->booking_start)->format('Y-m-d') : (optional($booking->booked_at ?? $booking->created_at)->format('Y-m-d') ?? '—') }}</td>
						<td class="py-2 px-4">
							{{ !empty($booking->booking_end) ? \Carbon\Carbon::parse($booking->booking_end)->format('Y-m-d') : '—' }}
						</td>
						<td class="py-2 px-4">{{ ucfirst(str_replace('_', ' ', $booking->status ?? '—')) }}</td>
					</tr>
				@empty
					<tr>
						<td class="py-2 px-4 text-center" colspan="8">No bookings found.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
		<!-- Pagination -->
		<div class="flex justify-end p-4">
			@if(method_exists($services, 'links'))
				{{ $services->appends(request()->query())->links() }}
			@else
				<!-- simple pager fallback -->
				<div class="flex gap-2">
					<span class="px-2 py-1 mx-1">{{ $services->count() }} items</span>
				</div>
			@endif
		</div>
	</div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	(function(){
		// Data passed from controller (fallbacks if not provided)
		const serviceLabels = @json($productLabels ?? []);
		const serviceData = @json($productData ?? []);
		const statusLabels = @json($statusLabels ?? []);
		const statusData = @json($statusData ?? []);

		// Helper to generate colors for charts
		function generateColors(count) {
			const palette = ['#4ade80', '#60a5fa', '#fbbf24', '#ef4444', '#2563eb', '#a21caf', '#f97316', '#06b6d4', '#a3e635', '#7c3aed'];
			const colors = [];
			for (let i = 0; i < count; i++) colors.push(palette[i % palette.length]);
			return colors;
		}

		// Category (Service) Pie
		try {
			const categoryPieCtx = document.getElementById('categoryPieChart').getContext('2d');
				new Chart(categoryPieCtx, {
					type: 'pie',
					data: {
						labels: serviceLabels.length ? serviceLabels : ['No Data'],
						datasets: [{
							data: serviceData.length ? serviceData : [1],
							backgroundColor: serviceData.length ? ['#4ade80', '#60a5fa', '#fbbf24', '#f97316', '#a78bfa'] : ['#e5e7eb']
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, usePointStyle: true } },
							tooltip: {
								callbacks: {
									label: function(context) {
										const value = context.parsed || 0;
										const dataArr = context.dataset.data || [];
										const sum = dataArr.reduce((a,b) => a + (Number(b) || 0), 0);
										const pct = sum ? ((value / sum) * 100).toFixed(1) : '0.0';
										return context.label + ': ' + value + ' (' + pct + '%)';
									}
								}
							}
						}
					}
				});
		} catch (e) {
			console.error('Category chart init failed', e);
		}

		// Status Pie - normalize to match default status order like productsreport
		try {
			// Ensure the status chart always shows these statuses (with zero if missing)
			// we expose 'approved' in the UI but normalize it to 'confirmed' for charting/aggregation
			const defaultStatuses = ['pending', 'ongoing', 'confirmed', 'completed', 'cancelled', 'reject', 'no_show'];
			const statusMap = {};
			for (let i = 0; i < statusLabels.length; i++) {
				// Convert incoming labels (which may be humanized like "No show") to normalized keys like 'no_show'
				let key = String(statusLabels[i] || '').toLowerCase().trim().replace(/\s+/g, '_');
				// normalize common variants
				if (key === 'rejected') key = 'reject';
				if (key === 'canceled') key = 'cancelled';
				if (key === 'complete') key = 'completed';
				if (key === 'paid') key = 'confirmed';
				if (key === 'approved') key = 'confirmed';
				statusMap[key] = (statusMap[key] || 0) + Number(statusData[i] || 0);
			}
			const statusLabelsFinal = defaultStatuses.map(s => {
				if (s === 'confirmed') return 'Approved';
				if (s === 'no_show') return 'No Show';
				if (s === 'ongoing') return 'Ongoing';
				return s.charAt(0).toUpperCase() + s.slice(1);
			});
			const statusDataFinal = defaultStatuses.map(s => statusMap[s] || 0);
			const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
			const statusChart = new Chart(statusPieCtx, {
				type: 'pie',
				data: {
					labels: statusLabelsFinal.length ? statusLabelsFinal : ['No Data'],
					datasets: [{
						data: statusDataFinal.length ? statusDataFinal : [1],
						// colors aligned to defaultStatuses order: pending, ongoing, confirmed, completed, cancelled, reject, no_show
						backgroundColor: statusDataFinal.length ? ['#fbbf24', '#f97316', '#22c55e', '#60a5fa', '#ef4444', '#a21caf', '#9ca3af'] : ['#e5e7eb']
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					// clicking a pie slice will set the status filter and submit the filters form
							onClick: function(evt, elements) {
						try {
							if (!elements || !elements.length) return;
							const idx = elements[0].index;
							const label = (this.data.labels && this.data.labels[idx]) ? String(this.data.labels[idx]).toLowerCase() : '';
							let value = label.replace(/\s+/g, '_');
							// map displayed labels back to UI filter values
									if (label === 'approved' || label === 'confirmed') value = 'approved';
									if (label === 'no show' || label === 'no_show') value = 'no_show';
									if (label === 'ongoing') value = 'ongoing';
							const form = document.getElementById('filtersForm');
							if (form) {
								const sel = form.querySelector('select[name="status"]');
								if (sel) {
									sel.value = value;
									const loadingIndicator = document.getElementById('loadingIndicator');
									if (loadingIndicator) loadingIndicator.classList.remove('hidden');
									form.submit();
								}
							}
						} catch (e) {
							console.error('Status pie click handler failed', e);
						}
					},
					plugins: {
						legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, usePointStyle: true } },
						tooltip: {
							callbacks: {
								label: function(context) {
									const value = context.parsed || 0;
									const dataArr = context.dataset.data || [];
									const sum = dataArr.reduce((a,b) => a + (Number(b) || 0), 0);
									const pct = sum ? ((value / sum) * 100).toFixed(1) : '0.0';
									return context.label + ': ' + value + ' (' + pct + '%)';
								}
							}
						}
					}
				}
			});
		} catch (e) {
			console.error('Status chart init failed', e);
		}
	})();
</script>

@endsection