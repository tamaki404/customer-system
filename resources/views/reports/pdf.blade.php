<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
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
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                <td>{{ $order->status }}</td>
                <td>₱{{ number_format($order->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
