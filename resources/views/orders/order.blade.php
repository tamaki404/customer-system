@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
@endpush



@section('content')


@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


    {{-- order action --}}
    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST"  enctype="multipart/form-data" action="{{ route('order.action') }}">

                @csrf
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
                
                @if (session('success'))
                    <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin: 10px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                        <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                    </div>
                @endif
                
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

                {{-- Validation & Flash Messages --}}
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

                @if (session('success'))
                    <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin: 10px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                        <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                    </div>
                @endif

                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">Process order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="order_id" value="{{ $order->order_id }}" required>

                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined">info</span>
                        <span>You can edit the heads/kilos just before the delivery.</span>
                    </p>

                    <table class="order-table" border="1" cellpadding="8" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>P{{ $item->productSetting->nego_price }}</td>
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

                    @foreach($items as $item)
                        @php
                            $days = count($order->supplier->delivery->delivery_days);

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
                            .order-item {
                                border: 1px solid #ddd;
                                border-radius: 6px;
                                margin-bottom: 20px;
                                background: #fff;
                                font-size: 14px;
                                overflow: hidden;
                            }

                            .order-item-header {
                                background: #f5f5f5;
                                padding: 10px 15px;
                                font-weight: 600;
                                border-bottom: 1px solid #ddd;
                            }

                            .order-item-header span {
                                display: inline-block;
                                margin-right: 10px;
                            }

                            .order-item-total {
                                padding: 10px 15px;
                                background: #fafafa;
                                border-bottom: 1px solid #ddd;
                                font-weight: 500;
                            }

                            .order-item-table {
                                width: 100%;
                                border-collapse: collapse;
                            }

                            .order-item-table th,
                            .order-item-table td {
                                border: 1px solid #e2e2e2;
                                padding: 8px 10px;
                                text-align: center;
                                font-size: 14px;
                            }

                            .order-item-table th {
                                background: #f0f0f0;
                                font-weight: 600;
                            }

                            .order-item-table input[type="number"] {
                                width: 90px;
                                padding: 5px;
                                font-size: 14px;
                                border: 1px solid #bbb;
                                border-radius: 4px;
                                text-align: right;
                            }

                            .order-item-table input[type="number"]:focus {
                                border-color: #007bff;
                                outline: none;
                                box-shadow: 0 0 4px rgba(0, 123, 255, 0.25);
                            }
                        </style>

                        <div class="order-item" 
                            data-total-heads="{{ $total_heads ?? 0 }}"
                            data-total-kilos="{{ $total_kilos ?? 0 }}"
                            data-type="{{ $item->product->measurement_type }}">

                            <div class="order-item-header">
                                <span>{{ $item->product->name }}</span>
                            </div>

                            <div class="order-item-total">
                                @if ($item->product->measurement_type === 'Kilos')
                                    Total: {{ $total_kilos }} kg
                                @elseif ($item->product->measurement_type === 'Heads')
                                    Total: {{ $total_heads }} pcs
                                @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                    Total: {{ $total_heads }} pcs | {{ $total_kilos }} kg
                                @endif
                            </div>

                            <table class="order-item-table">
                                <thead>
                                    <tr>
                                        <th>Delivery Day</th>
                                        @if ($item->product->measurement_type === 'Kilos')
                                            <th>Kilos</th>
                                        @elseif ($item->product->measurement_type === 'Heads')
                                            <th>Heads</th>
                                        @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                            <th>Heads</th>
                                            <th>Kilos</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->supplier->delivery->delivery_days as $day)
                                        <tr>
                                            <td>{{ $day }}</td>

                                            @if ($item->product->measurement_type === 'Kilos')
                                                <td>
                                                    <input type="number" 
                                                        name="items[{{ $item->id }}][{{ $day }}][kilos]"
                                                        value="{{ $per_day_kilos }}"
                                                        step="0.01" min="0">
                                                </td>
                                            @elseif ($item->product->measurement_type === 'Heads')
                                                <td>
                                                    <input type="number" 
                                                        name="items[{{ $item->id }}][{{ $day }}][heads]"
                                                        value="{{ $per_day_heads }}"
                                                        step="1" min="0">
                                                </td>
                                            @elseif ($item->product->measurement_type === 'Heads&Kilos')
                                                <td>
                                                    <input type="number" 
                                                        name="items[{{ $item->id }}][{{ $day }}][heads]"
                                                        value="{{ $per_day_heads }}"
                                                        step="1" min="0" placeholder="pcs">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                        name="items[{{ $item->id }}][{{ $day }}][kilos]"
                                                        value="{{ $per_day_kilos }}"
                                                        step="0.01" min="0" placeholder="kg">
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @endforeach

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

                        // ✅ Distribute initial values properly (handle decimals nicely)
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

                        // ✅ Prevent exceeding total
                        if (changedValue > remaining) {
                            changedValue = remaining;
                            changedInput.value = changedValue;
                        }

                        // ✅ Recalculate other fields proportionally
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
    {{-- file an action --}}
    <div class="modal fade" id="fileanaction" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
        <div class="modal-dialog">

            <form class="modal-content" style="width: 700px;" method="POST" enctype="multipart/form-data" action="{{ route('delivery.confirm') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                {{-- Display validation and success/error messages --}}
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

                @if (session('success'))
                    <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin: 10px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                        <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                    </div>
                @endif

                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">File an action for this order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>Committing any changes may be irreversible.</span>
                    </p>

                    {{-- STATUS --}}
                    <div class="modal-option-groups">
                        <p><span class="req-asterisk">*</span> What action would you like to do with this order?</p>
                        <select name="status" required>
                            <option value="">-- Select order status --</option>
                            <option value="Delivered">Mark as delivered</option>
                        </select>
                    </div>

                    {{-- FILE UPLOAD --}}
                    <div class="form-group">
                        <p style="margin: 0"><span class="req-asterisk">*</span>Upload signed POD (PDF only)</p>
                        <input type="file" name="pod_file" required accept="application/pdf">
                        <div id="file-error" style="color:#dc3545; font-size:13px; margin-top:5px;"></div>
                    </div>

                    {{-- RECEIVED QUANTITIES --}}
                    @if(isset($items) && count($items) > 0)
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Product ID</th>
                                        <th style="width: 200px;">Product</th>
                                        <th style="width: 160px;">Planned</th>
                                        <th style="width: 200px;">Received</th>
                                        <th style="width: 140px;">Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    @php
                                        $measurement = $item->product->measurement_type;
                                        $plannedHeads = $item->delitem->planned_heads ?? $item->delitem->placed_heads ?? 0;
                                        $plannedKilos = $item->delitem->planned_kilos ?? $item->delitem->placed_kilos ?? 0;
                                    @endphp

                                    <tr>
                                        <td>{{ $item->product_id }}</td>
                                        <td class="text-start">
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">{{ $measurement }}</small>
                                        </td>

                                        {{-- PLANNED --}}
                                        <td>
                                            @if ($measurement === 'Kilos')
                                                <div><strong>{{ $plannedKilos }}</strong> <small>kg</small></div>
                                            @elseif ($measurement === 'Heads')
                                                <div><strong>{{ $plannedHeads }}</strong> <small>heads</small></div>
                                            @elseif ($measurement === 'Heads&Kilos')
                                                <div><strong>{{ $plannedHeads }}</strong> <small>heads</small></div>
                                                <div><strong>{{ $plannedKilos }}</strong> <small>kg</small></div>
                                            @endif
                                        </td>

                                        {{-- RECEIVED --}}
                                        <td>
                                            @if ($measurement === 'Kilos')
                                                <input type="number"
                                                    name="received_kilos[{{ $item->delItem->delivery_item_id ?? '' }}]"
                                                    class="form-control received-input mb-1"
                                                    data-planned="{{ $plannedKilos }}"
                                                    step="0.01"
                                                    placeholder="Enter kilos">

                                            @elseif ($measurement === 'Heads')
                                                <input type="number"
                                                    name="received_heads[{{ $item->delItem->delivery_item_id ?? '' }}]"
                                                    class="form-control received-input mb-1"
                                                    data-planned="{{ $plannedHeads }}"
                                                    step="1"
                                                    placeholder="Enter heads">

                                            @elseif ($measurement === 'Heads&Kilos')
                                                <div class="d-flex flex-column gap-2">
                                                    <input type="number"
                                                        name="received_heads[{{ $item->delItem->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input"
                                                        data-planned="{{ $plannedHeads }}"
                                                        step="1"
                                                        placeholder="Enter heads">
                                                    <input type="number"
                                                        name="received_kilos[{{ $item->delItem->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input"
                                                        data-planned="{{ $plannedKilos }}"
                                                        step="0.01"
                                                        placeholder="Enter kilos">
                                                </div>
                                            @endif
                                        </td>

                                        {{-- VARIANCE --}}
                                        <td>
                                            @if ($measurement === 'Heads&Kilos')
                                                <div class="variance-text-heads text-muted mb-1">—</div>
                                                <div class="variance-text-kilos text-muted">—</div>
                                            @else
                                                <p class="variance-text text-muted m-0">—</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mt-3">No delivery items found for this order.</p>
                    @endif





                    {{-- FEEDBACK --}}
                    <div class="modal-option-groups">
                        <p>Feedback (Optional)</p>
                        <input type="text" name="feedback" maxlength="200">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit order status</button>
                </div>
            </form>


        </div>
    </div>
    {{-- view POD --}}
    <div class="modal fade" id="viewPOD" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">POD</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span></span>
                    </p>
                    
                   
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('orders.list') }}">< Orders list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Order</p>

                <div>
                    <p>{{$order->order_id}}</p>
                </div>
                @if (Auth()->user()->role !== 'Supplier' && $order->status === 'Pending')
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify account</button>
                    </div>
                @elseif (Auth()->user()->role === 'Supplier')
                    <div>
                        <button data-bs-toggle="modal"  class="btn-transition">Purchase order</button>
                    </div>
                @endif




                    
                


            </div>
            <div>
                <div style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                    <div style="display: flex; flex-direction: row; gap: 10px">
                        <!-- Buttons -->
                        @if ($order->status === 'Accepted' && Auth()->user()->role !== 'Supplier')
                            <button type="button" 
                                data-bs-toggle="modal" data-bs-target="#processModal" 
                                data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                class="btn-transition">
                                    Process order
                            </button>
                        @elseif ($order->status === 'Processed' && Auth()->user()->role !== 'Supplier')
                            <button type="button" 
                                data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                class="btn-transition">
                                    Customer Order
                            </button>



                            <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                    data-url="{{ route('orders.invoice.pdf', $order->order_id) }}"
                                    class="btn-transition">
                                Sales Invoice
                            </button>
                        @endif

                    </div>
                </div>

                <!-- Modal -->
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

                <p>Status: {{$order->status}}</p>
              
            </div>


        </div>


        <div>
           <p>Delivery frequency: <span>{{$order->supplier->delivery->delivery_frequency}}</span></p>
        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="table-body" style="margin-top: 50px">
                <p style="margin: 5px; font-weight: bold;">Scheduled deliveries</p>
                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deliveries as $delivery)
                                <tr>
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


                                    {{-- ACTION BUTTONS --}}
                                    <td>
                                        @if (Auth()->user()->role !== "Supplier")
                                            @if($delivery->status === "Scheduled")
                                                <button type="button" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pdfModal" 
                                                    data-url="{{ route('orders.delivery.pdf', $delivery->delivery_id) }}"
                                                    class="btn-transition">
                                                        Print DR
                                                </button>  
                                            @elseif($delivery->status === "Delivered")
                                                @if($delivery->pod_file)
                                                    <button type="button" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewPOD{{ $delivery->delivery_id }}" 
                                                            class="btn-transition">
                                                            View POD
                                                    </button>
                                                @else
                                                    <span class="text-muted">No POD</span>
                                                @endif
                                            @endif
                                        @else
                                            <button data-bs-toggle="modal" data-bs-target="#fileanaction">File an action</button>
                                        @endif
                                    </td>
                                </tr>
                                    @if($delivery->pod_file)
                                        @php
                                            $podData = 'data:' . ($delivery->pod_mime ?? 'application/pdf') . ';base64,' . base64_encode($delivery->pod_file);
                                        @endphp

                                        <div class="modal fade" id="viewPOD{{ $delivery->delivery_id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Proof of Delivery - {{ $delivery->delivery_id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center" style="height: 80vh;">
                                                        <iframe
                                                            src="{{ $podData }}"
                                                            width="100%"
                                                            height="100%"
                                                            style="border: none;"
                                                            title="POD for {{ $delivery->delivery_id }}"
                                                        ></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="{{ $podData }}" 
                                                        download="POD_{{ $delivery->delivery_id }}.pdf" 
                                                        class="btn btn-primary">
                                                            Download POD
                                                        </a>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                            @endforeach
                        </tbody>
                    </table>

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
@endsection



@push('scripts')
    <script src="{{ asset('js/global/password.js') }}"></script>
    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/pdf_view.js') }}"></script>
    <script src="{{ asset('js/order/variance.js') }}"></script>



@endpush