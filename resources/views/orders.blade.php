@extends('ordering')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Your Orders</title>
</head>
<body>
    
    <div class="orderFrame">

        <div class="titleFrame">

            <form method="GET" action="" class="date-search">
                <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by ID, Name, or User Type">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>    

            @if(auth()->user()->user_type === 'Admin')
            <button id="openModalBtn" class="addStaffBtn">Add Staff</button>
            @endif

        </div>

        <div class="titleCount"> 
            <h2 style="margin: 0">Your orders</h2> 
            <p style="margin: 0">These are the orders you've made</p>
        </div>    
        
        <div class="order-list">
            @forelse($orders as $order)
                <div class="order-summary">
                    {{-- <h3>Order #{{ $order->order_id }}</h3> --}}
                    <a class="order-meta" style="text-decoration: none; color: inherit;" href="{{ route('orders.view', $order->order_id) }}">

                        <span>
                            <p style="font-weight: bold; font-size: 15px;">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                            <p>x{{ $order->total_quantity }}</p>
                            {{-- <p>Product: {{ $order->product->name }}</p> --}}

                        </span>
                        <span>
                            <p>{{ ucfirst($order->status)}}</p>
                            <p style="font-weight: bold; color: green; font-size: 19px;">₱{{ number_format($order->total_amount, 2) }}</p>
                        </span>

                        {{-- <button class="view-btn">View order</button> --}}


                        {{-- @if ($order->status == 'pending')
                             <button class="cancel-btn">Cancel order</button>
                        @endif --}}
                        

                    </a>
                
                <!-- Button to view order details -->
                {{-- <button class="btn btn-sm btn-outline-primary" >
                    View Order Details
                </button> --}}



                <!-- Hidden details section (you can load via AJAX) -->
                <div id="details-{{ $order->order_id }}" style="display: none; margin-top: 15px;">
                    <p><em>Click to load individual item details...</em></p>
                </div>
                </div>
        @empty
            <div class="text-center" style="padding: 50px;">
                <h4>No orders found</h4>
                <p>You haven't placed any orders yet.</p>
            </div>
        @endforelse
    </div>

</body>
</html>




@endsection