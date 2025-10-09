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
                    <a href="{{ route('orders.order', ['order_id' => $delivery->order_id]) }}">< Order view</a>

                </p>
            </div>

        </div>

        <div class="delivery-status">
            <style>
                .delivery-status p{
                    margin: 0;
                    font-weight: bold;
                }
                .delivery-status p span{
                    font-weight: normal;
                }
            </style>
           <p>Status: <span>{{$delivery->status}}</span></p>
           <p>Delivery ID: <span>{{$delivery->delivery_id}}</span></p>
           <p>Delivered at: <span>{{$delivery->delivered_at}}</span></p>
           @if($delivery->status === "Delivered")
                <p>Delivery date: <span>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format(format: 'F j, Y') }}</span></p>
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
          
                    @endif
                </td>
            @elseif($delivery->status === "Scheduled")
                @if (Auth()->user()->role === "Supplier" && $delivery->status === "Scheduled")
                            <button 
                                type="button"
                                class="btn btn-primary file-action-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#fileanaction">
                                Receive this order
                            </button>
                        
                
                @endif
           @endif



        </div>


        {{-- file an action --}}
        <div class="modal fade" id="fileanaction" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
            <div class="modal-dialog">

                <form class="modal-content" style="width: 700px;" method="POST" enctype="multipart/form-data" action="{{ route('delivery.confirm') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $delivery->order_id }}">
                    <input type="hidden" name="delivery_id" value="{{ $delivery->delivery_id }}">

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
                                            $plannedHeads = $item->planned_heads ?? $item->placed_heads ?? 0;
                                            $plannedKilos = $item->planned_kilos ?? $item->placed_kilos ?? 0;
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
                                                        name="received_kilos[{{ $item->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input mb-1"
                                                        data-planned="{{ $plannedKilos }}"
                                                        step="0.01"
                                                        placeholder="Enter kilos">

                                                @elseif ($measurement === 'Heads')
                                                    <input type="number"
                                                        name="received_heads[{{ $item->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input mb-1"
                                                        data-planned="{{ $plannedHeads }}"
                                                        step="1"
                                                        placeholder="Enter heads">

                                                @elseif ($measurement === 'Heads&Kilos')
                                                    <div class="d-flex flex-column gap-2">
                                                        <input type="number"
                                                            name="received_heads[{{ $item->delivery_item_id ?? '' }}]"
                                                            class="form-control received-input"
                                                            data-planned="{{ $plannedHeads }}"
                                                            step="1"
                                                            placeholder="Enter heads">
                                                        <input type="number"
                                                            name="received_kilos[{{ $item->delivery_item_id ?? '' }}]"
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


        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="table-body" style="margin-top: 50px">
                <p style="margin: 5px; font-weight: bold;">Delivery items</p>
                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                        <thead style="background-color: #f8f8f8;">
                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                <th>#</th>
                                <th>Item ID</th>
                                <th>Product</th>
                                <th>Measurement</th>
                                <th>Planned Qty</th>
                                <th>Received Qty</th>
                                <th>Variance</th>


                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($delivery->deliveryItems as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->delivery_item_id }}</td>
                                    <td>{{ $item->orderItem->product->name ?? '—' }}</td>
                                    <td>{{ $item->orderItem->product->measurement_type ?? '—' }}</td>
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
                                    <td>{{ ucfirst($item->status ?? 'Pending') }}</td>
                                    {{-- ACTION BUTTONS --}}
                                    {{-- <td>
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

                                        @endif
                                    </td>
                                </tr> --}}
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
        </div>

        <div>
            {{ $delivery->feedback }}
        </div>
   </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/order/variance.js') }}"></script>

@endpush