<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>

        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }

        .status-title { background: #ddd; padding: 5px; margin-top: 15px; font-weight: bold; }
        .status-Completed { color: green; font-weight: bold; }
        .status-Pending { color: orange; }
        .status-Processing { color: blue; }
        .status-Cancelled { color: red; }
        .status-Rejected { color: darkred; }
    </style>
</head>
<body>

    <h2>Orders Report</h2>
    <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>

    <p>Total Orders: {{ $ordersCount }}</p>
    <p>
        Completed: {{ $completedOrders }} |
        Pending: {{ $pendingOrders }} |
        Processing: {{ $processingOrders }} |
        Cancelled: {{ $cancelledOrders }} |
        Rejected: {{ $rejectedOrders }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Store Name</th>
                <th>Status</th>
                <th>Total Quantity</th>
                <th>Total Price</th>
                <th>Updated at</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->store_name }}</td>
                    <td class="status-{{ $order->status }}">{{ $order->status }}</td>
                    <td>{{ $order->total_quantity }}</td>
                    <td>{{ number_format($order->total_price, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->action_at)->format('M d, Y g:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
