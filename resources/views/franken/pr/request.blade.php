@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/franken/modal.css') }}">

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

    {{-- Staff confirmation modal --}}
    @if (auth()->user()->role !== 'Customer' && $request->status === 'Pending')
        <div class="modal fade" id="confirm-action" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" style="height: 500px">
                <form class="modal-content content_frank" style="width: 800px; overflow: hidden; height: 600px;" method="POST" action="{{ route('pr.confirm', $request->po_id) }}">
                    @csrf

                        <div class="modal-header  header_frank">
                            <div class="detail-fank">
                                <span class="material-symbols-outlined icon">
                                assignment
                                </span>
                                <p>
                                    <span class="title">Purchase order approval</span>
                                    <span class="desc">Confirming this PO will automaticallys et the scheduled deliveries</span>
                                </p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="body-frank">

                            <div class="table-con-frank" style="width: 70%">
                                <p>
                                    <span class="material-symbols-outlined icon">
                                        note_stack
                                    </span>
                                    <span>Note (Optional)</span>
                                </p>
                                <textarea name="notes"  maxlength="255" id="staff_notes" class="" rows="3" style=" border-radius: 5px; outline: none; padding: 5px;" placeholder="You can add notes regarding this purchase order for the customer..."></textarea>

                            </div>
                            <div class="table-con-frank">
                                <p>
                                    <span class="req-asterisk">*</span>
                                    <span>Status</span>
                                </p>
                                <select name="decision" id="select" required>
                                    <option value="" style="color: #888">--- Select status ---</option>
                                    <option value="Accept">Accept request</option>
                                    <option value="Reject">Reject request</option>
                                </select>

                            </div>
                            <div class="table-con-frank">
                                <p>
                                    <span class="material-symbols-outlined icon">
                                        shopping_basket
                                    </span>
                                    <span>Product list</span>
                                </p>
                                <table class="table-frank" style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                    <thead >
                                        <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                            <td>#</td>
                                            <td>Product ID</td>
                                            <td>Name</td>
                                            <td>Measurement</td>
                                            <td>Quantity</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orderPo->items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->product->product_id }}</td>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->product->measurement_type }}</td>
                                            
                                                <td>
                                                    @if ($item->product->measurement_type === "Heads")
                                                        {{ $item->planned_heads }}
                                                    
                                                    @elseif ($item->product->measurement_type === "Kilos")
                                                        {{ $item->planned_kilos }}kg
                                                    
                                                    @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                        {{ $item->planned_heads }} - {{ $item->planned_kilos }}kg
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                     
                                    </tbody>
                                </table>

                            </div>
                            <div class="table-con-frank" style="margin-top: 5px">
                                <p>
                                    <span class="material-symbols-outlined icon">
                                        local_shipping
                                    </span>
                                    <span>Delivery schedule</span>
                                </p>
                                <table class="table-frank" style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                    <thead>
                                        <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                            <td>#</td>
                                            <td>Delivery details</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($delivery_scheduled as $del)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <p style="display: flex; font-size:12px; font-weight: normal; gap: 5px flex-direction: row; align-items:center;">
                                                            <span class="material-symbols-outlined" style="font-size: 13px; font-weight: normal;">event</span>  
                                                            <span>{{ $del->delivery_date->format('F j') }}</span>
                                                        </p>
                                                        <div style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 10px; width: 100%; gap: 5px;">
                                                            @foreach ($del->scheduled_items as $item)
                                                                <div style="width: auto; min-width: auto; display: flex; align-items: center; flex-direction: row; background-color: #ffffff; padding: 5px; gap: 3px; border-radius: 5px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-top: 3px solid #ffde59;">

                                                                    <div style=" width: auto; ">
                                                                        
                                                                        <p style="font-size: 13px; font-weight: bold;">
                                                                            <span>{{ $item->product->name }}</span> 
                                                                        </p>
                                                                        <p style="font-size: 13px; font-weight: normal;">
                                                                            @if ($item->product->measurement_type === "Heads")
                                                                                {{ $item->planned_heads }} heads
                                                                            @elseif ($item->product->measurement_type === "Kilos")
                                                                                {{ $item->planned_kilos }}kg
                                                                            @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                                                {{ $item->planned_heads }}heads and {{ $item->planned_kilos }}kg
                                                                            @else
                                                                                --
                                                                            @endif
                                                                        </p>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </td>
                                            </tr>
                                        @endforeach
                     
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    
                    
                    <div class="modal-footer footer-frank">
                        <button type="submit" name="action"class="accept_order">
                            <span class="material-symbols-outlined icon">approval</span>
                            Confirm changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
   <div class="content-bg" style="display: flex; flex-direction: row; overflow: hidden;">
        <div class="left" style="width: 75%">
            <div class="content-header">
                <div class="contents-display">
                    <p>
                        <a href="{{ route('pr.list') }}">< Purchase list</a>
                    </p>
                </div>
                <div class="title-actions">
                    <p class="heading" >
                        <span>PO #{{ $request->po_id }}</span>
                        <span class="order-status"> {{ $request->status }} </span>
                    </p>
                    <div class="upper-con" style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                        <div class="buttons">
                            <!-- Buttons -->

                        <div style="display: flex; gap: 10px;">
                            @if (auth()->user()->role !== 'Customer' && $request->status === 'Pending')
                                <button data-bs-toggle="modal" data-bs-target="#confirm-action" class="btn-transition">
                                    <span class="material-symbols-outlined">
                                        approval_delegation
                                    </span>
                                    Confirm Order
                                </button>
                            @endif
                        </div>
                            {{-- @if ($request->status === 'Accepted' && Auth()->user()->role !== 'Customer')
                                <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#processModal" 
                                    data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                    class="process"
                                    >
                                    <span class="material-symbols-outlined" >
                                    component_exchange
                                    </span>
                                    Process order
                                </button>
                            @endif
                            @elseif ($request->status === 'Processed' && Auth()->user()->role !== 'Customer')
                                <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#exportModal" 
                                    data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                    class="export"
                                    >
                                    <span class="material-symbols-outlined"> download</span>
                                    Export
                                </button>
                                <button class="collection-btn" onclick="window.location.href='{{ route('orders.receipt', ['order_id' => $order->order_id]) }}'">
                                    <span class="material-symbols-outlined" >
                                    grain
                                    </span>
                                    Payments collection
                                </button> 
                            @endif   --}}
                        </div>
                    </div>
                </div>
                <div class="details">
                    <div class="">
                        <p>
                            <span class="material-symbols-outlined">
                            room_service
                            </span>                        
                            <span class="value">{{ $itemCount }} Product(s)</span>
                        </p>
                        -
                        <p>
                            <span class="material-symbols-outlined">
                            local_shipping
                            </span>                        
                            <span class="value">{{ $delCount }} Deliveries</span>
                        </p>
                    </div>
                    <p class="placed_on" style="margin: 0">
                        <span >Order placed on</span>
                        <strong> {{ $request->created_at->format('F j, Y') }}</strong>
                    </p>
                </div>
                <div class="details-box">
                    <div class="first" style="justify-content: space-between">
                        <div class="customer">
                            @php
                                $imgSrc =  $request->customer->user->image 
                                    ? ('data:' . $request->customer->user->image_mime_type . ';base64,' . base64_encode($request->customer->user->image))
                                    : asset('images/default-avatar.png');
                            @endphp
                            <img class="customer-image" src="{{ $imgSrc }}" alt="Profile Image">
                            <div class="name-redirect">
                                <div>
                                    <p>{{ $request->customer->company_name ?? 'N/A' }}</p>
                                    <button href="">
                                        <span class="material-symbols-outlined">
                                            arrow_outward
                                        </span>
                                    </button>
                                </div>
                                <p class="category">{{ $request->customer->category }}</p>
                            </div>                        
                        </div>
                        <div class="un-named">
                            <div>
                                <p class="time">
                                    <span class="label">Receiving time</span>
                                    <span> --</span>
                                </p>
                                <p  class="date">
                                    <span class="label">Delivery days</span>
                                    <span>
                                        @foreach ($deliveries as $delivery)
                                            {{$delivery->delivery_date ->format('F j')}},
                                        @endforeach
                                    </span>

                                </p>
                                <p  class="date">
                                    <span class="label">Purchase order</span>
                                    <a href="" style="color: #f8912a">#{{ $request->po_id }}</a>

                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="address">
                        <div class="location">
                            <span class="material-symbols-outlined icon">
                            location_on
                            </span>
                            <p>
                                <span class="label">Address line 1</span>
                                <span>{{ ($request->requirements->delivery_address_1 ) ?? NULL}}</span>
                            </p>
                        </div>
                        <div class="location">
                            <span class="material-symbols-outlined icon">
                            location_on
                            </span>
                            <p>
                                <span class="label">Address line 2</span>
                                <span>{{ ($request->requirements->delivery_address_2 ) ?? NULL}}</span>
                            </p>
                        </div>
                        @if ($request->requirements->delivery_address_3)
                            <div class="location">
                                <span class="material-symbols-outlined icon">
                                location_on
                                </span>
                                <p>
                                    <span class="label">Address line 3</span>
                                    <span>{{ ($request->requirements->delivery_address_3 ) ?? NULL}}</span>
                                </p>
                            </div>   
                        @endif
                 
                    </div>
                </div>
            </div>
            <div class="content-body" style="padding: 10px; border: none; height: auto; overflow-x: auto; height: 450px;  border-radius: 0;">
                <div class="table-body" >
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <p style="margin: 5px; font-weight: bold;">Scheduled deliveries</p>
                    </div>
                    <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden; align-items: center;">
                        @if ($request->status !== "Pending")
                            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                <thead style="background-color: #f8f8f8;">
                                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                        <th>#</th>
                                        <th>Delivery ID</th>
                                        <th>Scheduled Date</th>
                                        <th>Delivered Date</th>
                                        <th>Items</th>
                                        <th>Planned</th>
                                        <th>Received</th>
                                        <th>Variance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deliveries as $delivery)
                                        <tr onclick="window.location.href='{{ route('dlv.delivery', ['delivery_id' => $delivery->delivery_id]) }}'">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $delivery->delivery_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</td>
                                            <td>
                                                @if($delivery->delivered_at)
                                                    {{ \Carbon\Carbon::parse($delivery->delivered_at)->format('F j, Y') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $delivery->deliveryItems->count() }}</td>
                                            {{-- PLANNED --}}
                                            @php
                                                $plannedHeads = $delivery->deliveryItems->sum('planned_heads');
                                                $plannedKilos = $delivery->deliveryItems->sum('planned_kilos');
                                            @endphp
                                            <td>
                                                @if ($plannedHeads > 0 && $plannedKilos > 0)
                                                    {{ $plannedHeads }} heads<br>{{ $plannedKilos }} kg
                                                @elseif ($plannedHeads > 0)
                                                    {{ $plannedHeads }} heads
                                                @elseif ($plannedKilos > 0)
                                                    {{ $plannedKilos }} kg
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            {{-- RECEIVED --}}
                                            @php
                                                $receivedHeads = $delivery->deliveryItems->sum('received_heads');
                                                $receivedKilos = $delivery->deliveryItems->sum('received_kilos');
                                                $hasReceived = $receivedHeads > 0 || $receivedKilos > 0;
                                            @endphp
                                            <td>
                                                @if ($hasReceived)
                                                    @if ($receivedHeads > 0 && $receivedKilos > 0)
                                                        {{ $receivedHeads }} heads<br>{{ $receivedKilos }} kg
                                                    @elseif ($receivedHeads > 0)
                                                        {{ $receivedHeads }} heads
                                                    @elseif ($receivedKilos > 0)
                                                        {{ $receivedKilos }} kg
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            {{-- VARIANCE --}}
                                            @php
                                                $varianceHeads = $delivery->deliveryItems->sum(function ($item) {
                                                    return ($item->received_heads ?? 0) - ($item->planned_heads ?? 0);
                                                });

                                                $varianceKilos = $delivery->deliveryItems->sum(function ($item) {
                                                    return ($item->received_kilos ?? 0) - ($item->planned_kilos ?? 0);
                                                });

                                                $hasVariance = $varianceHeads != 0 || $varianceKilos != 0;
                                            @endphp

                                            <td>
                                                @if ($hasReceived)
                                                    @if ($hasVariance)
                                                        @if ($varianceHeads != 0)
                                                            <span style="color: {{ $varianceHeads > 0 ? 'red' : 'orange' }};">
                                                                {{ $varianceHeads > 0 ? '+' : '' }}{{ $varianceHeads }} heads
                                                            </span><br>
                                                        @endif

                                                        @if ($varianceKilos != 0)
                                                            <span style="color: {{ $varianceKilos > 0 ? 'red' : 'orange' }};">
                                                                {{ $varianceKilos > 0 ? '+' : '' }}{{ number_format($varianceKilos, 2) }} kg
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span style="color: green;">Exact</span>
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            {{-- STATUS --}}
                                            <td>
                                                @php
                                                    $isToday = \Carbon\Carbon::parse($delivery->delivery_date)->isToday();
                                                    $color = match($delivery->status) {
                                                        'Completed', 'Delivered' => 'green',
                                                        'Scheduled' => 'orange',
                                                        'In Transit' => 'blue',
                                                        'Cancelled' => 'gray',
                                                        default => 'black',
                                                    };
                                                @endphp
                                                <span style="color: {{ $color }};">
                                                    @if($isToday && $delivery->status === 'Scheduled')
                                                        <strong style="color: green;">Delivery Today</strong>
                                                    @else
                                                        {{ $delivery->status }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="margin: 10px; color: #666;">This order has to be confirmed first.</p>
                        @endif
                    </div>
                </div>
                <div class="table-body" style="margin-top: 50px">
                    <p style="margin: 5px; font-weight: bold;">Purchased items</p>
                    <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Unit price</th>
                                    <th>Heads/Kilos</th>
                                    <th>Received</th>
                                </tr>
                            </thead>
                            <tbody>                                
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>₱{{ number_format($item->productSetting?->nego_price ?? 0, 2) }}</td>
                                        <td>
                                            @if ($item->product->measurement_type === "Kilos")
                                                {{ $item->planned_kilos ?? $item->total_planned_kilos }}kg
                                            @elseif ($item->product->measurement_type === "Heads")
                                                {{ $item->planned_heads ?? $item->total_planned_heads }}
                                            @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                {{ $item->planned_heads ?? $item->total_planned_heads }} heads, 
                                                {{ $item->planned_kilos ?? $item->total_planned_kilos }}kg
                                            @endif                                        
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="right" style="width: 25%; height: 100%; background-color: #fff">
            <p style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <span class="material-symbols-outlined">
                payments
                </span>
                <span>Balance</span>
            </p>
            <div class="right-bg" >
                <p style="background: transparent"> 
                    <span class="label">Status</span>
                    <span>{{ $paymentStatus }}</span>
                        {{-- <p>Paid Amount: ₱{{ number_format($verifiedPaidAmount, 2) }} / ₱{{ number_format($order->total_amount, 2) }}</p> --}}
                </p>
                <div class="cons">
                    <p class="label-con" style="gap: 3px">
                        <span class="material-symbols-outlined icon" >
                        request_page
                        </span>
                        <span>Bill</span>
                    </p>
                    <p style="justify-content: flex-end">
                        <span style="color: #333; font-weight: bold;">₱{{ number_format($poBalance, 2) }}</span>
                    </p>
                </div>
                <div class="payment-con" style="box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px, rgb(209, 213, 219) 0px 0px 0px 1px inset; border-radius: 5px; background-color: #f2f2f26f;" >
                    <div class="cons" style="height: auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; background-color:#ffb74d;">
                        <p class="label-con" style="gap: 3px; background-color: transparent;">
                            <span class="material-symbols-outlined icon" style="color: #333; font-weight: bold">
                            payment_arrow_down
                            </span>
                            <span style="color: #333; font-weight: bold;">Payment received</span>
                        </p>
                        <p style="justify-content: flex-end;background-color: transparent;">
                            <span style="color: #333; font-weight: bold; font-size: 12px">{{ $receivedPaymentCount }}</span>
                        </p>
                    </div>
                    <div style="padding: 10px; ">
                        @if ($receivedPaymentCount !== 0)
                            @foreach ( $payments as  $payment)
                                <button class="cons-receipt" style="margin: 0; padding: 10px;" onclick="window.location.href='{{ route('receipts.receipt', ['receipt_id' => $payment->receipt_id]) }}'">
                                    <p style="display: flex; gap: 0; margin: 0; padding: 0; background-color: transparent;">
                                        <span class="loop" style="font-size: 12px; color: #666;">#{{ $loop->iteration }}</span>
                                        <span style="margin-left: 7px">{{ $payment->created_at->format('j F, Y') }}</span>
                                        <span style="color: #f8912a; margin-left: auto;">+ ₱{{ number_format($payment->total_amount, 2) }}</span>
                                    </p>
                                    <p class="receipt-label" style="margin: 0; height: auto; padding: 0; ; background-color: transparent;">
                                        <span>Bank transfer</span>
                                    </p>
                                </button>
                                    <hr style="margin: 10px;   
                                        border-top: 1px dashed #666;
                                        border-bottom: none;
                                        border-left: none;
                                        border-right: none;">
                            @endforeach
                        @else
                            <p style="font-size:12px; color:#888; font-weight:normal; text-align:center;">
                                Please ensure your payments are settled before the deadline to avoid fines.
                            </p>


                        @endif

                    </div>
                </div>
                <hr>
                <div class="summary">
                    <p style="padding: 0; margin: 0;">
                        <span class="label">Paid</span>
                        <span class="value">₱{{ number_format($poPaid, 2) }}</span>
                    </p>
                    <p style="padding: 0">
                      
                        <span class="label">Balance</span>
                        <span class="value" style="font-size: 15px; color: #f8912a;">₱{{ number_format($remainingBalance, 2) }}</span>
                    </p>

                </div>
                <button class="collection-btn" style="width: 50%; border-radius: 5px;box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;" onclick="window.location.href='{{ route('pym.collection', ['po_id' => $request->po_id]) }}'" >
                    <span class="material-symbols-outlined" style="font-size: 14px">grain</span>
                    Payment collection
                </button>

            </div>
        </div>
    </div>



@endsection



@push('scripts')


@endpush