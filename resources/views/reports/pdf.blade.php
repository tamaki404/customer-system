<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
        .status-title { background: #ddd; padding: 5px; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>

    <h3> Summary Section (KPIs)</h3>
    <table class="summary-table">
        <tr><td>Total Orders</td><td>{{ $totalOrders }}</td></tr>
        <tr><td>Completed Orders</td><td>{{ $completedOrders }}</td></tr>
        <tr><td>Total Sales</td><td>₱ {{ number_format($totalSales, 2) }}</td></tr>
        <tr><td>Average Order Value</td><td>₱ {{ number_format($averageOrderValue, 2) }}</td></tr>
        <tr><td>Payment Success Rate</td><td>{{ $paymentSuccessRate }}%</td></tr>
    </table>

<h3>Sales by Status</h3>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Number of Orders</th>
            <th>Total Revenue</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salesByStatus as $status => $data)
            <tr>
                <td>{{ $status }}</td>
                <td>{{ $data['count'] }}</td>
                <td>₱ {{ number_format($data['revenue'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
