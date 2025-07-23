@extends('layout')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/customer_view.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="customerFrame" style="max-width:900px;margin:40px auto;padding:30px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;">
        
        <h2 style="margin-bottom:24px;">Customer Details</h2>

        <p><strong>Customer ID:</strong> {{ $customer->id }}</p>
        <p><strong>Username:</strong> {{ $customer->username }}</p>
        <p><strong>Store Name:</strong> {{ $customer->store_name}}</p>
        <p><strong>Account Status:</strong> {{ $customer->acc_status}}</p>

        <hr style="margin:32px 0;">
        <h3>Receipts</h3>
        @if(isset($receipts) && count($receipts) > 0)
            <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin-top:16px;">
                <thead>
                    <tr style="background:#f7f7fa;">
                        <th style="padding:8px;text-align:left;">Receipt #</th>
                        <th style="padding:8px;text-align:left;">Amount</th>
                        <th style="padding:8px;text-align:left;">Date</th>
                        <th style="padding:8px;text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $receipt)
                        <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                            <td style="padding:8px;">{{ $receipt->receipt_number }}</td>
                            <td style="padding:8px;">₱{{ number_format($receipt->total_amount, 2) }}</td>
                            <td style="padding:8px;">{{ $receipt->purchase_date }}</td>
                            <td style="padding:8px;">{{ $receipt->status }}</td>
                           
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @else
            <p style="color:#888;">No receipts found for this customer.</p>
        @endif


s





    </div>
</body>
</html>

@endsection