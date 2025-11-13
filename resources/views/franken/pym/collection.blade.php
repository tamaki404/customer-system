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
                        <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; flex: 1; ">
                            <span>Payments collection #{{ $purchase->po_id }}</span> 
                            <button class="collection-btn" onclick="window.location.href='{{ route('pr.request', ['po_id' => $purchase->po_id]) }}'">
                                <span class="material-symbols-outlined" style="font-size: 14px">call_made</span>
                                <span style="font-size: 13px">Purchase order</span>
                            </button>
                            <style>
                                .collection-btn {
                                    background-color: #f8912a;w
                                    font-size: 13px;
                                    color: #fff;
                                    border: none;
                                    padding: 8px;
                                    gap: 5px;
                                    display: flex;
                                    border-radius: 5px;
                                    align-items: center;
                                    box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
                                }
                                .collection-btn:hover {
                                    background-color: #df8124;
                                }

                            </style>
                        </div>

                    </div>
                </div>

                <div class="content-body" style="padding: 10px; border: none; height: auto; flex-direction: row; display: flex; gap: 10px; flex: 1; overflow-x: auto; flex-wrap: wrap;" >

                    @foreach ($payments as $deliveryId => $receipts)
                        <div class="delivery-group" style="margin-bottom: 20px;  box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; padding: 10px; border-radius: 5px; height: auto;">
                            <h4 style="margin-bottom: 10px; font-size: 13px">Del #{{ $deliveryId }}</h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                @foreach ($receipts as $receipt)
                                    <div class="receipt-box" style="display: flex; flex-direction: column; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; width: 300px; min-height: 500px;">
                                        <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                            <strong>#{{ $receipt->payment_id }}</strong>
                                            <div class="dropdown" style="display:flex; align-items: center; justify-content: center; margin-left: auto;">
                                                <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;">
                                                    <span class="material-symbols-outlined">expand_circle_down</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" onclick="window.location.href='{{ route('crd.download', ['payment_id' => $receipt->payment_id]) }}'">
                                                            <span class="material-symbols-outlined">download</span>
                                                            Download image
                                                        </button>
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
                                                ? 'data:' . $receipt->image_mime_type . ';base64,' . base64_encode($receipt->image)
                                                : asset('assets/default-company-logo.png');
                                        @endphp
                                        <img src="{{ $imgSrc }}" alt="Receipt Image" height="70%">

                                        <div class="more-info">
                                            <p class="status-date">
                                                <span>Updated on</span>
                                                <strong>{{ \Carbon\Carbon::parse($receipt->updated_at)->format('F j, Y, g:i A') }}</strong>
                                            </p>
                                            <p class="status-date">
                                                <span>Created on</span>
                                                <strong>{{ \Carbon\Carbon::parse($receipt->created_at)->format('F j, Y, g:i A') }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

              
                </div>
        </div>

@endsection



@push('scripts')


</script>


@endpush
