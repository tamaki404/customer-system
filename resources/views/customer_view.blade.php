@extends('layout')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .customerFrame {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px #0001;
        }
        .customerFrame h2 {
            color: #1976d2;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .customerFrame p {
            font-size: 1.1rem;
            color: #333;
            margin: 8px 0;
        }
        .customerFrame hr {
            margin: 32px 0;
            border: none;
            border-top: 1.5px solid #eee;
        }
        .customerFrame h3 {
            color: #ffde59;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .customerFrame table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            background: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
        }
        .customerFrame th, .customerFrame td {
            padding: 12px 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            font-size: 1rem;
            color: #333;
        }
        .customerFrame th {
            background: #ffde59;
            color: #333;
            font-weight: 600;
        }
        .customerFrame tr:hover {
            background: #e3f2fd;
            cursor: pointer;
        }
        .no-receipts {
            color: #888;
            text-align: center;
            margin: 2rem 0;
            font-size: 1.1rem;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/customer_view.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="customerFrame" style="max-width:900px;margin:40px auto;padding:30px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;">

        <div style="margin-bottom: 18px;">
            <form action="{{ url('/customer/accept/' . $customer->id) }}" method="POST" style="display:inline-block;margin-right:10px;">
                @csrf
                <button type="submit" style="background:#1976d2;color:#fff;padding:8px 22px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Accept</button>
            </form>
            <form action="{{ url('/customer/suspend/' . $customer->id) }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" style="background:#d32f2f;color:#fff;padding:8px 22px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Suspend</button>
            </form>
        </div>
        
        <h2 style="margin-bottom:24px;">Customer Details</h2>

        <p><strong>Customer ID:</strong> {{ $customer->id }}</p>
        <p><strong>Username:</strong> {{ $customer->username }}</p>
        <p><strong>Store Name:</strong> {{ $customer->store_name}}</p>
        <p><strong>Account Status:</strong> {{ $customer->acc_status}}</p>

        <hr style="margin:32px 0;">
        <h3>Receipts</h3>
        @if(isset($receipts) && count($receipts) > 0)
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $receipt)
                        <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                            <td>{{ $receipt->receipt_number }}</td>
                            <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                            <td>{{ $receipt->purchase_date }}</td>
                            <td>{{ $receipt->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-receipts">No receipts found for this customer.</div>
        @endif


s





    </div>
</body>
</html>

@endsection