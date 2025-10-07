<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order - {{ $order->order_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 40px;
            color: #333;
        }
        h1, h2, h3, h4, h5 {
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: left;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .details-box p {
            margin: 3px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-section {
            margin-top: 40px;
        }
        .footer {
            margin-top: 40px;
            border-top: 2px solid #333;
            padding-top: 10px;
            text-align: center;
            font-size: 13px;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Customer Order</h1>
        <p><strong>Sunny & Scramble</strong></p>
        <p>Created at: {{ $order->created_at->format('F j, Y g:i A') }}</p>

        <div class="details-box">
            <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
            <p><strong>PO ID:</strong> {{ $order->po_id ?? 'N/A' }}</p>
            <p><strong>Supplier:</strong> {{ $order->supplier->company_name }}</p>
            <p>
                <span>📞 {{ $order->supplier->mobile }}</span> |
                <span>☎ {{ $order->supplier->tele }}</span> |
                <span>✉ {{ $order->supplier->user->email_address }}</span>
            </p>
        </div>
    </div>

    <div class="content-body">

        <div class="table-section">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity (Heads/Kilos)</th>
                        <th>Condition</th>
                        <th>Packaging</th>
                        <th>Labeling requirement</th>
                        <th>Rejection parameter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>
                                {{ $item->product->measurement_type === 'Kilos'
                                    ? $item->placed_kilos . ' kg'
                                    : $item->placed_heads . ' heads' }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                        </tr>
                    @endforeach
                
                </tbody>
            </table>
        </div>

        @if($order->deliveries && $order->deliveries->count() > 0)
        <div class="table-section">
            <h3>Delivery Schedule</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Delivery ID</th>
                        <th>Scheduled Date</th>
                        <th>Delivered Date</th>
                        <th>Planned Qty</th>
                        <th>Status</th>
                        <th>Completion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->deliveries as $delivery)
                        @php
                            $plannedHeads = $delivery->deliveryItems->sum('planned_heads');
                            $plannedKilos = $delivery->deliveryItems->sum('planned_kilos');
                            $totalPlanned = $plannedHeads > 0 ? $plannedHeads . ' heads' : $plannedKilos . ' kg';

                            // compute completion ratio (delivered / total scheduled)
                            $totalDeliveries = $order->deliveries->count();
                            $completedDeliveries = $order->deliveries->where('status', 'Completed')->count();
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $delivery->delivery_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</td>
                            <td>{{ $delivery->delivered_date ? \Carbon\Carbon::parse($delivery->delivered_date)->format('F j, Y') : '—' }}</td>
                            <td>{{ $totalPlanned }}</td>
                            <td>{{ ucfirst($delivery->status) }}</td>
                            @if ($loop->last)
                                <td rowspan="{{ $totalDeliveries }}" style="vertical-align: middle; font-weight: bold;">
                                    {{ $completedDeliveries }}/{{ $totalDeliveries }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($order->receipts && $order->receipts->count() > 0)
        <div class="table-section">
            <h3>Receipts</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date Issued</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->receipts as $receipt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $receipt->receipt_id }}</td>
                            <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                            <td>{{ ucfirst($receipt->status) }}</td>
                            <td>{{ $receipt->created_at->format('F j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

    <div class="footer">
        <p>Generated by Sunny & Scramble Supply System</p>
        <p>© {{ date('Y') }} All rights reserved.</p>
    </div>

</body>
</html>
