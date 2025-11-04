<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Services Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        h1 { margin-bottom: 0.25rem; }
        p { margin: 0.1rem 0; }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <h1>Services Report</h1>
        <p>Total Services: {{ $totalServices ?? 0 }}</p>
        <p>Total Bookings: {{ $totalBookings ?? 0 }}</p>
        <p>Total Booking Sales (Completed only): ₱{{ number_format($totalBookingSales ?? 0, 2) }}</p>

        <h2>Booking Status Distribution</h2>
        @php
            // compute status totals from the provided collection
            $statusTotals = [];
            if (!empty($servicesAll)) {
                foreach ($servicesAll as $s) {
                    $st = $s->status ?? 'unknown';
                    $statusTotals[$st] = ($statusTotals[$st] ?? 0) + 1;
                }
            }
            // ensure a consistent order for display
            $preferredOrder = ['pending','ongoing','approved','confirmed','completed','cancelled','reject','rejected','no_show','unknown'];
        @endphp

        <div style="margin: 8px 0 12px 0;">
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
        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Unit</th>
                    <th>Booked By</th>
                    <th>Price</th>
                    <th>Attendees</th>
                    <th>Booking Start</th>
                    <th>Booking End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicesAll as $b)
                    <tr>
                        <td>{{ $b->service->service_name ?? $b->service_name ?? '—' }}</td>
                        <td>{{ $b->service->unit ?? $b->unit ?? '—' }}</td>
                        <td>{{ $b->user->name ?? $b->full_name ?? '—' }}</td>
                        <td>₱{{ number_format($b->total_price ?? ($b->service->price ?? 0), 2) }}</td>
                        <td>{{ $b->attendees ?? $b->quantity ?? 0 }}</td>
                        <td>{{ optional($b->booking_start ?? $b->booked_at ?? $b->created_at)->format('Y-m-d') }}</td>
                        <td>{{ !empty($b->booking_end) ? \Carbon\Carbon::parse($b->booking_end)->format('Y-m-d') : '—' }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $b->status ?? '—')) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>