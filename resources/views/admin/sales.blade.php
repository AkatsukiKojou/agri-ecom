@extends('admin.layout')
@section('content')
<div class="container py-4">
    <h2 class="text-2xl font-bold mb-4">Sales Report</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Total Sales</h5>
            <p class="card-text fs-3 fw-bold text-success">₱{{ number_format($totalSales, 2) }}</p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">Monthly Sales</div>
        <div class="card-body">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-semibold">Recent Sales</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->name }}</td>
                            <td>₱{{ number_format($sale->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesLabels) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($salesData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endpush
