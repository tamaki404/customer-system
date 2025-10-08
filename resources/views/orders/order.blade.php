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
            <form class="modal-content" method="POST"  enctype="multipart/form-data" action="{{ route('order.process') }}">

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
                    <p class="modal-title" id="requestActionLabel">Process order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="order_id" value="{{$order->order_id}}" required>
                
                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>You can edit the heads/kilos just before the delivery.</span>
                    </p>

                    <p>Delivery details: <span></span></p>

                    
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
                                        @if($item->product->measurement_type === 'Kilos')
                                            {{ $item->placed_kilos }} kg
                                        @else
                                            {{ $item->placed_heads }} heads
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @foreach($items as $item)
                        @php
                            $total = $item->product->measurement_type === 'Kilos'
                                ? $item->placed_kilos
                                : $item->placed_heads;

                            $days = count($order->supplier->delivery->delivery_days);
                            $per_day = $days > 0 ? round($total / $days, 2) : $total;
                        @endphp

                        <div class="order-item">
                            <p>{{ $item->product->name }}</p>
                            <p>Total {{ $item->product->measurement_type === 'Kilos' ? 'kg' : 'heads' }}: {{ $total }}</p>

                            @foreach($order->supplier->delivery->delivery_days as $day)
                                <div class="delivery-day">
                                    <label>{{ $day }}</label>
                                    <input type="number"
                                        name="items[{{ $item->id }}][{{ $day }}]"
                                        value="{{ $per_day }}"
                                        step="0.01"
                                        min="0">
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <script>
                        document.querySelectorAll('.delivery-day input').forEach(input => {
                        input.addEventListener('input', (e) => {
                            const itemDiv = e.target.closest('.order-item');
                            const inputs = itemDiv.querySelectorAll('.delivery-day input');
                            const total = parseFloat(itemDiv.dataset.total);
                            let sumOther = 0;

                            inputs.forEach(i => {
                                if(i !== e.target) sumOther += parseFloat(i.value) || 0;
                            });

                            // Automatically adjust last day if needed
                            const remaining = total - sumOther - parseFloat(e.target.value);
                            const lastInput = inputs[inputs.length - 1];
                            if(lastInput !== e.target){
                                lastInput.value = remaining > 0 ? remaining.toFixed(2) : 0;
                            }
                        });
                    });

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
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product ID</th>
                    <th>Product</th>
                    <th>Planned</th>
                    <th>Received</th>
                    <th>Variance</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                @php
                    $isKilos = $item->product->measurement_type === "Kilos";
                    $plannedValue = $isKilos
                        ? ($item->planned_kilos ?? $item->placed_kilos ?? 0)
                        : ($item->planned_heads ?? $item->placed_heads ?? 0);
                @endphp
                <tr>
                    <td>{{ $item->product_id }}</td>
                    <td>{{ $item->product->name }}</td>

                    {{-- PLANNED --}}
                    <td>
                        <span class="planned-value" data-measure="{{ $isKilos ? 'kilos' : 'heads' }}">
                            {{ $plannedValue }}
                        </span>
                        {{ $isKilos ? 'kilos' : 'heads' }}
                    </td>

                    {{-- RECEIVED --}}
                    <td>
                        @if ($isKilos)
                            <input type="number"
                                   name="received_kilos[{{ $item->delItem->delivery_item_id }}]"
                                   class="form-control received-input"
                                   data-planned="{{ $plannedValue }}"
                                   step="0.01"
                                   placeholder="Enter kilos">
                        @else
                            <input type="number"
                                   name="received_heads[{{ $item->delItem->delivery_item_id }}]"
                                   class="form-control received-input"
                                   data-planned="{{ $plannedValue }}"
                                   placeholder="Enter heads">
                        @endif
                    </td>

                    {{-- VARIANCE --}}
                    <td>
                        <p class="variance-text text-muted m-0">—</p>
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
                                                <th>Total Heads</th>
                                                <th>Total Kilos</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deliveries as $delivery)
                                {{-- <tr onclick="window.location.href='{{ route('order.delivery_items', ['delivery_id' => $delivery->delivery_id]) }}'"> --}}
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $delivery->delivery_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</td>
                                    <td>
                                        
                                    </td>
                                    <td>{{($delivery->deliveryItems)->count()}}</td>
                                    <td>
                                        @php
                                            $totalHeads = $delivery->deliveryItems->sum('planned_heads');
                                        @endphp
                                        @if ($totalHeads > 0)
                                            {{ $totalHeads }} heads
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $totalKilos = $delivery->deliveryItems->sum('planned_kilos');
                                        @endphp
                                        @if ($totalKilos > 0)
                                            {{ $totalKilos }} kg
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            
                                            $isToday = \Carbon\Carbon::parse($delivery->delivery_date)->isToday();
                                            $color = match($delivery->status) {
                                                'Completed' => 'green',
                                                'Scheduled' => 'orange',
                                                'In Transit' => 'blue',
                                                'Cancelled' => 'gray',
                                                default => 'black',
                                            };
                                        @endphp
                                        <span style="color: {{ $color }};">
                                            @if($isToday && $delivery->status === 'Scheduled')
                                                <strong style="color: green;">Delivery Today</strong>
                                            @elseif($isToday !== $delivery->delivery_date)
                                                {{ $delivery->status }}
                                            @endif
                                        </span>
                                    </td>

                                    <td>{{ $delivery->remarks ?? '—' }}</td>
                                    <td>
                                        @if (Auth()->user()->role!=="Supplier")
                                            @if($delivery->status === "Scheduled" )
                                                <button type="button" 
                                                    data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                                    data-url="{{ route('orders.delivery.pdf', $delivery->delivery_id) }}"
                                                    class="btn-transition">
                                                        Print DR
                                                </button>  
                                            @elseif($delivery->status === "Delivered" )
                                                    Completed
                                            @endif
                                        @elseif (Auth()->user()->role==="Supplier")
                                                <button data-bs-toggle="modal" data-bs-target="#fileanaction" >File an action</button>
                                        @endif

                                            
                                    </td>
                                </tr>
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