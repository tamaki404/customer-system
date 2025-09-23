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
                @endif




                    
                


            </div>
            <div>

            <div style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                <p style="margin: 0">
                    <span>Print</span>
                </p>
                <div>
                    <a href="{{ route('orders.customer.pdf', $order->id) }}" target="_blank">
                        <button type="button">Customer order</button>
                    </a>
                    <a href="{{ route('orders.delivery.pdf', $order->id) }}" target="_blank">
                        <button type="button">Delivery receipt</button>
                    </a>
                    <a href="{{ route('orders.invoice.pdf', $order->id) }}" target="_blank">
                        <button type="button">Sales invoice</button>
                    </a>
                </div>
            </div>




                <p>{{$order->status}}</p>
                <p style="display: flex; flex-direction: column;">
                    @foreach ($items as $item)
                        <span>{{$item->product->name}}</span>
                        <span>{{ $item->productSetting?->price ?? 'N/A' }}</span>

                        
                    @endforeach
                </p>
                <button onclick="">View purchase order</button>
            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
          
         
       
        </div>


   </div>
@endsection



@push('scripts')
    <script src="{{ asset('js/global/password.js') }}"></script>
    <script src="{{ asset('js/global/two_mb.js') }}"></script>



@endpush