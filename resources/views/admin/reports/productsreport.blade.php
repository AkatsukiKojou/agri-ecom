@extends('admin.layout')

@section('content')
<div class="flex flex-col items-center gap-2 mb-6">
	<h1 class="text-2xl md:text-4xl lg:text-4xl font-extrabold text-green-800 tracking-tight">Products Report</h1>
	<p class="text-sm md:text-base text-gray-500">Overview and analytics for your products</p>
</div>
<div class="container mx-auto p-4">
	<!-- Summary Cards -->
	<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
		
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Total Product Sales</div>
			<div class="text-2xl">₱{{ number_format($totalProductSales ?? 0, 2) }}</div>
			<div class="text-sm text-gray-500 mt-1">(Completed orders only)</div>
		</div>
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Number of Orders</div> <br>
			<div class="text-2xl">{{ $numberOfOrders ?? 0 }}</div>
		</div>
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Top Product Sold</div>
			<div class="text-sm mt-2 w-full">
				@php
					// Always prefer showing a single top product (Top 1).
					$topProduct = null;
					if (!empty($topProductsSold) && ($topProductsSold instanceof \Illuminate\Support\Collection || is_array($topProductsSold))) {
						$topProduct = is_array($topProductsSold) ? ($topProductsSold[0] ?? null) : $topProductsSold->first();
					}
				@endphp

				@if(!empty($topProduct) && (is_object($topProduct) || is_array($topProduct)))
					@php
						$name = is_array($topProduct) ? ($topProduct['name'] ?? ($topProduct['product_name'] ?? null)) : ($topProduct->name ?? ($topProduct->product_name ?? null));
						$unit = is_array($topProduct) ? ($topProduct['unit'] ?? null) : ($topProduct->unit ?? null);
						$type = is_array($topProduct) ? ($topProduct['type'] ?? ($topProduct['product_type'] ?? 'Unspecified')) : ($topProduct->type ?? ($topProduct->product_type ?? 'Unspecified'));
						$price = is_array($topProduct) ? ($topProduct['price'] ?? 0) : ($topProduct->price ?? 0);
						$qty = is_array($topProduct) ? ($topProduct['total_qty'] ?? ($topProduct['quantity'] ?? 0)) : ($topProduct->total_qty ?? ($topProduct->quantity ?? 0));
					@endphp
					<div class="text-2xl font-medium truncate">{{ $name ?? '—' }}@if(!empty($unit)) · <span class="text-sm text-gray-600">{{ $unit }}</span>@endif</div>
					<div class="text-xs text-gray-500 mt-1">{{ $type }} · ₱{{ number_format($price, 2) }}</div>
					<div class="text-lg font-semibold mt-2">x{{ $qty }}</div>
				@elseif(!empty($productLabels) && count($productLabels))
					{{-- fallback: show first label/data pair --}}
					@php
						$firstLabel = $productLabels[0] ?? null;
						$firstQty = $productData[0] ?? 0;
					@endphp
					@if($firstLabel)
						<div class="text-2xl font-medium truncate">{{ $firstLabel }}</div>
						<div class="text-lg font-semibold mt-2">x{{ $firstQty }}</div>
					@else
						<div class="text-2xl">N/A</div>
					@endif
				@else
					<div class="text-2xl">N/A</div>
				@endif
			</div>
		</div>
		<div class="bg-white shadow rounded p-4 text-center">
			<div class="text-lg font-bold">Top Buyer</div>
			@if(!empty($topBuyer))
				<div class="text-xl font-semibold mt-2 truncate">{{ $topBuyer }}</div>
				<div class="text-sm text-gray-600 mt-1">Total items purchased: <span class="font-medium text-gray-800">{{ $topBuyerQty ?? 0 }}</span></div>
				
			@else
				<div class="text-sm text-gray-500 mt-2">No buyers found for the current filters.</div>
			@endif
		</div>
	</div>
		<div class="flex justify-end items-center gap-3 mb-2">

