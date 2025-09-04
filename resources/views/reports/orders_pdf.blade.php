<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 20px;
            line-height: 1.3;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #f8912a;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary-section {
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #ddd;
        }
        
        .summary-item:last-child {
            border-right: none;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .section-title {
            background: #f8912a;
            color: white;
            padding: 8px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        th, td { 
            border: 1px solid #ccc; 
            padding: 5px; 
            text-align: left; 
        }
        
        th { 
            background: #f2f2f2; 
            font-weight: bold;
            font-size: 9px;
        }
        
        /* .status-Completed { color: #28a745; font-weight: bold; }
        .status-Pending { color: #ffc107; font-weight: bold; }
        .status-Processing { color: #007bff; font-weight: bold; }
        .status-Cancelled { color: #6c757d; font-weight: bold; }
        .status-Rejected { color: #dc3545; font-weight: bold; } */
        
        .currency { text-align: right; }
        .center { text-align: center; }
        
        .page-break { page-break-before: always; }
        
        .detailed-table {
            font-size: 9px;
        }
        
        .order-group {
            background: #f9f9f9;
            font-weight: bold;
        }
        
        .product-row {
            background: white;
        }
        
        .order-total {
            background: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Orders Report</h1>
        <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i A') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="margin: 0 0 15px 0;">Summary Overview</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($ordersCount) }}</div>
                <div class="summary-label">Total Orders</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($completedOrders) }}</div>
                <div class="summary-label">Completed</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($processingOrders) }}</div>
                <div class="summary-label">Processing</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($pendingOrders) }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($cancelledOrders) }}</div>
                <div class="summary-label">Cancelled</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($rejectedOrders) }}</div>
                <div class="summary-label">Rejected</div>
            </div>
        </div>
        <div style="margin-top: 15px; text-align: center;">
            <strong>Total Revenue: ₱{{ number_format($totalRevenue, 2) }}</strong> |
            <strong>Average Order Value: ₱{{ number_format($averageOrderValue, 2) }}</strong>
        </div>
    </div>

    <!-- Orders Summary Table -->
    <div class="section-title">Orders Summary</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Order ID</th>
                <th style="width: 25%;">Store Name</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 12%;">Total Qty</th>
                <th style="width: 15%;">Total Price</th>
                <th style="width: 21%;">Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->store_name }}</td>
                    <td class="status-{{ $order->status }}">{{ $order->status }}</td>
                    <td class="center">{{ $order->total_quantity }}</td>
                    <td class="currency">₱{{ number_format($order->total_price, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->action_at)->format('M d, Y g:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <!-- Footer -->
    <div style="position: fixed; bottom: 20px; right: 20px; font-size: 9px; color: #666;">
        Page <span class="pagenum"></span>
    </div>
</body>
</html>