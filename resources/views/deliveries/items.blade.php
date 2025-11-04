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
                    @if (Auth()->user()->role !== "Customer")
                            @if($delivery->status === "Scheduled")
                                <button type="button" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#pdfModal" 
                                    data-url="{{ route('orders.delivery.pdf', $delivery->delivery_id) }}"
                                    class="btn-transition">
                                        Print DR
                                </button>  
                                
                            @elseif($delivery->status === "Delivered")
                                    <button type="button" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewPOD{{ $delivery->delivery_id }}" 
                                            class="btn-transition">
                                            View POD
                                    </button>

                            @else
                                    <span class="text-muted">No POD</span>
                            @endif

                        
                    @else
          
                    @endif
                </td>
                <p>Total order: <strong>₱{{ number_format($receivedTotal, 2) }}</strong></p>

            @elseif($delivery->status === "Scheduled")
                @if (Auth()->user()->role === "Customer" && $delivery->status === "Scheduled")
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
                            <p style="font-size: 13px; color: #666; margin: 0;">If there’s a variance, please provide an explanation below.</p>
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
                                <th>Nego price</th>

                                <th>Total Amount</th>
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
        
                                    <td>
                                        @if ($item->orderItem->product->measurement_type === "Heads")
                                            {{ $item->planned_heads}} heads
                                        @elseif ($item->orderItem->product->measurement_type === "Kilos")
                                            {{ $item->planned_kilos}}kg
                                        @elseif ($item->orderItem->product->measurement_type === "Heads&Kilos")
                                             {{ $item->planned_heads}} heads | {{ $item->planned_kilos}}kg
                                        @else
                                            —
                                        @endif
                                    </td>

                                    {{-- RECEIVED --}}

                                    <td>
                                        @if ($item->status !== "Delivered")

                                        @else
                                            @if ($item->orderItem->product->measurement_type === "Heads")
                                                {{ $item->received_heads}} heads
                                            @elseif ($item->orderItem->product->measurement_type === "Kilos")
                                                {{ $item->received_kilos}}kg
                                            @elseif ($item->orderItem->product->measurement_type === "Heads&Kilos")
                                                {{ $item->received_heads}} heads | {{ $item->received_kilos}}kg
                                            @else
                                                —
                                            @endif
                                        @endif

                                    </td>

                                    @php
                                        $varianceHeads = ($item->planned_heads ?? 0) - ($item->received_heads ?? 0);
                                        $varianceKilos = ($item->planned_kilos ?? 0) - ($item->received_kilos ?? 0);
                                        $hasVariance = $varianceHeads != 0 || $varianceKilos != 0;
                                    @endphp

                                    {{-- VARIANCE DISPLAY --}}
                                    <td>
                                        @if ($item->status === "Delivered")
                                            @if (!$hasVariance)
                                                <span style="color: green;">Exact</span>
                                            @else
                                                @if ($varianceHeads != 0)
                                                    <span style="color: red;">
                                                        {{ $varianceHeads > 0 ? '-' : '+' }}{{ abs($varianceHeads) }} heads
                                                    </span>
                                                    @if ($varianceKilos != 0)
                                                        <br>
                                                    @endif
                                                @endif

                                                @if ($varianceKilos != 0)
                                                    <span style="color: red;">
                                                        {{ $varianceKilos > 0 ? '-' : '+' }}{{ number_format(abs($varianceKilos), 2) }} kg
                                                    </span>
                                                @endif
                                            @endif
                                            
                                        @endif

                                    </td>

                                    <td>{{ ucfirst($item->status ?? 'Pending') }}</td>
                                    <td>
                                        ₱{{ number_format($item->productSetting->nego_price, 2) }}
                                    </td>
                                    <td>
                                        ₱{{ number_format($item->received_kilos * $item->productSetting->nego_price, 2) }}
                                    </td>

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
        <td>
            @if ($item->variance_heads !== 0 || $item->variance_kilos !== NULL)

                <button type="button" 
                        data-bs-toggle="modal" data-bs-target="#pdfModal" 
                        data-url="{{ route('orders.return-slip.pdf', $delivery->order_id) }}"
                        class="btn-transition">
                    View return slip
                </button>

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
                {{$delivery->feedback}}

                
            @elseif ($item->variance_heads === NULL OR $item->variance_heads === NULL)
                {{$delivery->feedback}}
            @endif
        </td>

            
        </div>
   </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/order/variance.js') }}"></script>
    <script src="{{ asset('js/global/pdf_view.js') }}"></script>

@endpush