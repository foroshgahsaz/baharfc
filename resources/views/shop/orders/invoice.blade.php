<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاکتور {{ $order->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; color: #0f172a; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: right; }
        th { background: #f1f5f9; }
        .muted { color: #64748b; font-size: 12px; }
        .totals { margin-top: 16px; text-align: left; direction: ltr; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        @auth
            <a href="{{ route('orders.invoice', [$order, 'download' => 1]) }}">دانلود PDF</a>
        @endauth
    </div>

    <h1>فاکتور فروش</h1>
    <p class="muted">شماره سفارش: {{ $order->number }} — تاریخ: {{ $order->paid_at?->format('Y/m/d H:i') ?? $order->created_at->format('Y/m/d H:i') }}</p>

    <p>
        <strong>{{ $order->recipient_name }}</strong><br>
        {{ $order->province }}، {{ $order->city }}<br>
        {{ $order->address_line }}<br>
        تلفن: {{ $order->recipient_phone }}
    </p>

    <table>
        <thead>
            <tr>
                <th>کالا</th>
                <th>تعداد</th>
                <th>قیمت واحد (تومان)</th>
                <th>جمع (تومان)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price) }}</td>
                    <td>{{ number_format($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>جمع جزء: {{ number_format($order->subtotal) }} تومان</p>
        @if ($order->discount_total > 0)
            <p>تخفیف: {{ number_format($order->discount_total) }} تومان</p>
        @endif
        <p>ارسال: {{ number_format($order->shipping_total) }} تومان</p>
        <p><strong>مبلغ قابل پرداخت: {{ number_format($order->total) }} تومان</strong></p>
    </div>

    <p class="muted" style="margin-top: 24px;">وضعیت: {{ $order->statusLabel() }}</p>
</body>
</html>
