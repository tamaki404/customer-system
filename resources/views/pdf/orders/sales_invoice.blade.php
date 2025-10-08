<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - {{ $order->order_id }}</title>
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
            border: 2px solid #333;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            color: #666;
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
            padding: 8px;
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
            color: #333;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 8px 0;
        }
        
        .sub-header {
            font-size: 11px;
            color: #666;
            font-style: italic;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-box {
            border: 2px solid #333;
            border-radius: 5px;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .info-box h3 {
            font-size: 13px;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ffde59;
            font-weight: bold;
        }
        
        .info-row {
            margin: 5px 0;
            font-size: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            color: #333;
        }
        
        .table-section {
            margin: 25px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            background: #ffde59;
            border-left: 4px solid #333;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
            border-radius: 5px;
            margin-top: 10px;
            overflow: hidden;
        }
        
        thead {
            background: #e0e0e0;
        }
        
        th {
            padding: 10px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #333;
        }
        
        td {
            padding: 10px 6px;
            text-align: center;
            border: 1px solid #333;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        
        .total-row {
            background: #ffde59 !important;
            font-weight: bold;
            font-size: 12px;
        }
        
        .footer-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .footer-box {
            display: table-cell;
            width: 48%;
            padding: 15px;
            border: 2px solid #333;
            border-radius: 5px;
            background: #f9f9f9;
            vertical-align: top;
        }
        
        .footer-box:first-child {
            margin-right: 4%;
        }
        
        .footer-box h4 {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        .footer-box p {
            margin: 4px 0;
            font-size: 10px;
            color: #333;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #333;
            font-size: 9px;
            color: #666;
        }
        
        .footer p {
            margin: 3px 0;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top">

            <div class="header-center">
                <h1>SALES INVOICE</h1>
                <p class="company-name">Sunny & Scramble</p>
                <p class="sub-header">Official Invoice Document</p>
            </div>
            <div class="header-right">
                <div class="header-info-box">
                    <div><strong>Invoice #:</strong> {{ $order->order_id }}</div>
                    <div><strong>Order ID:</strong> {{ $order->order_id }}</div>
                    <div><strong>PO ID:</strong> {{ $order->po_id }}</div>
                    <div><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- BILL TO SECTION --}}
    <div class="info-section">
        <div class="info-box">
            <h3>Bill To</h3>
            
            <div class="info-row">
                <span class="info-label">Company:</span>
                <span class="info-value"><strong>{{ $order->supplier->company_name }}</strong></span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Contact:</span>
                <span class="info-value">
                    Mobile: {{ $order->supplier->mobile_no }} | 
                    Tel: {{ $order->supplier->telephone_no }} | 
                    Email: {{ $order->supplier->user->email_address }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Billing Address:</span>
                <span class="info-value">
                    {{ implode(', ', array_filter([
                        $order->supplier->office_street,
                        $order->supplier->office_subdivision,
                        $order->supplier->office_barangay,
                        $order->supplier->office_city,
                    ])) }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Delivery Address:</span>
                <span class="info-value">
                    {{ implode(', ', array_filter([
                        $order->supplier->home_street,
                        $order->supplier->home_subdivision,
                        $order->supplier->home_barangay,
                        $order->supplier->home_city,
                    ])) }}
                </span>
            </div>
        </div>
    </div>

    {{-- INVOICE ITEMS TABLE --}}
    <div class="table-section">
        <div class="section-title">Invoice Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 18%;">Product ID</th>
                    <th style="width: 32%;">Description</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 12%;">Quantity</th>
                    <th style="width: 18%;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->set_id }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $item->product->name }}</td>
                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                        <td><strong>{{ $item->quantity }}</strong></td>
                        <td><strong>₱{{ number_format($item->total_price, 2) }}</strong></td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="5" style="text-align: right; font-size: 12px;">TOTAL AMOUNT DUE:</td>
                    <td style="font-size: 14px;">₱{{ number_format($items->sum('total_price'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- FOOTER NOTES --}}
    <div class="footer-section">
        <div class="footer-box" style="margin-right: 20px;">
            <h4>Payment Terms</h4>
            <p>• Payment due within 30 days of invoice date</p>
            <p>• Late payments may incur additional charges</p>
            <p>• Please reference invoice number when making payment</p>
        </div>
        <div class="footer-box">
            <h4>Thank You For Your Business!</h4>
            <p>We appreciate your continued partnership.</p>
            <p>For questions about this invoice, please contact us at the details above.</p>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }}</p>
        <p>© {{ date('Y') }} Sunny & Scramble. All rights reserved.</p>
    </div>

</body>
</html>