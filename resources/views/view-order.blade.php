
 @extends('layout')

@section('content')



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/view-order.css') }}">
    <title>View Order</title>
</head>
<body>

    <div class="viewFrame">
             <a href="{{ route('spec-orders', ['id' => $orders->first()->customer_id]) }}"><- My orders</a>
            <span>
                <h2 style="margin: 0">{{ $orders->first()->order_id }}</h2>       
                 @if (  ucfirst($orders->first()->status) === 'Pending')
                    <button class="cancel-order">Cancel order</button>
                @endif
                
            </span>

            <div class="titleCount"> 
                <span>
                    <h2 style="margin:0; font-size: 25px; color: #333;">Order details</h2>
                    <p style="margin: 0;">{{ $orders->first()->created_at->format('F j, Y g:i A') }}</p>
                </span>

                <span>
             
                    <p style=" margin-left: auto; font-weight: bold; color: orange;">{{ ucfirst($orders->first()->status) }}</p>
                </span>

            </div>

            <div class="order-block">

                

                <div class="orders">

                    @foreach($orders as $order)
                        <div class="product-item">
                            <p style="padding:10px 8px; width:50%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $order->product->name ?? 'Unknown Product' }}</p>
                            <p>x{{ $order->quantity }}</p>
                            <p>₱{{ number_format($order->unit_price, 2) }}</p>
                            <p><strong>₱{{ number_format($order->total_price, 2) }}</strong></p>
                        </div>
                    @endforeach
                    
                </div>

                <span>
                    <p style="font-size: 18px">Total</p>
                    <p style="font-weight: bold; color: green; font-size: 30px;">₱{{ number_format($total, 2) }}</p>
                </span>
            </div>

    </div>

</body>
</html>


@endsection