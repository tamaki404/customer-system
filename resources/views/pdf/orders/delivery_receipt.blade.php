{{-- resources/views/pdf/orders/delivery_receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Receipt - {{ $delivery->delivery_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .sub-header {
            text-align: center;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .info-box {
            width: 48%;
            border: 1px solid #999;
            padding: 10px;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            text-decoration: underline;
        }
        .info-box p {
            margin: 2px 0;
            line-height: 1.3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #666;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background: #f3f3f3;
        }
        .totals {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
        }
        .signature-box {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 45%;
            text-align: center;
        }
        .signature p {
            margin-top: 60px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
            font-size: 12px;
        }
        .note {
            margin-top: 25px;
            border-top: 1px dashed #aaa;
            padding-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>Delivery Receipt</h1>
        <p><strong>Sunny & Scramble</strong></p>
        <p class="sub-header">Official record of goods delivered to the customer</p>
    </div>

    {{-- BASIC DETAILS --}}
    <div class="info-grid">
        <div class="info-box">
            <h3>Delivery Details</h3>
            <p><strong>Company name:</strong> {{$delivery->order->supplier->company_name}}</p>
            <p><strong>Delivery ID:</strong> {{ $delivery->delivery_id }}</p>
            <p><strong>Order ID:</strong> {{ $delivery->order_id }}</p>
            <p><strong>Scheduled Date:</strong> {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($delivery->status) }}</p>
            @if($delivery->order->supplier->delivery)
                <p><strong>Frequency:</strong> {{ $delivery->order->supplier->delivery->delivery_frequency ?? '—' }}</p>
                <p><strong>Receiving Time:</strong>{{ \Carbon\Carbon::parse($delivery->order->supplier->delivery->receiving_time)->format(' g:i A') }}</p>
                <p><strong>Delivery address 1:</strong> {{ $delivery->order->supplier->delivery->delivery_address_1 ?? '—' }}</p>
                <p><strong>Delivery address 2:</strong> {{ $delivery->order->supplier->delivery->delivery_address_2 ?? '—' }}</p>
                <p><strong>Delivery address 3:</strong> {{ $delivery->order->supplier->delivery->delivery_address_3 ?? '—' }}</p>

            @endif
        </div>
    </div>

    {{-- DELIVERY ITEMS TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Condition</th>
                <th>Packaging</th>

                <th>Heads/Kilos</th>
                <th>Received</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->orderItem->product->name ?? '—' }}</td>
                    <td>{{ $item->orderItem->product->req->condition ?? '—' }}</td>
                    <td>
                        <div>
                            <strong>prim:</strong>  {{ $item->orderItem->product->req->primary_packaging ?? '—' }},
                        </div>
                        <div>
                            <strong>sec:</strong>  {{ $item->orderItem->product->req->secondary_packaging ?? '—' }},
                        </div>                
                    </td>

                    <td>
                        @if ($item->orderItem->product->measurement_type === "Kilos")
                            {{ number_format($item->planned_kilos ?? 0, 2)  }}kg
                        @else
                            {{ $item->planned_heads }} heads
                        @endif
                    </td>
                    <td></td>
                    <td>{{ $item->remarks ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    {{-- SIGNATURES --}}
    <div class="signature-box">
        <div class="signature">
            <p>Delivered By<br><small>(Supplier Representative)</small></p>
        </div>
        <div class="signature">
            <p>Received By<br><small>(Customer Representative)</small></p>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="note">
 
        <p><strong>Delivery Instructions:</strong> {{ $delivery->order->supplier->delivery->delivery_instructions ?? 'None provided' }}</p>
        <p><strong>PPE Requirements:</strong> {{ $delivery->order->supplier->delivery->ppe_requirements ?? 'N/A' }}</p>
        <p><em>Please verify all goods upon receipt. Discrepancies should be reported immediately to the supplier.</em></p>
    </div>

    <div class="footer" style="text-align:center; margin-top:30px;">
        <p>Generated on {{ now()->format('F j, Y h:i A') }}</p>
        <p>© {{ date('Y') }} Sunny & Scramble. All rights reserved.</p>
    </div>

</body>
</html>
