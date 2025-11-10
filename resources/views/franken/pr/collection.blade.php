@extends('layouts.main')


@push('styles')
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
                        @if (Auth()->user()->role === "Customer")
                            <p>
                                <a href="{{ route('crd.list') }}">< Credits</a>
                            </p>
                        @elseif(Auth()->user()->role !== "Customer")
                            <p>
                                <a>< Go back to order</a>
                            </p>
                        @endif

                        {{-- <button onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">View order</button>  --}}

                    </div>

                    <div class="title-actions">
                        <p class="heading" style="display: flex; flex-direction: row; justify-content: space-between; flex: 1;">
                            <span>Payments collection</span> 
                            <span style="font-size: 14px; font-weight: normal; color: #666;">{{ $po->po_id }}</span>
                        </p>

                    </div>


                </div>

                <div class="content-body" style="padding: 10px; border: none; height: auto; flex-direction: row; display: flex; gap: 10px; flex: 1; overflow-x: auto; flex-wrap: wrap;" >

                    @foreach ( $receipts as $receipt )
                        <div class="receipt-box" style="display: flex; flex-direction: column;    box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; width: 300px; min-height: 500px">
                            
                            <p style="display: flex; flex-direction: row; justify-content: space-between;">
                                <span>{{ \Carbon\Carbon::parse($receipt->created_at)->format('F j, Y') }}</span>
                                <span>{{ $receipt->receipt_id}}</span>
                            </p>
                            <p style="margin: 0">{{ $receipt->status }}</p>
                            
                            @php
                                $imgSrc = $receipt->image 
                                    ? ('data:' . $receipt->image_mime_type . ';base64,' . base64_encode($receipt->image))
                                    : asset('assets/default-company-logo.png');
                            @endphp
                            <img src="{{ $imgSrc }}" alt="Profile Image" height="70%">
                            
                            
                            <div class="more-info">
                                <p class="paid">
                                    ₱{{ number_format($receipt->total_amount, 2) }}
                                </p>
                                <p class="status-date">
                                    <span>{{ $receipt->status }} on</span>
                                    <span>{{ \Carbon\Carbon::parse($receipt->updated_at)->format('F j, Y') }}</span>
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
