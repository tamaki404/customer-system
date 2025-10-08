<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order - {{ $order->order_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 30px;
            color: #333;
            font-size: 11px;
            line-height: 1.5;
        }
        
        .header {
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .logo-section {
            display: table-cell;
            width: 20%;
            vertical-align: middle;
            text-align: left;
        }
        
        .logo-placeholder {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #555;
            height: 60px;
            line-height: 40px;
        }
        
        .header-center {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: center;
        }
        
        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }
        
        .header-info-box {
            border: 2px solid #000;
            padding: 8px;
            background: #f9f9f9;
            text-align: left;
            font-size: 10px;
            line-height: 1.6;
        }
        
        .header-info-box div {
            margin: 2px 0;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin: 8px 0;
        }
        
        .sub-header {
            font-size: 11px;
            color: #555;
            font-style: italic;
        }
        
        .supplier-details {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-box {
            border: 2px solid #000;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .info-box h3 {
            font-size: 13px;
            color: #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
            font-weight: bold;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #000;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            color: #000;
        }
        
        .table-section {
            margin: 25px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            background: #e0e0e0;
            border-left: 4px solid #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: 10px;
        }
        
        thead {
            background: #e0e0e0;
        }
        
        th {
            padding: 10px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #000;
        }
        
        td {
            padding: 10px 6px;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #000;
            font-size: 9px;
            color: #555;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        .contact-info {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top">

            <div class="header-center">
                <h1>CUSTOMER ORDER</h1>
                <p class="company-name">Sunny & Scramble</p>
                <p class="sub-header">Purchase Order Document</p>
            </div>
            <div class="header-right">
                <div class="header-info-box">
                    <div><strong>Order ID:</strong> {{ $order->order_id }}</div>
                    <div><strong>PO ID:</strong> {{ $order->po_id ?? 'N/A' }}</div>
                    <div><strong>Created:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
        <div class="supplier-details">
            <div class="info-row">
                <span class="info-label">Supplier:</span>
                <span class="info-value"><strong>{{ $order->supplier->company_name }}</strong></span>
            </div>
            <div class="contact-info">
                Mobile: {{ $order->supplier->mobile }} | 
                Tel: {{ $order->supplier->tele }} | 
                Email: {{ $order->supplier->user->email_address }}
            </div>
        </div>
    </div>

    <div class="content-body">

        {{-- ORDER ITEMS TABLE --}}
        <div class="table-section">
            <div class="section-title">Order Items</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Product</th>
                        <th style="width: 15%;">Quantity</th>
                        <th style="width: 13%;">Condition</th>
                        <th style="width: 14%;">Packaging</th>
                        <th style="width: 14%;">Labeling Req.</th>
                        <th style="width: 14%;">Rejection Param.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: left; font-weight: bold;">{{ $item->product->name }}</td>
                            <td>
                                <strong>
                                {{ $item->product->measurement_type === 'Kilos'
                                    ? number_format($item->placed_kilos, 2) . ' kg'
                                    : $item->placed_heads . ' heads' }}
                                </strong>
                            </td>
                            <td>{{ $item->product->req->condition ?? '—' }}</td>
                            <td style="text-align: left; font-size: 9px;">
                                <div><strong>P:</strong> {{ $item->product->req->primary_packaging ?? '—' }}</div>
                                <div><strong>S:</strong> {{ $item->product->req->secondary_packaging ?? '—' }}</div>
                            </td>
                            <td>{{ $item->product->req->labeling_requirement ?? '—' }}</td>
                            <td>{{ $item->product->req->rejection_parameter ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- DELIVERY SCHEDULE TABLE --}}
        @if($order->deliveries && $order->deliveries->count() > 0)
        <div class="table-section">
            <div class="section-title">Delivery Schedule</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 18%;">Delivery ID</th>
                        <th style="width: 15%;">Scheduled Date</th>
                        <th style="width: 15%;">Delivered Date</th>
                        <th style="width: 15%;">Planned Qty</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 10%;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDeliveries = $order->deliveries->count();
                        $completedDeliveries = $order->deliveries->where('status', 'Completed')->count();
                    @endphp
                    
                    @foreach ($order->deliveries as $delivery)
                        @php
                            $plannedHeads = $delivery->deliveryItems->sum('planned_heads');
                            $plannedKilos = $delivery->deliveryItems->sum('planned_kilos');
                            $totalPlanned = $plannedHeads > 0 ? $plannedHeads . ' heads' : number_format($plannedKilos, 2) . ' kg';
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $delivery->delivery_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M j, Y') }}</td>
                            <td>{{ $delivery->delivered_date ? \Carbon\Carbon::parse($delivery->delivered_date)->format('M j, Y') : '—' }}</td>
                            <td><strong>{{ $totalPlanned }}</strong></td>
                            <td>
                                <span style="border: 1px solid #000; padding: 2px 6px; font-size: 9px;">
                                    {{ ucfirst($delivery->status) }}
                                </span>
                            </td>
                            @if ($loop->first)
                                <td rowspan="{{ $totalDeliveries }}" style="vertical-align: middle; font-weight: bold; font-size: 12px;">
                                    {{ $completedDeliveries }}/{{ $totalDeliveries }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- RECEIPTS TABLE --}}
        @if($order->receipts && $order->receipts->count() > 0)
        <div class="table-section">
            <div class="section-title">Receipts</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">#</th>
                        <th style="width: 25%;">Receipt ID</th>
                        <th style="width: 20%;">Total Amount</th>
                        <th style="width: 17%;">Status</th>
                        <th style="width: 30%;">Date Issued</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->receipts as $receipt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $receipt->receipt_id }}</td>
                            <td style="font-weight: bold;">₱{{ number_format($receipt->total_amount, 2) }}</td>
                            <td>
                                <span style="border: 1px solid #000; padding: 2px 6px; font-size: 9px;">
                                    {{ ucfirst($receipt->status) }}
                                </span>
                            </td>
                            <td>{{ $receipt->created_at->format('F j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
        <p>© {{ date('Y') }} Sunny & Scramble. All rights reserved.</p>
    </div>

</body>
</html>