@extends('layout')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    {{-- <style>
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
      
    </style> --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/customer_view.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="customerFrame">

        {{-- <div>
            <form action="{{ url('/customer/accept/' . $customer->id) }}" method="POST" style="display:inline-block;margin-right:10px;">
                @csrf
                <button type="submit" style="background:#1976d2;color:#fff;padding:8px 22px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Accept</button>
            </form>
            <form action="{{ url('/customer/suspend/' . $customer->id) }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" style="background:#d32f2f;color:#fff;padding:8px 22px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Suspend</button>
            </form>
        </div> --}}
        
        <h2>Customer Details</h2>

        <div class="customerDetails">

            <div class="image">
                <img src="{{ asset('images/' . $customer->image) }}" alt="User Image" style="">
            </div>
            <div class="details">
                <p class="customer-id">cID: {{ $customer->id }}</p>
                <p class="store-name">{{ $customer->store_name}}</p>
                <span class="username"><p class="handle">Handled by</p><p>{{ $customer->name }}</p></span>
                {{-- <p><strong>Account Status:</strong> {{ $customer->acc_status}}</p> --}}
                <p style="font-size: 14px;">Joined on {{ $customer->created_at -> format('F Y')}}</p>
            </div>


        </div>


        <div class="receiptsCorner">
            <hr style="margin:32px 0;">
            {{-- <p class="receiptTitle">Receipts sent</p> --}}
            <div style="overflow-x:auto;">
            
            @if(isset($receipts) && count($receipts) > 0)
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
                                <td>{{ $receipt->created_at }}</td>
                                <td>{{ $receipt->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="no-receipts">No receipts found for this customer.</div>
                    @endif
            </div>

        </div>







    </div>
</body>
</html>

@endsection