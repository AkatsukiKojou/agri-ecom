@extends('admin.layout')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-extrabold mb-10 text-center text-green-900 tracking-tight drop-shadow-lg">Reports</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Kanban Column for Completed Transactions -->
                <div class="bg-gradient-to-r from-green-500 to-green-200 p-6 rounded-xl shadow-lg border border-green-200 flex flex-col gap-2">
                    <h2 class="text-xl font-extrabold text-green-900 mb-2">All Completed Transactions</h2>
                    <div class="flex flex-col gap-1">
                        <div>
                            <span class="block text-green-800 text-lg font-semibold">Total Completed</span>
                            <span class="text-3xl font-extrabold text-green-800">{{ $services_completed + $products_completed }}</span>
                        </div>
                        <div>
                            <span class="block text-green-800 text-lg font-semibold mt-2">Total Income</span>
                            <span class="text-3xl font-extrabold text-green-800">₱{{ number_format($services_completed_income + $completed_income, 2) }}</span>
                        </div>
                    </div>
                </div>
            
            <!-- Orders Completed -->
            <!-- Orders Completed Kanban -->
            <div class="bg-gradient-to-r from-green-400 to-blue-100 p-6 rounded-xl shadow-lg border border-blue-200 flex flex-col gap-2">
                <h2 class="text-xl font-extrabold text-green-900 mb-2 flex items-center"><span class="mr-2">✅</span> Orders Completed</h2>
                <div class="flex flex-col gap-1">
                    <div>
                        <span class="block text-green-800 text-lg font-semibold">Total Orders Completed</span>
                        <span class="text-3xl font-extrabold text-blue-800">{{ $products_completed }}</span>
                    </div>
                    <div>
                        <span class="block text-green-800 text-lg font-semibold mt-2">Total Income</span>
                        <span class="text-3xl font-extrabold text-blue-800">₱{{ number_format($completed_income, 2) }}</span>
                    </div>
                </div>
            </div>

       

            <!-- Services Completed Kanban -->
            <div class="bg-gradient-to-r from-green-400 to-green-100 p-6 rounded-xl shadow-lg border border-green-200 flex flex-col gap-2">
                <h2 class="text-xl font-extrabold text-green-900 mb-2 flex items-center"><span class="mr-2">🛠️</span> Services Completed</h2>
                <div class="flex flex-col gap-1">
                    <div>
                        <span class="block text-green-800 text-lg font-semibold">Total Services Completed</span>
                        <span class="text-3xl font-extrabold text-green-800">{{ $services_completed }}</span>
                    </div>
                    <div>
                        <span class="block text-green-800 text-lg font-semibold mt-2">Total Income</span>
                        <span class="text-3xl font-extrabold text-green-800">₱{{ number_format($services_completed_income, 2) }}</span>
                    </div>
                </div>
            </div><br><br>
            <!-- Donut Charts Row -->
            <div class="flex flex-row gap-8 justify-center items-center mb-8 w-full">
                <!-- Orders Donut Chart -->
                <div class="relative flex flex-col items-center justify-center w-40 h-40">
                    <canvas id="ordersDonutChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span id="ordersDonutValue" class="text-4xl font-extrabold text-gray-800">{{ $orders }}</span>
                        <span class="text-base font-semibold text-gray-500">Total Orders</span>
                    </div>
                </div><br>
                <!-- Bookings Donut Chart -->
                <div class="relative flex flex-col items-center justify-center w-40 h-40">
                    <canvas id="bookingsDonutChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span id="bookingsDonutValue" class="text-4xl font-extrabold text-green-700">{{ $bookings }}</span>
                        <span class="text-base font-semibold text-gray-500">Total Bookings</span>
                    </div>
                </div> <br>
                <!-- Products Donut Chart -->
                <div class="relative flex flex-col items-center justify-center w-40 h-40">
                    <canvas id="productsDonutChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span id="productsDonutValue" class="text-4xl font-extrabold text-green-700">{{ $products }}</span>
                        <span class="text-base font-semibold text-gray-500">Total Products</span>
                    </div>
                </div><br>
                <!-- Services Donut Chart -->
                <div class="relative flex flex-col items-center justify-center w-40 h-40">
                    <canvas id="servicesDonutChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span id="servicesDonutValue" class="text-4xl font-extrabold text-emerald-700">{{ $services }}</span>
                        <span class="text-base font-semibold text-gray-500">Total Services</span>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Orders Chart
                    var ctx = document.getElementById('ordersDonutChart').getContext('2d');
                    var orders = {{ $orders }};
                    var maxOrders = Math.max(orders, 100);
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [orders, maxOrders - orders],
                                backgroundColor: [
                                    '#7dd3fc', // blue
                                    '#bbf7d0'  // green
                                ],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false }
                            },
                            responsive: false,
                            maintainAspectRatio: false,
                        }
                    });
                    // Bookings Chart
                    var ctxBookings = document.getElementById('bookingsDonutChart').getContext('2d');
                    var bookings = {{ $bookings }};
                    var maxBookings = Math.max(bookings, 100);
                    new Chart(ctxBookings, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [bookings, maxBookings - bookings],
                                backgroundColor: [
                                    '#86efac', // green
                                    '#bbf7d0'  // light green
                                ],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false }
                            },
                            responsive: false,
                            maintainAspectRatio: false,
                        }
                    });
                    // Products Chart
                    var ctxProducts = document.getElementById('productsDonutChart').getContext('2d');
                    var products = {{ $products }};
                    var maxProducts = Math.max(products, 100);
                    new Chart(ctxProducts, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [products, maxProducts - products],
                                backgroundColor: [
                                    '#facc15', // yellow
                                    '#fef9c3'  // light yellow
                                ],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false }
                            },
                            responsive: false,
                            maintainAspectRatio: false,
                        }
                    });
                    // Services Chart
                    var ctxServices = document.getElementById('servicesDonutChart').getContext('2d');
                    var services = {{ $services }};
                    var maxServices = Math.max(services, 100);
                    new Chart(ctxServices, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [services, maxServices - services],
                                backgroundColor: [
                                    '#34d399', // emerald
                                    '#d1fae5'  // light emerald
                                ],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false }
                            },
                            responsive: false,
                            maintainAspectRatio: false,
                        }
                    });
                });
            </script>


             </div>
        <!-- Top Products Sold and Top Services Booked Section (moved to bottom) -->
        <div class="flex flex-row gap-8 justify-center items-start mt-10 w-full">
            <!-- Top Products Sold -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 w-96">
                <h2 class="text-xl font-bold text-yellow-700 mb-4 flex items-center"><span class="mr-2"></span> Top Products Sold</h2>
                <div class="space-y-6">
                    @php
                        $maxQty = $topProducts->max('total_qty') ?? 1;
                    @endphp
                    @forelse($topProducts as $product)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-gray-800">{{ $product->name }}</span>
                                <span class="font-bold text-gray-700">{{ $product->total_qty }}</span>
                            </div>
                            <div class="w-full h-3 bg-gray-200 rounded-full">
                                <div class="h-3 rounded-full bg-blue-500 transition-all duration-300" style="width: {{ ($product->total_qty / $maxQty) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500 py-2">No data available.</div>
                    @endforelse
                </div>
            </div>
            <!-- Top Services Booked -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 w-96">
                <h2 class="text-xl font-bold text-emerald-700 mb-4 flex items-center"><span class="mr-2">🛠️</span> Top Services Booked</h2>
                <div class="space-y-6">
                    @php
                        $maxBooked = $topServices->max('total_booked') ?? 1;
                    @endphp
                    @forelse($topServices as $service)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-gray-800">{{ $service->service_name }}</span>
                                <span class="font-bold text-gray-700">{{ $service->total_booked }}</span>
                            </div>
                            <div class="w-full h-3 bg-gray-200 rounded-full">
                                <div class="h-3 rounded-full bg-emerald-500 transition-all duration-300" style="width: {{ ($service->total_booked / $maxBooked) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500 py-2">No data available.</div>
                    @endforelse
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection
