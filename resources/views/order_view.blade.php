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
    <link rel="stylesheet" href="{{ asset('css/confirmation-modal/receipts_view.css') }}">

    <title>Order View</title>
</head>
<body>
         <!-- confirmation modal -->
    <div class="modal fade" id="confirmModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px;">
                    Are you sure you want to commit changes?
                </div>

                <div class="modal-footer" style="padding: 5px">
                    <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ordersFrame">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('orders') }}"><- Orders list</a>
        <span class="customer-details">
            <p class="order-num">Order#: {{ $orderItems->first()->order_id }}</p>
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

            <p id="status" style="font-size: 14px" class="{{ $statusClasses[$status] ?? 'status-default' }}">
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
                        <p style="color: green"> ₱{{ $item->total_price }}</p>
                  </div>

                @endforeach
            </div>
                @if ($orderItems->first()->status != 'Pending')
                   <span class="status-action">
                        <p style="font-weight: bold">{{$orderItems->first()->status}}</p>
                        <p>by</p>                           
                        <p style="font-weight: bold">{{$orderItems->first()->action_by}}</p>
                        <p>-</p>
                        <p></p>{{ \Carbon\Carbon::parse($orderItems->first()->action_at)->format('M d, Y g:i A') }}</p>
                   </span>             
                @endif
            <div class="order-summary">
                <div class="quan-price">
                    <p><strong>{{ $orderItems->sum('quantity') }} </strong> item(s)</p>
                    <p class="price"> ₱ {{ $orderItems->sum('total_price') }}</p>
                </div>

                <span>
                    @if ($orderItems->first()->status === 'Processing')

                        <form class="status-action" action="{{ url('/order/mark-done/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="button" class="btn-confirm markAsDone" data-action="Mark as done" style="width: 150px;">Mark as done</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">
                        </form>
                        <form class="status-action"  action="{{ url('/order/reject/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="button" class="btn-confirm reject" data-action="Cancel" style="width: 100px;">Cancel</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                        </form>                    

                    @elseif ($orderItems->first()->status === 'Completed')
                        

                    @elseif ($orderItems->first()->status === 'Cancelled')
                    
                    @elseif ($orderItems->first()->status === 'Pending')
                        <form class="status-action"   action="{{ url('/order/accept/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="button" class="btn-confirm process" data-action="Process" style="width: 100px;">Process</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                       </form> 
                        <form class="status-action"  action="{{ url('/order/reject/' .$orderItems->first()->order_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="button" class="btn-confirm reject" data-action="Cancel" style="width: 100px;">Cancel</button>
                            <input type="hidden" name="action_by" value="{{ auth()->user()->name }}">

                        </form>                  
                    

                    @endif



                </span>




            </div>

        </div>
    </div>



<script src="{{ asset('js/confirmation-modal/order_view.js') }}"></script>

    
    

</body>
</html>




@endsection