</div><br>
		
	<!-- Date Filter & Export -->
	<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
		<form method="GET" action="{{ route('admin.reports.productsreport') }}" class="flex gap-2 items-center" id="filtersForm">
			<label for="date_from" class="font-semibold">From:</label>
			<input type="date" id="date_from" name="date_from" value="{{ old('date_from', $date_from ?? request('date_from')) }}" class="border rounded px-2 py-1">
			<label for="date_to" class="font-semibold ml-2">To:</label>
			<input type="date" id="date_to" name="date_to" value="{{ old('date_to', $date_to ?? request('date_to')) }}" class="border rounded px-2 py-1">
			
			<input type="text" name="search" id="search" placeholder="Search Product Name or Category" value="{{ request('search') ?? '' }}" class="border rounded px-2 py-1 ml-2">
			<select name="status" class="border rounded px-2 py-1 ml-2">
				<option value="">All Status</option>
				<option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
				<option value="confirmed" @if(request('status')=='confirmed') selected @endif>Confirmed</option>
				<option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
				<option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
				<option value="reject" @if(request('status')=='reject') selected @endif>Rejected</option>
			</select>

			<button type="button" id="applyFiltersBtn" class="bg-green-600 text-white px-3 py-1 rounded">Filter</button>
		</form>
		<div class="flex items-center gap-3">
			<div id="loadingIndicator" class="ml-2 hidden flex items-center gap-2 text-sm text-gray-600">
			<svg class="animate-spin h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
				<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
			</svg>
			<span>Loading...</span>
		</div>
			
		</div>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const form = document.getElementById('filtersForm');
				const monthSelect = form.querySelector('select[name="month"]');
				const yearSelect = form.querySelector('select[name="year"]');
				const dateFrom = form.querySelector('input[name="date_from"]');
				const dateTo = form.querySelector('input[name="date_to"]');
				const searchInput = form.querySelector('input[name="search"]');
				const statusSelect = form.querySelector('select[name="status"]');
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
					applyBtn.addEventListener('click', function () {
						if (loadingIndicator) loadingIndicator.classList.remove('hidden');
						form.submit();
					});
				}

				// When month is selected, populate date_from and date_to with that month's bounds (using selected year if present)
				const pad = (n) => String(n).padStart(2, '0');
				function applyMonthToDates() {
					if (!monthSelect) return;
					const mVal = monthSelect.value ? parseInt(monthSelect.value, 10) : null;
					// If mVal is null the user picked "All Months" (empty value)
					if (!mVal) {
						// If a specific year is selected, set bounds to that year (Jan 1 - Dec 31)
						if (yearSelect && yearSelect.value && yearSelect.value !== '') {
							const yVal = parseInt(yearSelect.value, 10);
							const firstYear = `${yVal}-01-01`;
							const lastYear = `${yVal}-12-31`;
							if (dateFrom) dateFrom.value = firstYear;
							if (dateTo) dateTo.value = lastYear;
						} else {
							// All Years + All Months -> clear date bounds (no restriction)
							if (dateFrom) dateFrom.value = '';
							if (dateTo) dateTo.value = '';
						}
						return;
					}
					let yVal = yearSelect && yearSelect.value ? parseInt(yearSelect.value, 10) : (new Date()).getFullYear();
					const first = `${yVal}-${pad(mVal)}-01`;
					// JS Date months are 0-based; month parameter in Date(y, m, 0) returns last day of month m
					const lastDay = new Date(yVal, mVal, 0).getDate();
					const last = `${yVal}-${pad(mVal)}-${pad(lastDay)}`;
					if (dateFrom) dateFrom.value = first;
					if (dateTo) dateTo.value = last;
				}

				if (monthSelect) {
					monthSelect.addEventListener('change', function () {
						applyMonthToDates();
					});
					// if a month is already selected on load (we default to current month), apply bounds immediately
					if (monthSelect.value) applyMonthToDates();
				}

				// If user changes the year while a month is selected, update the date bounds too
				if (yearSelect) {
					yearSelect.addEventListener('change', function () {
						applyMonthToDates();
					});
				}

				// hide loading indicator on page show (keeps UX similar)
				window.addEventListener('pageshow', function () { if (loadingIndicator) setTimeout(() => loadingIndicator.classList.add('hidden'), 300); });
			});
		</script>
	

		<script>
			// Export PDF: open current filters in a new tab with export=pdf param
			document.addEventListener('DOMContentLoaded', function () {
				const exportBtn = document.getElementById('exportPdfBtn');
				const form = document.getElementById('filtersForm');
				const loadingIndicator = document.getElementById('loadingIndicator');
				if (exportBtn && form) {
					exportBtn.addEventListener('click', function (e) {
						e.preventDefault();
						// build query from form inputs (only include non-empty values)
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
	</div>



	<!-- Charts -->
	<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
		<div class="bg-white shadow rounded p-4">
			<div class="font-bold mb-2">Sales by Product Type</div>
			<div style="height:260px;">
				<canvas id="categoryPieChart" style="width:100%; height:100%; display:block;"></canvas>
			</div>
		</div>
		<div class="bg-white shadow rounded p-4">
			<div class="font-bold mb-2">Order Status Distribution</div>
			<div style="height:260px;">
				<canvas id="statusPieChart" style="width:100%; height:100%; display:block;"></canvas>
			</div>
		</div>
	</div>
	<!-- Export PDF button placed under charts, aligned right -->
	<div class="flex justify-end mb-4">
		<button id="exportPdfBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-lg shadow-sm transition-colors" title="Export current view as PDF">
			<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l-3-3m3 3l3-3" />
				<rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke-width="0"></rect>
			</svg>
			<span class="font-medium">Export PDF</span>
		</button>
	</div>
	<!-- Products Table -->
	<div class="overflow-x-auto bg-white shadow rounded">
		<table class="min-w-full">
			<thead class="bg-gray-100">
				<tr>
					<th class="py-2 px-4">Product Name</th>
					<th class="py-2 px-4">Product Type</th>
					<th class="py-2 px-4">Unit</th>
					<th class="py-2 px-4">Price</th>
					<th class="py-2 px-4">Quantity Sold</th>
					<th class="py-2 px-4">Buyer Name</th>
					<th class="py-2 px-4">Order Date</th>
					<th class="py-2 px-4">Order Status</th>
				</tr>
			</thead>
			<tbody>
				@forelse($recentOrderItems as $item)
				<tr>
					<td class="py-2 px-4">{{ $item->product_name }}</td>
					<td class="py-2 px-4">{{ $item->product_type ?? 'Unspecified' }}</td>
					<td class="py-2 px-4">{{ $item->unit ?? '-' }}</td>
					<td class="py-2 px-4">₱{{ number_format($item->price, 2) }}</td>
					<td class="py-2 px-4">{{ $item->quantity }}</td>
					<td class="py-2 px-4">{{ $item->buyer_name }}</td>
					<td class="py-2 px-4">{{ \Carbon\Carbon::parse($item->order_date)->format('Y-m-d') }}</td>
					<td class="py-2 px-4">{{ ucfirst($item->status) }}</td>
				</tr>
				@empty
				<tr>
					<td class="py-2 px-4" colspan="7">No orders found.</td>
				</tr>
				@endforelse
			</tbody>
		</table>
		<!-- Pagination -->
		<div class="flex justify-end p-4">
			{{ $recentOrderItems->links() }}
		</div>
	</div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	// Chart data from server
	// prefer product type aggregates when provided by controller
	const productTypeLabels = {!! json_encode($productTypeLabels ?? null) !!};
	const productTypeData = {!! json_encode($productTypeData ?? null) !!};
	const productLabels = productTypeLabels && productTypeLabels.length ? productTypeLabels : {!! json_encode($productLabels ?? []) !!};
	const productData = productTypeData && productTypeData.length ? productTypeData : {!! json_encode($productData ?? []) !!};
	const statusLabels = {!! json_encode($statusLabels ?? []) !!};
	const statusData = {!! json_encode($statusData ?? []) !!};

	// Ensure the status chart always shows these statuses (with zero if missing)
	// Added: ready_to_pick_up and to_delivery
	const defaultStatuses = ['pending', 'confirmed', 'ready_to_pick_up', 'to_delivery', 'completed', 'cancelled', 'reject'];
	// build a map from server labels -> data (case-insensitive), normalize common variants and user-friendly names
	const statusMap = {};
	for (let i = 0; i < statusLabels.length; i++) {
		let key = String(statusLabels[i] || '').toLowerCase().trim();
		// normalize common variants
		if (key === 'rejected') key = 'reject';
		if (key === 'canceled') key = 'cancelled';
		if (key === 'complete') key = 'completed';
		if (key === 'paid') key = 'confirmed';
		// normalize ready/out-for-delivery variants
		if (key.includes('ready') && key.includes('pick')) key = 'ready_to_pick_up';
		if (key.includes('ready') && key.includes('pickup')) key = 'ready_to_pick_up';
		if (key.includes('out') && key.includes('delivery')) key = 'to_delivery';
		if (key.includes('out_for_delivery')) key = 'to_delivery';
		if (key.includes('to_delivery')) key = 'to_delivery';
		if (key.includes('delivery') && !key.includes('pickup')) key = 'to_delivery';

		statusMap[key] = (statusMap[key] || 0) + Number(statusData[i] || 0);
	}

	// Convert default status keys to nicer display labels (replace underscores with spaces and title-case)
	// Override some labels for better UX (e.g., show 'Rejected' instead of 'Reject')
	const displayOverrides = { 'reject': 'Rejected' };
	const statusLabelsFinal = defaultStatuses.map(s => displayOverrides[s] ?? s.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' '));
	const statusDataFinal = defaultStatuses.map(s => statusMap[s] || 0);

	const categoryPieCtx = document.getElementById('categoryPieChart').getContext('2d');
	new Chart(categoryPieCtx, {
		type: 'pie',
		data: {
			labels: productLabels.length ? productLabels : ['No Data'],
			datasets: [{
				data: productData.length ? productData : [1],
				backgroundColor: ['#4ade80', '#60a5fa', '#fbbf24', '#f97316', '#a78bfa']
			}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			plugins: {
				legend: {
					position: 'bottom',
					labels: { boxWidth: 12, padding: 8, usePointStyle: true }
				}
			}
		}
	});

	const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
	new Chart(statusPieCtx, {
		type: 'pie',
		data: {
			labels: statusLabelsFinal.length ? statusLabelsFinal : ['No Data'],
			datasets: [{
				data: statusDataFinal.length ? statusDataFinal : [1],
				// colors aligned to defaultStatuses order: pending, confirmed, ready to pick up, out for delivery, completed, cancelled, reject
				backgroundColor: ['#fbbf24', '#22c55e', '#6366f1', '#8b5cf6', '#60a5fa', '#ef4444', '#a21caf']
			}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			plugins: {
				legend: {
					position: 'bottom',
					labels: { boxWidth: 12, padding: 8, usePointStyle: true }
				}
			}
		}
	});
</script>

@endsection