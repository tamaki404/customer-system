<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Orders Report</title>
    <style>
        @media print {
            @page {
                margin: 20mm;
                size: A4 landscape;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .date-range-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .date-range-info h3 {
            margin: 0 0 10px 0;
            color: #555;
        }
        
        .date-range-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: #333;
            background-color: #fff;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .table thead {
            background-color: #f9f9f9;
            color: #444;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        .table th,
        .table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            text-align: left;
            word-wrap: break-word;
        }

        .table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .table td:first-child {
            font-weight: 500;
            color: #222;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        
        .summary {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Purchase Orders Report</h1>
    </div>
    
    <div class="date-range-info">
        <h3>Report Period: {{ $dateRangeLabel }}</h3>
        <p><strong>Date range:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        <p><strong>Generated on:</strong> {{ now()->format('M d, Y g:i A') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Company</th>
                <th>Contact Person</th>
                <th>Total Amount (₱)</th>
                <th>Paid (₱)</th>
                <th>Available Balance (₱)</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td>{{ $po->po_id }}</td>
                    <td>{{ $po->company_name }}</td>
                    <td>{{ $po->receiver_name }}</td>
                    <td>₱{{ number_format($po->grand_total, 2) }}</td>
                    <td>₱{{ number_format($po->paid_amount ?? 0, 2) }}</td>
                    <td>₱{{ number_format($po->available_balance ?? 0, 2) }}</td>
                    <td>{{ $po->payment_status ?? 'Unpaid' }}</td>
                    <td>{{ $po->status }}</td>
                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="no-data">No purchase orders found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($purchaseOrders->count() > 0)
    <div class="summary">
        <p><strong>Total Records:</strong> {{ $purchaseOrders->count() }}</p>
        <p><strong>Total Value:</strong> ₱{{ number_format($purchaseOrders->sum('grand_total'), 2) }}</p>
        <p><strong>Total Paid:</strong> ₱{{ number_format($purchaseOrders->sum('paid_amount'), 2) }}</p>
    </div>
    @endif
</body>
</html>