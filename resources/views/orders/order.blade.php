@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
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

    {{-- order action --}}
    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST"  enctype="multipart/form-data" action="{{ route('order.action') }}">

                @csrf

                
                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">File an action for this order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="order_id" value="{{$order->order_id}}" required>
                
                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>Committing any changes may be irreversible.</span>
                    </p>
                    
                    <div class="modal-option-groups">
                        <p>
                            <span class="req-asterisk">*</span>
                            Do you want to accept this order?
                        </p>
                        <select name="status" required>
                            <option value="">-- Select order status --</option>
                            <option value="Accepted">Yes, accept this order</option>
                            <option value="Rejected">No, reject this order</option>
                        </select>
                    </div>

                    <div class="modal-option-groups">
                        <p>Remarks (Optional)</p>
                        <input type="text" name="remarks" maxlength="200">
                    </div>
            
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit order status</button>
                </div>
            </form>
        </div>
    </div>
    {{-- process action --}}
    <div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('order.process') }}">
                @csrf

                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">Process order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="order_id" value="{{ $order->order_id }}" required>

                <div class="modal-body" style="height: 500px; overflow: auto;">
                    <p class="note-notify">
                        <span class="material-symbols-outlined">info</span>
                        <span>You can edit the heads/kilos just before the delivery.</span>
                    </p>
                    <div>
                        <p style="font-size: 13px; color: #333; margin: 0;">Summary of ordered items</p>
                        <table class="order-table"  cellpadding="8" cellspacing="0" style="margin-bottom: 15px; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; border-radius: 10px;">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Product Name</th>
                                    <th style="text-align: center">Price</th>
                                    <th style="text-align: center">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>₱{{ $item->productSetting->nego_price }}</td>
                                        <td>
                                            @if ($item->product->measurement_type === 'Kilos')
                                                {{ $item->placed_kilos }} kg
                                            @elseif ($item->product->measurement_type === 'Heads')
                                                {{ $item->placed_heads }} pcs
                                            @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                                {{ $item->placed_heads }} pcs | {{ $item->placed_kilos }} kg
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <p style="font-size: 13px; color: #333; margin: 0;">Items: distributed days and quantities</p>
                        <div style="padding: 5px; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; border:#888 1px solid; border-radius: 5px;">
                            @foreach($items as $item)
                                @php
                                    $rawDays = $order->supplier->delivery->delivery_days ?? '';
                                    
                                    // Check if it's already an array, otherwise try to explode by comma, then try JSON decode
                                    if (is_array($rawDays)) {
                                        $deliveryDays = $rawDays;
                                    } elseif (is_string($rawDays) && !empty($rawDays)) {
                                        // Try comma-separated first
                                        if (strpos($rawDays, ',') !== false) {
                                            $deliveryDays = array_map('trim', explode(',', $rawDays));
                                        } else {
                                            // Try JSON decode as fallback
                                            $deliveryDays = json_decode($rawDays, true) ?? [$rawDays];
                                        }
                                    } else {
                                        $deliveryDays = [];
                                    }
                                    
                                    $days = count($deliveryDays);

                                    if ($item->product->measurement_type === 'Kilos') {
                                        $total_kilos = $item->placed_kilos;
                                        $per_day_kilos = $days > 0 ? round($total_kilos / $days, 2) : $total_kilos;
                                    } elseif ($item->product->measurement_type === 'Heads') {
                                        $total_heads = $item->placed_heads;
                                        $per_day_heads = $days > 0 ? round($total_heads / $days, 2) : $total_heads;
                                    } elseif ($item->product->measurement_type === 'Heads&Kilos') {
                                        $total_heads = $item->placed_heads;
                                        $total_kilos = $item->placed_kilos;
                                        $per_day_heads = $days > 0 ? round($total_heads / $days, 2) : $total_heads;
                                        $per_day_kilos = $days > 0 ? round($total_kilos / $days, 2) : $total_kilos;
                                    }
                                @endphp

                                <style>
                                    tr td input{
                                        border-radius: 5px;
                                        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
                                        padding: 5px;
                                        min-width: 20px;
                                        width: 50px;
                                        text-align: center;
                                        border: none

                                    }
                                    .order-item-table{
                                        border-radius: 5px;
                                        box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
                                    }
                                    .order-item-table table head tr th{
                                        font-size: 13px;
                                    }
                                    .order-item-header span{
                                        color: #333;
                                        font-weight: bold;
                                    }
                                </style>
                        
                                <div class="order-item" 
                                    data-total-heads="{{ $total_heads ?? 0 }}"
                                    data-total-kilos="{{ $total_kilos ?? 0 }}"
                                    data-type="{{ $item->product->measurement_type }}"
                                    style="margin-top: 15px"
                                    >
                                    <p class="order-item-header" style="display: flex; flex-direction:row; justify-content: space-between; align-items: center; font-size: 13px; font-weight: normal; margin: 0; margin: 5px; ">
                                        <span>{{ $item->product->name }}</span>
                                        <span>
                                            @if ($item->product->measurement_type === 'Kilos')
                                                 {{ $total_kilos }} kg
                                            @elseif ($item->product->measurement_type === 'Heads')
                                                {{ $total_heads }} pcs
                                            @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                                 {{ $total_heads }} pcs | {{ $total_kilos }} kg
                                            @endif
                                        </span>
                                    </p>

                                    <div class="order-item-table">
                                        <table>
                                            <thead>

                                                    <tr>
                                                        <th style="text-align: center">Delivery Day</th>
                                                        @if ($item->product->measurement_type === 'Kilos')
                                                            <th style="text-align: center">Kilos</th>
                                                        @elseif ($item->product->measurement_type === 'Heads')
                                                            <th style="text-align: center">Heads</th>
                                                        @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                                            <th style="text-align: center">Heads</th>
                                                            <th style="text-align: center">Kilos</th>
                                                        @endif
                                                    </tr>
                                            </thead>
                                            <tbody>
                                                    @php
                                                        $deliveryDays = [];
                                                        if ($order->supplier && $order->supplier->delivery && $order->supplier->delivery->delivery_days) {
                                                            $rawDays = $order->supplier->delivery->delivery_days;
                                                            
                                                            if (is_array($rawDays)) {
                                                                $deliveryDays = $rawDays;
                                                            } elseif (is_string($rawDays) && !empty($rawDays)) {
                                                                // Try comma-separated first
                                                                if (strpos($rawDays, ',') !== false) {
                                                                    $deliveryDays = array_map('trim', explode(',', $rawDays));
                                                                } else {
                                                                    $deliveryDays = json_decode($rawDays, true) ?? [$rawDays];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    @foreach($deliveryDays as $day)
                                                        <tr>
                                                            <td>{{ trim($day) }}</td>

                                                            @if ($item->product->measurement_type === 'Kilos')
                                                                <td>
                                                                    <input type="number" 
                                                                        name="items[{{ $item->id }}][{{ trim($day) }}][kilos]"
                                                                        value="{{ $per_day_kilos }}"
                                                                        step="0.01" min="0">
                                                                </td>
                                                            @elseif ($item->product->measurement_type === 'Heads')
                                                                <td>
                                                                    <input type="number" 
                                                                        name="items[{{ $item->id }}][{{ trim($day) }}][heads]"
                                                                        value="{{ $per_day_heads }}"
                                                                        step="1" min="0">
                                                                </td>
                                                            @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                                                <td>
                                                                    <input type="number" 
                                                                        name="items[{{ $item->id }}][{{ trim($day) }}][heads]"
                                                                        value="{{ $per_day_heads }}"
                                                                        step="1" min="0" placeholder="pcs">
                                                                </td>
                                                                <td>
                                                                    <input type="number" 
                                                                        name="items[{{ $item->id }}][{{ trim($day) }}][kilos]"
                                                                        value="{{ $per_day_kilos }}"
                                                                        step="0.01" min="0" placeholder="kg">
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                </div>

                            @endforeach
                        </div>
                    </div>


                    <script>
                    document.querySelectorAll('.order-item').forEach(itemDiv => {
                        const type = itemDiv.dataset.type;
                        const totalHeads = parseFloat(itemDiv.dataset.totalHeads || 0);
                        const totalKilos = parseFloat(itemDiv.dataset.totalKilos || 0);

                        if (type.includes('Heads')) {
                            setupAutoAdjust(itemDiv, 'heads', totalHeads);
                        }
                        if (type.includes('Kilos')) {
                            setupAutoAdjust(itemDiv, 'kilos', totalKilos);
                        }
                    });

                    function setupAutoAdjust(itemDiv, key, total) {
                        const inputs = Array.from(itemDiv.querySelectorAll(`input[name*="[${key}]"]`));

                        //  Distribute initial values properly (handle decimals nicely)
                        distributeInitial(inputs, total);

                        inputs.forEach((input, index) => {
                            input.addEventListener('input', () => {
                                adjustRemaining(itemDiv, key, total, index);
                            });
                        });
                    }

                    function distributeInitial(inputs, total) {
                        const count = inputs.length;
                        let base = Math.floor(total / count);
                        let remainder = total % count;

                        inputs.forEach((input, i) => {
                            let value = base;
                            if (remainder > 0) {
                                value += 1;
                                remainder -= 1;
                            }
                            input.value = value;
                        });
                    }

                    function adjustRemaining(itemDiv, key, total, changedIndex) {
                        const inputs = Array.from(itemDiv.querySelectorAll(`input[name*="[${key}]"]`));
                        let sumExceptChanged = 0;

                        inputs.forEach((i, idx) => {
                            if (idx !== changedIndex) {
                                sumExceptChanged += parseFloat(i.value) || 0;
                            }
                        });

                        const remaining = total - sumExceptChanged;
                        const changedInput = inputs[changedIndex];
                        let changedValue = parseFloat(changedInput.value) || 0;

                        //  Prevent exceeding total
                        if (changedValue > remaining) {
                            changedValue = remaining;
                            changedInput.value = changedValue;
                        }

                        //  Recalculate other fields proportionally
                        const diff = total - (changedValue + sumExceptChanged);
                        if (diff !== 0) {
                            distributeDiff(inputs, changedIndex, diff);
                        }
                    }

                    function distributeDiff(inputs, changedIndex, diff) {
                        const otherInputs = inputs.filter((_, idx) => idx !== changedIndex);
                        let remainingDiff = diff;

                        // Adjust each input evenly to absorb or give back the difference
                        otherInputs.forEach((input, i) => {
                            if (remainingDiff === 0) return;

                            let value = parseFloat(input.value) || 0;
                            const adjustment = Math.sign(remainingDiff); // +1 or -1
                            input.value = value + adjustment;
                            remainingDiff -= adjustment;
                        });
                    }
                    </script>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Process this order</button>
                </div>
            </form>

        </div>
    </div>

   <div class="content-bg" style="display: flex; flex-direction: row;">
        <div class="left" style="width: 75%">
            <div class="content-header">
                <div class="contents-display">
                    <p>
                        <a href="{{ route('orders.list') }}">< Orders list</a>
                    </p>
                </div>
                <div class="title-actions">
                        <p class="heading" >
                            <span>Order #{{ $order->order_id }}</span>
                            <span class="order-status"> {{ $order->status }} </span>
                        </p>
                        <div class="upper-con" style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                            <div class="buttons">
                                <!-- Buttons -->
                                @if ($order->status === 'Accepted' && Auth()->user()->role !== 'Supplier')
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
                                @elseif ($order->status === 'Processed' && Auth()->user()->role !== 'Supplier')
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
                                        Receipt collection
                                    </button> 
                                @endif
                            </div>
                        </div>
                </div>
                <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" style="width: auto">
                            <div class="modal-header">
                                <p class="modal-title">Export</p>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="height: auto;">
                                <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                    data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                    >
                                    <span class="material-symbols-outlined"> print</span>
                                    Customer Order
                                </button>
                                <button type="button" 
                                        data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                        data-url="{{ route('orders.delivery.pdf', $order->order_id) }}"
                                        >
                                    <span class="material-symbols-outlined"> print</span>
                                    Delivery receipt                               
                                </button>
                                <button type="button" 
                                        data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                        data-url="{{ route('orders.invoice.pdf', $order->order_id) }}"
                                    >
                                    <span class="material-symbols-outlined"> print</span>
                                    Sales Invoice
                                </button>                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content" style="width: 100%">
                        <div class="modal-header">
                            <p class="modal-title">PDF Preview</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="height: 80vh;">
                            <iframe id="pdfFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
                        </div>
                        </div>
                    </div>
                </div>
                {{-- @if ($order->status === "Accepted")
                    <p style="padding: 5px; width: 300px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;">Waiting for scheduled delivery</p>
                @else
                <p>{{ $order->status}}</p>
                @endif --}}
                {{-- 
                @if (Auth()->user()->role !== 'Supplier' && $order->status === 'Pending')
                    <div>
                                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify account</button>
                    </div>
                @elseif (Auth()->user()->role === 'Supplier')
                    <div>
                        <button data-bs-toggle="modal"  class="btn-transition">Purchase order</button>
                    </div>
                @endif --}}
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
                        <strong> {{ $order->created_at->format('F j, Y') }}</strong>
                    </p>
                </div>
                <div class="details-box">
                    <div class="first" style="justify-content: space-between">
                        <div class="supplier">
                            @php
                                $imgSrc =  $order->supplier->user->image 
                                    ? ('data:' . $order->supplier->user->image_mime_type . ';base64,' . base64_encode($order->supplier->user->image))
                                    : asset('images/default-avatar.png');
                            @endphp
                            <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">
                            <div class="name-redirect">
                                <div>
                                    <p>{{ $order->supplier->company_name ?? 'N/A' }}</p>
                                    <button href="">
                                        <span class="material-symbols-outlined">
                                            arrow_outward
                                        </span>
                                    </button>
                                </div>
                                <p class="category">{{ $order->supplier->category }}</p>
                            </div>                        
                        </div>
                        <div class="un-named">
                            <div>
                                <p class="time">
                                    <span class="label">Receiving time</span>
                                    <span> {{ $order->requirements->receiving_time->format('h:i:s a') }}</span>
                                </p>
                                <p  class="date">
                                    <span class="label">Delivery days</span>
                                    <span>{{ $order->requirements->delivery_days }}</span>

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
                                <span>{{ ($order->requirements->delivery_address_1 ) ?? NULL}}</span>
                            </p>
                        </div>
                        <div class="location">
                            <span class="material-symbols-outlined icon">
                            location_on
                            </span>
                            <p>
                                <span class="label">Address line 2</span>
                                <span>{{ ($order->requirements->delivery_address_2 ) ?? NULL}}</span>
                            </p>
                        </div>
                        <div class="location">
                            <span class="material-symbols-outlined icon">
                            location_on
                            </span>
                            <p>
                                <span class="label">Address line 3</span>
                                <span>{{ ($order->requirements->delivery_address_3 ) ?? NULL}}</span>
                            </p>
                        </div>                    
                    </div>
                </div>
                {{-- <p style="display: flex; flex-direction: column;">
                        <span><strong>Supplier:</strong> {{ $order->supplier->company_name }}</span>
                        <span><strong>Order ID:</strong> {{ $order->order_id }}</span>
                        <span><strong>Total amount: </strong> ₱{{ number_format($order->total_amount, 2) }}</span>
                        <span><strong>Payment status: </strong> {{ $order->payment_status }}</span>
                        @if ($order->status === "Delivered")
                            <span><strong>Delivered at:</strong> {{ $order->delivered_at->format('F j, Y') }}</span>
                        @elseif($order->status === "Rejected")
                            <span><strong>Rejected at:</strong> {{ $order->rejected_at->format('F j, Y') }}</span>
                        @elseif($order->status === "Completed")
                            <span>
                                <strong>Completed at:</strong> 
                                {{ $order->completed_at?->format('F j, Y') ?? 'Not yet completed' }}
                            </span>
                        @elseif($order->status === "Accepted")
                            <span><strong>Acccepted at:</strong> {{ $order->created_at->format('F j, Y') }}</span>

                        @endif
                </p> --}}
            </div>

            {{-- <div>
            <p>Delivery frequency: <span>{{$order->supplier->delivery->delivery_frequency}}</span></p>
            </div> --}}

            <div class="content-body" style="padding: 10px; border: none; height: auto;">
                <div class="table-body" style="margin-top: 50px">
                                <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                    <p style="margin: 5px; font-weight: bold;">Scheduled deliveries</p>
                                </div>

                                    <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden; align-items: center;">
                                        @if ($activeDelivery > 0)

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
                                                    <tr onclick="window.location.href='{{ route('order.delivery_items', ['delivery_id' => $delivery->delivery_id]) }}'">
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
                                                            $varianceHeads = $delivery->deliveryItems->sum('variance_heads');
                                                            $varianceKilos = $delivery->deliveryItems->sum('variance_kilos');
                                                            $hasVariance = $varianceHeads != 0 || $varianceKilos != 0;
                                                        @endphp
                                                        <td>
                                                            @if ($hasReceived && $hasVariance)
                                                                @if ($varianceHeads != 0 && $varianceKilos != 0)
                                                                    <span style="color: {{ $varianceHeads == 0 ? 'green' : 'red' }};">
                                                                        {{ $varianceHeads > 0 ? '+' : '' }}{{ $varianceHeads }} heads
                                                                    </span>
                                                                    <br>
                                                                    <span style="color: {{ $varianceKilos == 0 ? 'green' : 'red' }};">
                                                                        {{ $varianceKilos > 0 ? '+' : '' }}{{ number_format($varianceKilos, 2) }} kg
                                                                    </span>
                                                                @elseif ($varianceHeads != 0)
                                                                    <span style="color: {{ $varianceHeads == 0 ? 'green' : 'red' }};">
                                                                        {{ $varianceHeads > 0 ? '+' : '' }}{{ $varianceHeads }} heads
                                                                    </span>
                                                                @elseif ($varianceKilos != 0)
                                                                    <span style="color: {{ $varianceKilos == 0 ? 'green' : 'red' }};">
                                                                        {{ $varianceKilos > 0 ? '+' : '' }}{{ number_format($varianceKilos, 2) }} kg
                                                                    </span>
                                                                @else
                                                                    <span style="color: green;">Exact</span>
                                                                @endif
                                                            @elseif($hasReceived && !$hasVariance)
                                                                <span style="color: green;">Exact</span>
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
                                            <p style="margin: 10px; color: #666;">This order has no confirmed delivery days yet.</p>
                                        @endif
                                    </div>


                </div>
                <div class="table-body" style="margin-top: 50px">
                    <p style="margin: 5px; font-weight: bold;">Order items</p>
                    <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Unit price</th>
                                    <th>Heads/Kilos</th>
                                    <th>Total amount</th>                                                
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($items as $item)
                                    <tr >
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>₱{{ number_format($item->productSetting?->nego_price ?? '--', 2) }}</td>
                                        <td>
                                            @if ($item->product->measurement_type === "Kilos")
                                                {{ $item->placed_kilos }}kg
                                            @elseif ($item->product->measurement_type === "Heads")
                                                {{ $item->placed_heads }}
                                            @endif
                                        </td>
                                        <td>₱{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
        </div>
        <div class="right" style="width: 25%; height: 100%;">
            <p>
                <span class="material-symbols-outlined">
                payments
                </span>
                <span>Balance</span>
            </p>
            <div class="right-bg">

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
                        <span style="color: #333; font-weight: bold;">₱{{ number_format($order->total_amount, 2) }}</span>
                    </p>
                </div>

                <div class="cons" style="height: 50px">
                    <p class="label-con" style="gap: 3px">
                        <span class="material-symbols-outlined icon" >
                        payment_arrow_down
                        </span>
                        <span>Payment received</span>
                    </p>
                    <p style="justify-content: flex-end">
                        <span style="color: #333; font-weight: bold; font-size: 12px">{{ $receivedPaymentCount }}</span>
                    </p>
                </div>
                <div class="payment-con" style="padding: 5px">
                    @foreach ( $payments as  $payment)
                        <button class="cons-receipt" style="margin: 0; padding: 10px;">
                            <p style="display: flex; gap: 0; margin: 0; padding: 0; background-color: transparent;">
                                <span class="loop" style="font-size: 12px; color: #666;">#{{ $loop->iteration }}</span>
                                <span style="margin-left: 7px">{{ $payment->created_at->format('j F, Y') }}</span>
                                <span style="color: green; margin-left: auto;">+ ₱{{ number_format($order->total_amount, 2) }}</span>
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
                </div>

                <hr>
                <div class="summary">
                    <p style="padding: 0; margin: 0;">
                        <span class="label">Paid</span>
                        <span class="value">₱{{ number_format($verifiedPaidAmount, 2) }}</span>
                    </p>
                    <p style="padding: 0">
                        @php
                            $remainingBalance = ($order->total_amount) - ($verifiedPaidAmount);
                        @endphp
                        <span class="label">Balance</span>
                        <span class="value">₱{{ number_format($remainingBalance, 2) }}</span>
                    </p>

                </div>
            </div>
        </div>

    </div>



@endsection



@push('scripts')
    <script src="{{ asset('js/global/password.js') }}"></script>
    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/pdf_view.js') }}"></script>
    <script src="{{ asset('js/order/modal-values.js') }}"></script>



@endpush