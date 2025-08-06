@extends('ordering')

@section('content')
<div class="container">
    <h2>Your Orders</h2>
<h2>Order #{{ $orders->first()->order_id ?? 'Not found' }}</h2>

@foreach($orders as $order)
    <div>
        <p>Product: {{ $order->product->name ?? 'Unknown Product' }}</p>
        <p>Quantity: {{ $order->order_id }}</p>
        <p>Unit Price: ₱{{ number_format($order->unit_price, 2) }}</p>
        <p>Total Price: ₱{{ number_format($order->total_price, 2) }}</p>
    </div>
    <hr>
@endforeach


        
        {{-- <table class="table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spec_orders as $spec_order)
                        <tr>
                            <td>{{ $spec_order->order_id }}</td>
                            <td>{{ $spec_order->product->name }}</td>
                            <td>{{ $spec_order->quantity }}</td>
                            <td>₱{{ number_format($spec_order->unit_price, 2) }}</td>
                        </tr>
                @endforeach
            </tbody>
        </table> --}}
       

</div>


@endsection