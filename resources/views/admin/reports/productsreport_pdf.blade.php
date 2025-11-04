<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Report</title>
    <style>
        /* Minimal PDF styles for Dompdf (inline so we don't rely on Tailwind build)
           Use simple table layout and readable font sizes suitable for A4 portrait. */
        body { font-family: DejaVu Sans, Arial, sans-serif; color:#111; font-size:12px; }
        .summary { display:flex; gap:12px; margin-bottom:16px; }
        .card { border:1px solid #ddd; padding:8px 12px; border-radius:4px; flex:1; }
        .card h3 { margin:0 0 6px 0; font-size:13px; }
        .card .value { font-size:18px; font-weight:700; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; font-size:11px; }
        th { background:#f2f2f2; }
        .muted { color:#666; font-size:10px; }
    </style>
</head>
<body>
    <h1>Products Report</h1>
    <p class="muted">@if(!empty($date_from)) From: {{ $date_from }} @endif @if(!empty($date_to)) To: {{ $date_to }} @endif</p>

    <div class="summary">
        <div class="card">
            <h3>Total Products</h3>
            <div class="value">{{ $totalProducts ?? 0 }}</div>
        </div>
        <div class="card">
            <h3>Total Product Sales</h3>
            <div class="value">₱{{ number_format($totalProductSales ?? 0, 2) }}</div>
            <div class="muted">(Based on selected status)</div>
        </div>
        <div class="card">
            <h3>Number of Orders</h3>
            <div class="value">{{ $numberOfOrders ?? 0 }}</div>
        </div>
        <div class="card">
            <h3>Top Products Sold</h3>
            @if(!empty($topProductsSold) && count($topProductsSold))
                <ol style="padding-left:16px; margin:8px 0 0 0">
                    @foreach($topProductsSold as $p)
                        <li style="margin-bottom:6px;">{{ $p->name }} @if(!empty($p->unit))· {{ $p->unit }}@endif — x{{ $p->total_qty ?? 0 }}<br><span class="muted">{{ $p->type ?? 'Unspecified' }} · ₱{{ number_format($p->price ?? 0, 2) }}</span></li>
                    @endforeach
                </ol>
            @else
                <div class="muted">N/A</div>
            @endif
        </div>
    </div>
    {{-- Status totals summary (compact) --}}
    @php
        $statusTotals = [];
        if (!empty($recentOrderItemsAll)) {
            foreach ($recentOrderItemsAll as $it) {
                $st = $it->status ?? 'unknown';
                $statusTotals[$st] = ($statusTotals[$st] ?? 0) + 1;
            }
        }
        $preferredOrder = ['pending','confirmed','completed','cancelled','reject','unknown'];
    @endphp
    <div style="margin-bottom:12px;">
        <table style="width:auto; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:6px; background:#f5f5f5;">Status</th>
                    <th style="border:1px solid #ddd; padding:6px; background:#f5f5f5;">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preferredOrder as $st)
                    @if(isset($statusTotals[$st]))
                        <tr>
                            <td style="border:1px solid #ddd; padding:6px;">{{ ucfirst(str_replace('_',' ', $st)) }}</td>
                            <td style="border:1px solid #ddd; padding:6px;">{{ $statusTotals[$st] }}</td>
                        </tr>
                    @endif
                @endforeach
                @foreach($statusTotals as $key => $val)
                    @if(!in_array($key, $preferredOrder))
                        <tr>
                            <td style="border:1px solid #ddd; padding:6px;">{{ ucfirst(str_replace('_',' ', $key)) }}</td>
                            <td style="border:1px solid #ddd; padding:6px;">{{ $val }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 style="margin-top:12px">Products Table</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product Type</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Quantity Sold</th>
                <th>Buyer Name</th>
                <th>Order Date</th>
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($recentOrderItemsAll) && count($recentOrderItemsAll))
                @foreach($recentOrderItemsAll as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_type ?? 'Unspecified' }}</td>
                        <td>{{ $item->unit ?? '-' }}</td>
                        <td>₱{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->buyer_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->order_date)->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="8">No orders found.</td></tr>
            @endif
        </tbody>
    </table>

</body>
</html>