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
                    <p class="modal-title" id="requestActionLabel">Process order</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="order_id" value="{{$order->order_id}}" required>
                
                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>Committing any changes may be irreversible.</span>
                    </p>

                    <p>Delivery frequency: <span>{{$order->supplier->delivery->delivery_frequency}}</span></p>
                    <div class="modal-option-groups">
                        <p>How many times in a week?</p>
                        <div>
                            <input type="date" >
                        </div>

                    </div>
                    
                    <div class="modal-option-groups">
                        <p>Status</p>
                        <select name="status" required>
                            <option value="">-- Select order status --</option>
                            <option value="Accepted">Process this order</option>
                            <option value="Rejected">Cancel this order</option>
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

            <!-- Buttons -->
            @if ($order->status === 'Processing' && Auth()->user()->role !== 'Supplier')
                <div style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                    <p style="margin: 0"><span>Print</span></p>
                    <div style="display: flex; flex-direction: row; gap: 10px">
                        <button type="button" 
                                                data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                                data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                                                class="btn-transition">
                                            Customer Order
                                        </button>

                                        <button type="button" 
                                                data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                                data-url="{{ route('orders.delivery.pdf', $order->order_id) }}"
                                                class="btn-transition">
                                            Delivery Receipt
                                        </button>

                                        <button type="button" 
                                                data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                                data-url="{{ route('orders.invoice.pdf', $order->order_id) }}"
                                                class="btn-transition">
                                            Sales Invoice
                                        </button>
                    </div>
                </div>
            @elseif ($order->status === 'Accepted' && Auth()->user()->role !== 'Supplier')
                <div style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                    <div style="display: flex; flex-direction: row; gap: 10px">
                        <button type="button" 
                            data-bs-toggle="modal" data-bs-target="#processModal" 
                            data-url="{{ route('orders.customer.pdf', $order->order_id) }}"
                            class="btn-transition">
                                Process order
                        </button>
                    </div>
                </div>

            @endif
                     


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



@endpush