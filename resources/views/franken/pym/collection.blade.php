@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/credits/receipt.css') }}">
@endpush



@section('content')

            @if ($errors->any())
                <div class="alert alert-danger" style="margin: 10px;">
                    <h6 style="margin-bottom: 10px; font-weight: bold;">Validation Errors:</h6>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 14px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success') || session('error'))
                <div 
                    id="flash-message"
                    class="flash-message 
                        {{ session('success') ? 'alert-success' : 'alert-danger' }}">
                    <strong>
                        {{ session('success') ? 'Success:' : ' Error:' }}
                    </strong>
                    {{ session('success') ?? session('error') }}
                </div>
            @endif
    

        <div class="content-bg" >
            
                <div class="content-header">
                    <div class="contents-display">
                        {{-- @if (Auth()->user()->role === "Customer")
                            <p>
                                <a href="{{ route('crd.list') }}">< Credits</a>
                            </p>
                        @elseif(Auth()->user()->role !== "Customer")
                            <p>
                                <a>< Go back to order</a>
                            </p>
                        @endif --}}

                        {{-- <button onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">View order</button>  --}}

                    </div>

                    <div class="title-actions">
                        <p class="heading" style="display: flex; flex-direction: row; justify-content: space-between; flex: 1;">
                            <span>Payments collection </span> 
                            <span style="font-size: 14px; font-weight: normal; color: #666;">Updated on <strong>{{ $purchase->created_at->format("F j, Y, g:i A")}}</strong></span>
                        </p>
                    </div>
                </div>

                <div class="content-body" style="padding: 10px; border: none; height: auto; flex-direction: row; display: flex; gap: 10px; flex: 1; overflow-x: auto; flex-wrap: wrap;" >

                    @foreach ( $payments as $receipt )
                        <div class="receipt-box" style="display: flex; flex-direction: column;    box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; width: 300px; min-height: 500px">
                         
                            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                <strong>#{{ $receipt->payment_id}}</strong>
                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center; margin-left: auto;">
                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                        <span class="material-symbols-outlined">
                                        expand_circle_down
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item">
                                                <span class="material-symbols-outlined">download</span>
                                                Download image
                                            </a>
                                        </li>
                                    </ul>
                                </div>   
                            </div>
                            <p style="margin: 0; display: flex; flex-direction: row; justify-content: space-between;">
                                <span>{{ $receipt->status }}</span>
                                <span class="paid">
                                    ₱{{ number_format($receipt->total_amount, 2) }}
                                </span>
                            </p>
                            
                            @php
                                $imgSrc = $receipt->image 
                                    ? ('data:' . $receipt->image_mime_type . ';base64,' . base64_encode($receipt->image))
                                    : asset('assets/default-company-logo.png');
                            @endphp
                            <img src="{{ $imgSrc }}" alt="Profile Image" height="70%">
                            
                            
                            <div class="more-info">

                                <p class="status-date">
                                    <span>Updated at</span>
                                    <strong>{{ \Carbon\Carbon::parse($receipt->updated_at)->format('F j, Y, g:i A') }}</strong>
                                </p>
                                <p class="status-date">
                                    <span>Created at</span>
                                    <strong>{{ \Carbon\Carbon::parse($receipt->created_at)->format('F j, Y, g:i A') }}</strong>
                                </p>
                            </div>
                        </div>

                    @endforeach
              
                </div>
        </div>

@endsection



@push('scripts')


</script>


@endpush
