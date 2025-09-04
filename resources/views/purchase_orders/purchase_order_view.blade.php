@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/purchase_order_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="{{ asset('css/confirmation-modal/receipts_view.css') }}">

    <title>Create an Order</title>
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

    <div class="purchase-order-bg">
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

        <a class="go-back-a" href="/purchase_order"><- Purchase Order</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: auto;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>

        <span style="flex-direction: row; display: flex; flex-wrap: wrap; justify-content: space-between;">
            <h2 style="font-size: 25px; font-weight: bold; color: #333;">Order details</h2> 

        </span>
 
        <form class="order-actions" action="{{ route('change.po_status') }}" method="POST">
            @csrf
            @if(auth()->user()->user_type !== 'Customer')

                @if($po->status === "Pending")
                    <button type="button" class="btn btn-success btn-confirm" data-action="Accepted">Accept</button>
                    <button type="button" class="btn btn-warning btn-confirm" data-action="Rejected">Reject</button>
                    <button type="button" class="btn btn-danger btn-confirm" data-action="Cancelled">Cancel</button>

                @elseif($po->status === "Accepted")
                    <button type="button" class="btn btn-primary btn-confirm" data-action="Delivered">Mark as Delivered</button>

                @else
                @endif
                <input type="hidden" name="status" value="">
                <input type="hidden" name="po_id" value="{{ $po->id }}">
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            @endif
        </form>

        <form class="order-actions" action="{{ route('customer.po_status') }}" method="POST">
            @csrf

            @if(auth()->user()->user_type === 'Customer')
                @if($po->status === 'Pending')
                <input type="hidden" name="status" value="Cancelled">
                <button type="button" class="btn btn-danger btn-confirm" data-action="Cancelled">Cancel</button>
                @endif
            @endif
            
            <input type="hidden" name="status" value="">
            <input type="hidden" name="po_id" value="{{ $po->id }}">
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <input type="hidden" name="user_type" value="{{ auth()->user()->user_type }}">
        </form>
        <div class="order-div">
            <div class="order-header">
                <div class="order-id">
                    <p style="font-size: 14px; color: #888;">Order ID</p>
                    <p style="font-size: 16px">{{$po->order->order_id}}</p>
                </div>

                {{-- <span class="status-text">{{$po->status}}</span> --}}
            </div>

            <div class="shipment-div">
                <div class="ship-details">
                    <p style="margin: 0; font-size: 14px;">Shipment</p>
                    <div class="shipper-details" style="margin-top: 5px">
                        <img src="{{ asset('assets/sunnyScarmbles_logoEmail.png') }}" alt="Owner Image" width="100" class="ownerImage">
                        <div class="shipper-name-address">
                            <p class="company-name">Sunny & Scramble</p>
                            <p class="ship-detail">123 Sunny Street Sunnyville, 1860</p>
                        </div>
                    </div>

                </div>
                <div class="recipient-div">
                    <div class="shipper-details">
                        <p class="recipient-title">Recipient</p>
                        <div class="shipper-name-address">
                            <p class="company-name">{{$po->receiver_name}}</p>
                            <p class="ship-detail">{{$po->receiver_mobile}}</p>

                        </div>
                    </div>
                    <div class="shipper-details">
                        <p class="recipient-title">Delivery address</p>
                        <div class="shipper-name-address">
                            <p class="company-name">{{$po->user->store_name}}</p>
                            <p class="ship-detail" style="white-space:normal; word-wrap:break-word;">
                                {{ $po->street }},
                                {{ $po->barangay['barangay_name'] }},
                                {{ $po->municipality['municipality_name'] }},
                                {{ $po->province['province_name'] }},
                                {{ $po->region['region_name'] }}
                            </p>

                        </div>
                    </div>
                </div>      

            </div>
            <div class="shipment-div">

                <div class="recipient-div">
                    <div class="shipper-details">
                        <p class="recipient-title">Bill & Address</p>
                        <div class="shipper-name-address">
                            <p class="company-name"> ₱ {{$po->grand_total}}</p>
                            <p class="ship-detail" style="white-space:normal; word-wrap:break-word;">
                                {{ $po->billing_address }},
                                
                            </p>

                        </div>
                    </div>
                    <div class="shipper-details">
                        <p class="recipient-title">Status</p>
                        <div class="shipper-name-address">
                            <p class="company-name">{{$po->status}}</p>
                            @if ($po->status == 'Pending')
                                <p class="ship-detail" style="font-size: 13px">
                                    {{ $po->created_at->format('d F Y h:i A') }}
                                </p>

                            @elseif ($po->status == 'Approved' && $po->approved_at)
                                <p class="ship-detail" style="font-size: 13px">
                                    {{ $po->approved_at->format('d F Y h:i A') }}
                                </p>

                            @elseif ($po->status == 'Rejected' && $po->rejected_at)
                                <p class="ship-detail" style="font-size: 13px">
                                    {{ $po->rejected_at->format('d F Y h:i A') }}
                                </p>

                            @elseif ($po->status == 'Delivered' && $po->delivered_at)
                                <p class="ship-detail" style="font-size: 13px">
                                    {{ $po->delivered_at->format('d F Y h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="shipper-details">
                        <p class="recipient-title">Order placed</p>
                        <div class="shipper-name-address">
                            <p class="company-name" style="font-weight: normal">{{ $po->created_at->format('d F Y h:i A') }}</p>
                            <p class="ship-detail">{{$po->user->name}}</p>
                        </div>
                    </div>
                    <div class="shipper-details">
                        <p class="recipient-title">Purchase Order</p>
                        <div class="shipper-name-address">
                            <p class="company-name">{{$po->po_number}}</p>
                            <button style="margin-top: 5px" class="viewPO-btn" onclick="window.location='{{ route('purchase_order.create', $po->po_number) }}'">
                                <span class="material-symbols-outlined">description</span>
                                Purchase order
                            </button>
                        </div>
                    
                    </div>

                </div>      

            </div>
            <div class="shipment-div" style="width: 100%">
                <div class="ship-details">
                    <p class="timeline-title">Timeline</p>

                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="dot"></span>
                            <div class="content">
                                <p class="label">Order placed</p>
                                <p class="date">{{ $po->created_at->format('d F Y h:i A') }}</p>
                            </div>
                        </div>

                        @if ($po->approved_at)
                        <div class="timeline-item">
                            <span class="dot"></span>
                            <div class="content">
                                <p class="label">Approved</p>
                                <p class="date">{{ $po->approved_at->format('d F Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if ($po->rejected_at)
                        <div class="timeline-item">
                            <span class="dot"></span>
                            <div class="content">
                                <p class="label" style="color: red">Rejected</p>
                                <p class="date">{{ $po->rejected_at->format('d F Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if ($po->status === 'Cancelled' && $po->cancelled_at)
                            <div class="timeline-item">
                                <span class="dot"></span>
                                <div class="content">
                                    @if($po->cancelled_user_type === 'Customer')
                                        <p class="label" style="color: red">You cancelled this transaction</p>
                                    @else
                                        <p class="label" style="color: red">Cancelled</p>
                                    @endif
                                    <p class="date">{{ \Carbon\Carbon::parse($po->cancelled_at)->format('d F Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif

            

                        @if ($po->delivered_at)
                        <div class="timeline-item">
                            <span class="dot"></span>
                            <div class="content">
                                <p class="label" style="color: #3730a3">Delivered</p>
                                <p class="date">{{ $po->delivered_at->format('d F Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>


            </div>
            <div class="items-div">
                <div style="margin: 0; font-size: 15px; gap: 10px; margin-top: 10px; color: #333; display: flex; flex-direction: row;">
                    Products ordered 
                    <p class="order-count">({{$orderCount}})</p>
                </div>
                <div class="items-list">
                    @foreach($ordersItem as $item)
                        <div class="each-item-div">
                            @php
                                $isBase64 = !empty($item->product->image_mime);
                                $imgSrc = $isBase64 
                                    ? ('data:' . $item->product->image_mime . ';base64,' . $item->product->image) 
                                    : asset('images/' . ($item->product->image ?? 'default-product.png'));
                            @endphp

                            @if(!empty($item->product->image) && !empty($item->product->image_mime))
                                <img src="data:{{ $item->product->image_mime }};base64,{{ $item->product->image }}" 
                                    alt="{{ $item->product->name }}" 
                                    style="width: 50px; height: 50px; object-fit: cover; value">
                            @else
                                <div class="thumb-placeholder" 
                                    style="width: 50px; height: 50px; color:#888; font-size:13px; text-align: center;  display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 5px;">
                                    No Image
                                </div>
                            @endif
                            <div class="item-detail">
                                <p class="item-name">{{$item->product->name}}</p>
                                <p class="item-price-quan">
                                    <span class="item-original-price">₱{{ number_format($item->product->price, 2) }}</span>
                                    <span class="item-quantity"> × {{ $item->quantity }}</span>
                                </p>
                                <span class="item-total-price"> ₱ {{ number_format($item->product->price * $item->quantity, 2) }}</span>


                                <p class="item-unit-title-value">
                                    <span class="item-unit-title">Unit: </span>
                                    <span class="item-unit-value">{{$item->product->unit}}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach     
                </div>
        

            </div>

            <div class="items-div" style="width: 100%">
                <div style="margin: 0; font-size: 15px; gap: 10px; margin-top: 10px; color: #333; display: flex; flex-direction: row;">
                    Order summary 
                </div>
                <div class="order-summmary-div">
                    <div class="summary-items">
                        <p class="total-title">Sub total</p>
                        <p class="total-total"> ₱ {{$po->subtotal}}</p>
                    </div>
                    <div class="summary-items">
                        <p class="total-title">Tax</p>
                        <p class="total-total"> ₱ {{$po->tax_amount}}</p>
                    </div>
                    <div class="summary-items">
                        <p class="total-title">Discount</p>
                        <p class="total-total"> ₱ {{$po->subtotal}}</p>
                    </div>
                    <div class="summary-items" style="margin-top: 10px; background-color: #ffde59; padding: 10px; border-radius: 10px;">
                        <p class="total-title" style="color:#333; font-size:17px; font-weight: bold;">Total</p>
                        <p class="total-total" style="color:#333; font-size:17px; font-weight: bold;"> ₱ {{$po->grand_total}}</p>
                    </div>
                </div>

                @if ($po->status==="Delivered")
                    <button  class="invoice-btn" onclick="window.location='{{ route('invoice.view', $po->po_number) }}'">
                        <span class="material-symbols-outlined">description</span>
                        Invoice
                    </button>           
                @endif
        

            </div>


        </div>

    </div>

<script src="{{ asset('js/confirmation-modal/purchase_order_view.js') }}"></script>

</body>
</html>

@endsection