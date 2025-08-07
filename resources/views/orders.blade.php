@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Orders</title>
</head>
<body>

    <div class="ordersFrame">
        <div class="titleFrame">
            <form method="GET" action="" class="date-search">
                <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name, Product ID & Status">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form> 
        </div>

        <div class="titleCount">
            <h2>Orders List</h2>
        </div>


        <div class="productList" style="padding: 15px;">
            @forelse($orders as $order)
                <a class="order-card" onclick="window.location='{{ route('order.view', $order->order_id) }}'" style="text-decoration: none; color: inherit;">
                    <p style="margin: 0; font-size: 15px;">{{ $order->created_at->format('F j, Y') }}</p>
                    <span>
                        <p class="store-name">{{ $order->user->store_name }}</p>
                         @php
                            $status = $order->status;

                            $statusClasses = [
                                'Pending' => 'status-pending',
                                'Processing' => 'status-processing',
                                'Cancelled' => 'status-cancelled',
                                'Rejected' => 'status-rejected',
                                'Done' => 'status-done',
                                'Completed' => 'status-completed',
                            ];
                        @endphp

                        <p class="{{ $statusClasses[ $order->status] ?? 'status-default' }}">
                            {{  $order->status }}
                        </p>


                        <p>x{{ $order->total_quantity }}</p>
                        <p style="color: green; font-size: 16px; font-weight: bold;">₱{{ number_format($order->total_amount, 2) }}</p>
                    </span>

                    

                </a>
            @empty
                <div class="noInput">No orders found.</div>
            @endforelse

        </div>



    </div>


</body>
</html>
@endsection
