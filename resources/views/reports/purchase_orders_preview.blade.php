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
        <div class="img-title">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image" width="100" class="ownerImage">
                <p>
                    <span style="font-size: 25px; font-weight: bold;">Sunny & Scramble</span>
                    <span>558 General Luna Street, malanday San Mateo, Rizal Philippines</span>
                </p>
        </div>
      
        <p style="margin: 5px; font-size: 18px; margin-top: 20px; font-weight: bold;">PURCHASE ORDERS REPORT</p>
        <div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%;">
            <p class="dates-summary">
                <span>Report period: <strong>{{ $dateRangeLabel }}</strong></span>
                <span>Date range: <strong>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</strong></span>
                <span>Genrated on: <strong>{{ now()->format('M d, Y g:i A') }}</strong></span>
            </p>
            @if($purchaseOrders->count() > 0)
                <p class="total-summary">
                    <span>Total records: <strong>{{ $purchaseOrders->count() }}</strong></span>
                    <span>Total value: <strong>₱{{ number_format($purchaseOrders->sum('grand_total'), 2) }}</strong></span>
                    <span>Total paid: <strong>₱{{ number_format($purchaseOrders->sum('paid_amount'), 2) }}</strong></span>
                </p>
            @endif
        </div>


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

    <style>
        .dates-summary, .total-summary{
            display: flex;
            flex-direction: column;



        }
        .dates-summary span{
            font-size: 14px;
            margin: 0;
        }
        .dates-summary span strong{
            font-size: 14px;
            margin: 0;
        }
        .header{
            display: flex;
            flex-direction: column;
            width: 100%;
            height: auto;
            align-items: center;
            justify-items: center;

        }
        .header .img-title{
            display:flex;
            flex-direction: row;
            width: 100%;
            height: auto;
            gap: 10px;
            align-content: center;
            justify-content: center
        }
        .img-title img{
            height: 50px;
            margin: 0;
            width: auto;
        }
        .img-title p{
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .img-title p span{
            color: #333;
            font-size: 14px;
            margin: 0;
        }
    </style>
    

</body>
</html>