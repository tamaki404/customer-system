@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/order_view.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Order View</title>
</head>
<body>
     
    <div class="ordersFrame">
        <a href="{{ route('orders') }}"><- Orders list</a>
        <span class="customer-details">
            <p style="font-size: 19px; font-weight: bold;">Order#: {{ $orderItems->first()->order_id }}</p>
         @php
            $status = $orderItems->first()->status;

            $statusClasses = [
                'Pending' => 'status-pending',
                'Processing' => 'status-processing',
                'Cancelled' => 'status-cancelled',
                'Rejected' => 'status-rejected',
                'Done' => 'status-done',
                'Completed' => 'status-completed',
            ];
        @endphp

        <p class="{{ $statusClasses[$status] ?? 'status-default' }}">
            {{ $status }}
        </p>

        </span>
        <p class="order-date">{{ $orderItems->first()->created_at->format('F j, Y') }} at {{ $orderItems->first()->created_at->format('g:i a ') }}</p>
        <div class="customer">
            @php
                $user = $orderItems->first()->user;
                $isBase64 = !empty($user->image_mime);
                $imgSrc = $isBase64 ? ('data:' . $user->image_mime . ';base64,' . $user->image) : asset('images/' . $user->image);
            @endphp
            <img  class="store-image" src="{{ $imgSrc }}" alt="">
            <p class="customer-store">{{ $orderItems->first()->user->store_name }}</p>

            <button onclick="window.location='{{ route('customer.view', $orderItems->first()->user->id) }}'">Visit Store</button>
        </div>
        <div class="orders-container">
            <div class="order-list">
                 @foreach($orderItems as $item)
                 
                  <div class="order-item">
                        <p style="width:70%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; font-weight: bold;">{{ $item->product->name }}</p>
                        <p>x{{ $item->quantity }}</p>
                        <p>₱ {{ $item->total_price }}</p>
                  </div>

                @endforeach
            </div>
                @if ($orderItems->first()->status != 'Pending')
                   <span class="status-action">
                    <p style="font-weight: bold">{{$orderItems->first()->status}}</p>
                    <p>by</p>                           
                    <p style="font-weight: bold">{{$orderItems->first()->action_by}}</p>
                    <p>-</p>
                    <p>{{ $orderItems->first()->action_at}}</p>
                </span>             
                @endif
            <div class="order-summary">


                <span>
                    @if ($orderItems->first()->status === 'Processing')

                        <form class="status-action" action="{{ url('/order/mark-done/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="markAsDone" style="width: 150px;">Mark as done</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">
                        </form>
                        <form class="status-action"  action="{{ url('/order/reject/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="reject" style="width: 100px;">Cancel</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                        </form>                    

                    @elseif ($orderItems->first()->status === 'Completed')
                        

                    @elseif ($orderItems->first()->status === 'Cancelled')
                    
                    @elseif ($orderItems->first()->status === 'Pending')
                        <form class="status-action"   action="{{ url('/order/accept/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="process" style="width: 100px;">Process</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                       </form> 
                        <form class="status-action"  action="{{ url('/order/reject/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="reject" style="width: 100px;">Cancel</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                        </form>                  
                    

                    @endif



                </span>


                <p><strong>{{ $orderItems->sum('quantity') }} </strong> item(s)</p>
                <p class="price"> ₱ {{ $orderItems->sum('total_price') }}</p>

            </div>

        </div>
        

    </div>




    
    

</body>
</html>




@endsection