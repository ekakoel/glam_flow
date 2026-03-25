<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 12px; }
        .container { width: 100%; padding: 24px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 8px; color: #9f1239; }
        .muted { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #fff1f2; }
        .total { margin-top: 18px; font-size: 16px; font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        @php
            $items = $booking->bookingItems;
            $computedTotal = (float) ($items->sum('subtotal') ?: 0);
            $fallbackTotal = (float) ($booking->payment?->amount ?? $booking->service->price);
            $totalAmount = $computedTotal > 0 ? $computedTotal : $fallbackTotal;
        @endphp
        <div class="title">MUA Invoice</div>
        <div class="muted">Invoice Number: {{ $invoiceNumber }}</div>

        <table>
            <tr>
                <th>Customer Name</th>
                <td>{{ $booking->customer->name }}</td>
            </tr>
            <tr>
                <th>Services</th>
                <td>
                    @if($items->isNotEmpty())
                        @foreach($items as $item)
                            <div>{{ $item->service->name }} x {{ $item->people_count }} person</div>
                        @endforeach
                    @else
                        {{ $booking->service->name }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td>{{ $booking->booking_date?->format('d M Y') }} {{ substr((string) $booking->booking_time, 0, 5) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($booking->payment?->status ?? 'pending') }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="total">
            Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
    </div>
</body>
</html>
