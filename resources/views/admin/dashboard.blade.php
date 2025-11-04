@extends('admin.layout')

@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .card:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15)!important;
        transform: translateY(-4px) scale(1.03);
        transition: all 0.2s;
    }
    .dashboard-icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    .card-title {
        letter-spacing: 1px;
    }

    /* Force FullCalendar grid to be visible */
    #calendar, .fc {
        background: #fff !important;
        color: #222 !important;
        border-radius: 8px;
        border: 1px solid #28a745;
        min-height: 220px;
    }
    .fc .fc-scrollgrid, .fc .fc-scrollgrid-section {
        background: #fff !important;
        color: #222 !important;
    }
    .fc .fc-daygrid-day {
        border: 1px solid #e0e0e0 !important;
    }
    .fc .fc-daygrid-day-number {
        color: #28a745 !important;
        font-weight: bold;
    }
    .fc .fc-event {
        background: #28a745 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 4px !important;
        font-size: 0.95rem;
    }
</style>
@endpush

<div class="container py-4">
    <div class="row mb-4 g-3 justify-content-center">
        <!-- Top row: 3 cards -->
        <div class="col-md-4">
            <a href="{{ route('products.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-success" style="font-size:2rem;">
                            <i class="bi bi-box-seam dashboard-icon text-success"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Products</h6>
                        <p class="card-text fs-4 fw-bold mb-0">{{ $totalProducts }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('services.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-info" style="font-size:2rem;">
                            <i class="bi bi-mortarboard dashboard-icon text-info"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Training Services</h6>
                        <p class="card-text fs-4 fw-bold mb-0">{{ $totalServices }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-danger" style="font-size:2rem;">
                            <i class="bi bi-cash-stack dashboard-icon text-danger"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Completed Transaction</h6>
                        <p class="card-text fs-4 fw-bold mb-0 mt-2 text-danger">
                            Total: ₱{{ number_format($productSales + $serviceSales, 2) }}
                        </p>
                    </div>
                </div>
            </a>
        </div>
       
    </div>
    <div class="row mb-4 g-3 justify-content-center">
        <!-- Second row: 3 cards -->
         <div class="col-md-4">
            <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-warning" style="font-size:2rem;">
                            <i class="bi bi-calendar-check dashboard-icon text-warning"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Bookings</h6>
                        <p class="card-text fs-4 fw-bold mb-0">{{ $totalBookings }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-primary" style="font-size:2rem;">
                            <i class="bi bi-cart-check dashboard-icon text-primary"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Orders</h6>
                        <p class="card-text fs-4 fw-bold mb-0">{{ $totalOrders }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="" class="text-decoration-none">
                <div class="card shadow-sm border-0 bg-light h-100 clickable-card">
                    <div class="card-body text-center">
                        <div class="mb-2 text-dark" style="font-size:2rem;">
                            <i class="bi bi-people dashboard-icon text-dark"></i>
                        </div>
                        <h6 class="card-title fw-semibold">Total Buyers</h6>
                        <p class="card-text fs-4 fw-bold mb-0">{{ $totalBuyers }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row mb-4 g-3 justify-content-center">
        <!-- Bottom row: 3 cards -->
        

    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold">
                    Monthly Sales
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-semibold">
                    Bookings Calendar
                </div>
                <div class="card-body">
                    @php
                        use Carbon\Carbon;
                        $selectedMonth = request('month') ? Carbon::createFromFormat('Y-m', request('month')) : Carbon::now();
                        $year = $selectedMonth->year;
                        $month = $selectedMonth->month;
                        $startOfMonth = Carbon::create($year, $month, 1);
                        $endOfMonth = $startOfMonth->copy()->endOfMonth();
                        $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1=Mon, 7=Sun
                        $daysInMonth = $endOfMonth->day;
                        $bookingsByDate = collect($bookingEvents)->groupBy('start');
                        $prevMonth = $selectedMonth->copy()->subMonth()->format('Y-m');
                        $nextMonth = $selectedMonth->copy()->addMonth()->format('Y-m');
                    @endphp
                    <div class="w-full max-w-xl mx-auto">
                        <div class="flex justify-between items-center mb-2">
                            <form method="GET" class="inline">
                                <input type="hidden" name="month" value="{{ $prevMonth }}">
                                <button type="submit" class="px-2 py-1 rounded bg-green-200 text-green-800 hover:bg-green-300">&lt;</button>
                            </form>
                            <span class="text-lg font-bold text-green-700">{{ $selectedMonth->format('F Y') }}</span>
                            <form method="GET" class="inline">
                                <input type="hidden" name="month" value="{{ $nextMonth }}">
                                <button type="submit" class="px-2 py-1 rounded bg-green-200 text-green-800 +
                                ------------------------------------hover:bg-green-300">&gt;</button>
                            </form>
                        </div>
                        <div class="grid grid-cols-7 gap-1 bg-green-100 rounded-t">
                            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                <div class="text-center py-1 font-semibold text-green-800">{{ $day }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-1 border border-green-200 rounded-b bg-white">
                            @for($i = 1; $i < $startDayOfWeek; $i++)
                                <div></div>
                            @endfor
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = Carbon::create($year, $month, $day)->format('Y-m-d');
                                    $bookings = $bookingsByDate[$date] ?? [];
                                @endphp
                                <div class="h-20 border border-green-100 p-1 align-top relative">
                                    <div class="text-xs text-gray-500 absolute top-1 left-1">{{ $day }}</div>
                                    @foreach($bookings as $booking)
                                        <div class="mt-5 text-xs bg-green-500 text-white rounded px-1 py-0.5 truncate shadow">
                                            {{ $booking['title'] ?? 'Booking' }}
                                        </div>
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart - defensive initialization
    (function() {
        var salesCanvas = document.getElementById('salesChart');
    // Serialize PHP arrays to JSON for JS safely
    var salesLabels = {!! json_encode($salesLabels ?? []) !!};
    var salesData = {!! json_encode($salesData ?? []) !!};

        if (!salesCanvas) {
            console.warn('Sales chart canvas not found.');
            return;
        }

        // Ensure arrays
        if (!Array.isArray(salesLabels)) salesLabels = [];
        if (!Array.isArray(salesData)) salesData = [];

        // If there's no data, replace canvas with a friendly placeholder
        if (salesLabels.length === 0 || salesData.length === 0 || salesData.every(function(v){ return v === null || v === undefined; })) {
            var holder = document.createElement('div');
            holder.className = 'text-center text-muted py-4';
            holder.innerText = 'No sales data to display.';
            salesCanvas.parentNode.replaceChild(holder, salesCanvas);
            return;
        }

        try {
            var ctx = salesCanvas.getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Sales',
                        data: salesData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        } catch (e) {
            console.error('Chart.js failed to render sales chart:', e);
            var errHolder = document.createElement('div');
            errHolder.className = 'text-center text-danger py-4';
            errHolder.innerText = 'Unable to render sales chart.';
            salesCanvas.parentNode.replaceChild(errHolder, salesCanvas);
        }
    })();

    // FullCalendar for Bookings
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    var eventsData = {!! json_encode($bookingEvents) !!};
    if (!Array.isArray(eventsData) || eventsData.length === 0) {
        eventsData = [
            { title: 'No Approved Bookings', start: new Date().toISOString().slice(0,10), allDay: true }
        ];
    }
    try {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: eventsData
        });
        calendar.render();
        console.log('FullCalendar rendered!');
    } catch (e) {
        console.error('FullCalendar error:', e);
    }
});
</script>
@endpush