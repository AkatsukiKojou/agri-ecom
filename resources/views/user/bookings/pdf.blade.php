<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt</title>
    <style>
        @page { size: A4; margin: 24mm 18mm 24mm 18mm; }
        html, body { height: 100%; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #222;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: auto;
            padding: 0;
            border: 2px solid #228B22;
            border-radius: 16px;
            box-sizing: border-box;
            background: #fff;
        }
        h1 { color: #228B22; font-size: 2.2em; margin-bottom: 0.2em; text-align: center; }
        .header { text-align: center; margin-bottom: 1em; }
        .logo { width: 60px; height: 60px; display: block; margin: 0 auto 0.5em auto; }
        .section-title { font-size: 1.15em; color: #228B22; margin-top: 1.2em; margin-bottom: 0.5em; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
        td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; font-size: 1em; }
        .label { color: #555; font-weight: bold; width: 40%; }
        .value { color: #222; width: 60%; }
        .status { padding: 4px 12px; border-radius: 8px; font-weight: bold; display: inline-block; margin-bottom: 1em; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        .footer { text-align: center; color: #666; font-size: 0.95em; margin-top: 2em; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <br><br><br>
<div class="container no-break">
    <div class="header">
        <img src="{{ file_exists(public_path('logo.png')) ? public_path('logo.png') : '' }}" class="logo" alt="Logo" onerror="this.style.display='none'">
        <h1>Agri-Ecom</h1>
    </div>
    <div class="section-title">Booking Receipt</div>
    <table>
        <tr><td class="label">Reference #</td><td class="value">BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td class="label">Booked By</td><td class="value">{{ $booking->user->name ?? 'N/A' }}</td></tr>
    <tr><td class="label">Training Service</td><td class="value">{{ $booking->service->service_name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Location</td><td class="value">{{ $booking->service->location ?? '-' }}</td></tr>
        <tr><td class="label">Start Date</td><td class="value">{{ \Carbon\Carbon::parse($booking->booking_start)->format('M d, Y') }}</td></tr>
        <tr><td class="label">End Date</td><td class="value">
            @php
                $start = $booking->booking_start ? \Carbon\Carbon::parse($booking->booking_start) : null;
                $duration = intval($booking->service->duration ?? 0);
                $end = ($start && $duration > 0) ? $start->copy()->addDays($duration) : null;
            @endphp
            {{ $end ? $end->format('M d, Y') : '-' }}
        </td></tr>
        <tr><td class="label">Duration</td><td class="value">{{ $booking->service->duration ?? '-' }}</td></tr>
        <tr><td class="label">Attendees</td><td class="value">{{ $booking->attendees }}</td></tr>
        <tr><td class="label">Trainer Name</td><td class="value">{{ $booking->service->trainer_name ?? '-' }}</td></tr>
        <tr><td class="label">Trainer Credentials</td><td class="value">{{ $booking->service->trainer_credentials ?? '-' }}</td></tr>
    </table>
    <div class="section-title">Payment Summary</div>
    <table>
        <tr><td class="label">Payment Method</td><td class="value">{{ ucfirst($booking->payment_method) }}</td></tr>
        <tr><td class="label">Total Price</td><td class="value">{{ number_format($booking->total_price, 2) }}</td></tr>
        @if($booking->payment_method === 'gcash')
        <tr><td class="label">Amount Paid</td><td class="value">{{ number_format($booking->downpayment ?? 0, 2) }}</td></tr>
        @endif
        <tr><td class="label">Balance</td><td class="value">
            @php
                $paid = $booking->amount_paid ?? 0;
                $balance = ($booking->total_price ?? 0) - $paid;
                $downpayment = $booking->downpayment ?? 0;
                $isGcash = $booking->payment_method === 'gcash';
                if ($isGcash) {
                    $balance = ($booking->total_price ?? 0) - $downpayment;
                }
            @endphp
            {{ number_format($balance, 2) }}
        </td></tr>
        @if($booking->payment_method === 'gcash' && !empty($booking->gcash_reference))
        <tr><td class="label">GCash Reference #</td><td class="value">{{ $booking->gcash_reference }}</td></tr>
        @endif
    </table>
    <div class="section-title">Status</div>
    @php
        $statusClasses = [
            'approved' => 'status status-approved',
            'pending' => 'status status-pending',
            'cancelled' => 'status status-cancelled',
            'completed' => 'status status-completed',
        ];
        $statusClass = $statusClasses[$booking->status] ?? 'status';
    @endphp
    <span class="{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
    <div class="section-title">Contact</div>
    <table>
        <tr><td class="label">Contact Person</td><td class="value">{{ $booking->service->contact_person ?? '-' }}</td></tr>
        <tr><td class="label">Contact Info</td><td class="value">{{ $booking->service->contact_info ?? '-' }}</td></tr>
        <tr><td class="label">Admin Email</td><td class="value">{{ $booking->service->admin && $booking->service->admin->profile ? $booking->service->admin->profile->email : 'support@example.com' }}</td></tr>
    </table>
    <div class="footer">
        Thank you for your booking! Please keep this receipt for your records.<br>
        If you have any questions, contact us at <span class="font-semibold">{{ $booking->service->admin && $booking->service->admin->profile ? $booking->service->admin->profile->email : 'support@example.com' }}</span>.
    </div>
</div>
</body>
</html>
