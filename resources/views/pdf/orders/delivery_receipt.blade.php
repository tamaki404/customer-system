{{-- resources/views/pdf/orders/delivery_receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Receipt - {{ $delivery->delivery_id }}</title>
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
                <h1>DELIVERY RECEIPT</h1>
                <p class="company-name">Sunny & Scramble</p>
                <p class="sub-header">Official Record of Goods Delivered</p>
            </div>
            <div class="header-right">
                <div class="header-info-box">
                    <div><strong>Delivery ID:</strong> {{ $delivery->delivery_id }}</div>
                    <div><strong>Order ID:</strong> {{ $delivery->order_id }}</div>
                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
        <div class="company-details">
            <strong>From:</strong> {{ $delivery->order->supplier->company_name }}
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
                    <span class="status-badge">{{ ucfirst($delivery->status) }}</span>
                </span>
            </div>
            
            @if($delivery->order->supplier->delivery)
                <div class="info-row">
                    <span class="info-label">Delivery Frequency:</span>
                    <span class="info-value">{{ $delivery->order->supplier->delivery->delivery_frequency ?? '—' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Receiving Time:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($delivery->order->supplier->delivery->receiving_time)->format('g:i A') }}</span>
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
                    <th style="width: 18%;">Packaging</th>
                    <th style="width: 6%;">Heads</th>
                    <th style="width: 6%;">Kilos</th>
                    <th style="width: 13%;">Received</th>
                    <th style="width: 20%;">Remarks</th>
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
                            <div class="packaging-details">
                                <div><strong>Primary:</strong> {{ $item->orderItem->product->req->primary_packaging ?? '—' }}</div>
                                <div><strong>Secondary:</strong> {{ $item->orderItem->product->req->secondary_packaging ?? '—' }}</div>
                            </div>
                        </td>
                        <td>

                                <strong>{{ $item->planned_heads }}</strong> heads
                        </td>
                        <td>
                                <strong>{{ number_format($item->planned_kilos ?? 0, 2) }}</strong> kg
                      
                        </td>
                        <td class="empty-cell">___________</td>
                        <td style="text-align: left;">{{ $item->remarks ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- SIGNATURES --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Delivered By
            </div>
            <div class="signature-role">SNS Representative</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Received By
            </div>
            <div class="signature-role">Authorized Signatory</div>
        </div>
    </div>

    {{-- NOTES & INSTRUCTIONS --}}
    <div class="notes-section">
        <h4>Additional Information</h4>
        
        <div class="note-item">
            <span class="note-label">Delivery Instructions:</span> 
            {{ $delivery->order->supplier->delivery->delivery_instructions ?? 'None provided' }}
        </div>
        
        <div class="note-item">
            <span class="note-label">PPE Requirements:</span> 
            {{ $delivery->order->supplier->delivery->ppe_requirements ?? 'N/A' }}
        </div>
        
        <div class="disclaimer">
            Please verify all goods upon receipt. Any discrepancies must be reported immediately to the supplier. 
            This document serves as proof of delivery and acceptance of goods.
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
        <p>© {{ date('Y') }} Sunny & Scramble. All rights reserved.</p>
    </div>

</body>
</html>