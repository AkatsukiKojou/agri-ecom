<!-- filepath: resources/views/admin/emails/new_order_notification.blade.php -->
<div style="font-family: Arial, sans-serif; background: #f8fafc; padding: 32px;">
    <div style="max-width: 520px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px #0001; padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 48px; margin-bottom: 8px;">
            <h1 style="color: #22c55e; font-size: 2rem; margin: 0;">New Order Notification</h1>
        </div>

        <p style="font-size: 1.1rem; color: #222; margin-bottom: 16px;">
            You have received a <strong style="color: #22c55e;">new order</strong>.
        </p>

        <div style="background: #f1f5f9; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px;">
            <p style="margin: 0 0 6px 0;"><strong>Order ID:</strong> <span style="color: #2563eb;">{{ $order->id }}</span></p>
            <p style="margin: 0 0 6px 0;"><strong>Buyer:</strong> {{ $order->name }} <span style="color: #64748b;">({{ $order->phone }})</span></p>
            <p style="margin: 0;"><strong>Address:</strong> {{ $order->address }}</p>
        </div>

        <h3 style="color: #2563eb; margin-bottom: 10px;">Products in your order:</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="text-align: left; padding: 8px; font-size: 1rem;">Product</th>
                    <th style="text-align: center; padding: 8px; font-size: 1rem;">Quantity</th>
                    <th style="text-align: right; padding: 8px; font-size: 1rem;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $item['product_name'] }}</td>
                    <td style="padding: 8px; text-align: center; border-bottom: 1px solid #e5e7eb;">{{ $item['quantity'] }}</td>
                    <td style="padding: 8px; text-align: right; border-bottom: 1px solid #e5e7eb;">₱{{ number_format($item['price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ url('/admin/orders/'.$order->id) }}" style="background: #22c55e; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-weight: bold; font-size: 1rem;">
                View Order Details
            </a>
        </div>
    </div>
    <p style="text-align: center; color: #94a3b8; font-size: 0.95rem; margin-top: 32px;">
        This is an automated notification from your system.
    </p>
</div>