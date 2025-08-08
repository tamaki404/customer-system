<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Store</th>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total Price</th>
            </tr>
        </thead>
<tbody>
@php
    $grandTotal = 0;
@endphp

@foreach($orders as $orderId => $orderGroup)
    @php
        $orderTotal = $orderGroup->sum('total_price');
        $grandTotal += $orderTotal;
    @endphp
    <tr>
        <td>{{ $orderGroup->first()->user->store_name }}</td>
        <td>{{ $orderId }}</td>
        <td>{{ $orderGroup->first()->created_at->format('Y-m-d') }}</td>
        <td>{{ $orderGroup->first()->status }}</td>
        <td>&#8369;{{ number_format($orderTotal, 2) }}</td>
    </tr>
@endforeach

<!-- Grand Total Row -->
<tr>
    <td colspan="4" style="text-align: right; font-weight: bold;">Grand Total</td>
    <td style="font-weight: bold;">&#8369;{{ number_format($grandTotal, 2) }}</td>
</tr>
</tbody>


    </table>
</body>
</html>
