{{-- resources/views/pdf/orders/delivery_receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return slip - {{ $delivery->delivery_id }}</title>
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
        
        .company-details {
            text-align: center;
            font-size: 12px;
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
            width: 40%;
            font-weight: bold;
            color: #000;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
            color: #000;
        }
        
        .table-container {
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        
        thead {
            background: #e0e0e0;
        }
        
        th {
            padding: 10px 6px;
            text-align: center;
            font-size: 11px;
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
        
        .packaging-details {
            text-align: left;
            font-size: 9px;
            line-height: 1.4;
        }
        
        .packaging-details div {
            margin: 2px 0;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 50px;
            margin-bottom: 30px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }
        
        .signature-line {
            border-top: 2px solid #000;
            margin-top: 70px;
            padding-top: 8px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .signature-role {
            font-size: 9px;
            color: #555;
            font-style: italic;
            margin-top: 3px;
        }
        
        .notes-section {
            border-top: 2px dashed #000;
            padding-top: 15px;
            margin-top: 20px;
            background: #f9f9f9;
            padding: 15px;
        }
        
        .notes-section h4 {
            font-size: 12px;
            color: #000;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .note-item {
            margin: 8px 0;
            font-size: 10px;
        }
        
        .note-label {
            font-weight: bold;
            color: #000;
        }
        
        .disclaimer {
            margin-top: 10px;
            font-style: italic;
            color: #555;
            font-size: 9px;
            border-left: 3px solid #000;
            padding-left: 10px;
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
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: bold;
        }
        
        .empty-cell {
            background: #f0f0f0;
            color: #888;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top">

            <div class="header-center">
                <h1>RETURN REPORT</h1>
                <p class="company-name">Sunny & Scramble</p>
                <p class="sub-header">Customer return slip</p>
            </div>
            <div class="header-right">
                <div class="header-info-box">
                    <div><strong>Delivery ID:</strong> {{ $delivery->delivery_id }}</div>
                    <div><strong>Order ID:</strong> {{ $delivery->order_id }}</div>
                    <div><strong>Delivered at:</strong> {{ \Carbon\Carbon::parse($delivery->delivered_at)->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
  
    </div>

    {{-- DELIVERY INFORMATION --}}
    <div class="info-section">
        <div class="info-box">
            <h3>Delivery Information</h3>
            
            <div class="info-row">
                <span class="info-label">Company Name:</span>
                <span class="info-value">{{ $delivery->order->supplier->company_name }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Delivery ID:</span>
                <span class="info-value">{{ $delivery->delivery_id }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Order ID:</span>
                <span class="info-value">{{ $delivery->order_id }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Scheduled Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge">{{ ucfirst($delivery->status) }} at {{ $delivery->delivered_at }}</span>
                </span>
            </div>
            
            @if($delivery->order->supplier->delivery)
                <div class="info-row">
                    <span class="info-label">Delivery Frequency:</span>
                    <span class="info-value">{{ $delivery->order->supplier->delivery->delivery_frequency ?? '—' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Delivery Address:</span>
                    <span class="info-value">
                        {{ $delivery->order->supplier->delivery->delivery_address_1 ?? '' }}
                        @if($delivery->order->supplier->delivery->delivery_address_2)
                            <br>{{ $delivery->order->supplier->delivery->delivery_address_2 }}
                        @endif
                        @if($delivery->order->supplier->delivery->delivery_address_3)
                            <br>{{ $delivery->order->supplier->delivery->delivery_address_3 }}
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- DELIVERY ITEMS TABLE --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Product</th>
                    <th style="width: 12%;">Condition</th>
                    <th style="width: 6%;">Heads</th>
                    <th style="width: 6%;">Kilos</th>
                    <th style="width: 6%;">Received</th>
                    <th style="width: 7%;">Variance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left; font-weight: bold;">
                            {{ $item->orderItem->product->name ?? '—' }}
                        </td>
                        <td>{{ $item->orderItem->product->req->condition ?? '—' }}</td>
             
                        <td>

                                <strong>{{ $item->planned_heads }}</strong> heads
                        </td>
                        <td>
                                <strong>{{ number_format($item->planned_kilos ?? 0, 2) }}</strong> kg
                      
                        </td>
                        <td>
                            <strong>
                                    @if ($item->orderItem->product->measurement_type === "Heads")
                                        {{ $item->received_heads}} heads
                                    @elseif ($item->orderItem->product->measurement_type === "Kilos")
                                        {{ $item->received_kilos}}kg
                                    @elseif ($item->orderItem->product->measurement_type === "Heads&Kilos")
                                        {{ $item->received_heads}} heads | {{ $item->received_kilos}}kg
                                    @else
                                        —
                                    @endif
                            </strong>
                        </td>
                            @php
                                $varianceHeads = ($item->planned_heads ?? 0) - ($item->received_heads ?? 0);
                                $varianceKilos = ($item->planned_kilos ?? 0) - ($item->received_kilos ?? 0);
                                $hasVariance = $varianceHeads != 0 || $varianceKilos != 0;
                            @endphp
                            {{-- VARIANCE DISPLAY --}}
                            <td>
                            @if ($item->status === "Delivered")
                                @if (!$hasVariance)
                                    <span style="color: green;">Exact</span>
                                @else
                                    @if ($varianceHeads != 0)
                                        <span style="color: red;">
                                            {{ $varianceHeads > 0 ? '-' : '+' }}{{ abs($varianceHeads) }} heads
                                        </span>
                                        @if ($varianceKilos != 0)
                                            <br>
                                        @endif
                                    @endif
                                    @if ($varianceKilos != 0)
                                        <span style="color: red;">
                                            {{ $varianceKilos > 0 ? '-' : '+' }}{{ number_format(abs($varianceKilos), 2) }} kg
                                        </span>
                                    @endif
                                @endif
                                
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    {{-- NOTES & INSTRUCTIONS --}}
    <div class="notes-section">
        <h4>Report: </h4>
        <p>{{ $delivery->feedback }}</p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
        <p>© {{ date('Y') }} Sunny & Scramble. All rights reserved.</p>
    </div>

</body>
</html>