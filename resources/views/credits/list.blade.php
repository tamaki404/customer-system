@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">
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


        {{-- upload receipt modal --}}
        <div class="modal fade" id="add-receipt-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content"  method="POST" action="{{ route('receipt.create') }}"  enctype="multipart/form-data">
                    @csrf
                
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Payment receipt form</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span> Make sure image selected is 2MB or less, scanned image is recommended.</span>

                        </p>

                        <div class="modal-option-groups">
                            <div class="form-group">
                                <p><span class="req-asterisk">*</span>Unpaid orders</p>
                                 <select name="order_id" id="">
                                    <option value="">-- Select order --</option> 
                                    @foreach($unpaidOrders as $unpaidOrder)
                                        <option value="{{ $unpaidOrder->order_id }}">
                                            {{ $unpaidOrder->order_id }} | {{ \Carbon\Carbon::parse($unpaidOrder->created_at)->format('F j, Y') }} | ₱{{ number_format($unpaidOrder->total_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                    <p><span class="req-asterisk">*</span>Upload receipt image</p>
                                    <input type="file" name="image" id="image" required accept="image/*">
                                    <div id="file-preview" style="margin-top:10px;"></div>
                                    <div id="file-error" style="color:#dc3545; font-size:13px; margin-top:5px;"></div>
                            </div>
                            <input type="hidden" name="status" value="Pending">
                            <input type="hidden" name="customer_id" value="{{ auth()->user()->customer->customer_id }}">

                        </div>
        

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="add-staff-submit">Submit receipt</button>
                    </div>


                
                </form>
            </div>
        </div>


        <div class="content-bg" >
                <div class="content-header">
                    <div class="contents-display">
                        {{-- <p>
                            <a href="{{ route('staffs.list') }}">< Staffs list</a>
                        </p> --}}
                    </div>

                    <div class="title-actions">
                        <p class="heading">Credits</p>
                        <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#add-receipt-modal">
                            <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                            Upload a receipt
                        </button>
                
                    </div>


                </div>

                <div class="content-body" style="padding: 10px; border: none; height: auto; gap: 10px">
                    <style>
                        .credit-row span{
                            margin: 0;
                        }
                        .credit-row .credit-label{
                            font-size: 13px;
                            color: #888;

                        }
                        .credit-row .credit-value{
                            color: #333;
                            font-weight: bold;
                        }
                    </style>

                    <div class="credit-summary" style="padding: 10px; height: auto; border-radius: 5px; width: 400px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; background-color: #fff; border: none;">
                        <div class="credit-row" style="display: flex; flex-direction: column;">
                            <span class="credit-label">Available credit:</span>
                            <span class="credit-value available" style="font-size: 30px; color: #f8912a;">₱{{ number_format($availableCredit, 2) }}</span>
                        </div>
                        <div class="credit-row">
                            <span class="credit-label" >Credit limit: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($credit->credit_limit, 2) }}</span>
                        </div>
                        <div class="credit-row">
                            <span class="credit-label">Outstanding balance: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($usedCredit, 2) }}</span>
                        </div>
                    </div>

                    <div class="tab-div" style="margin-top: 20px;">

                        <div class="tabs" role="tablist">
                            <button class="tab-button active" data-tab="transaction" role="tab" aria-selected="false" aria-controls="transaction-content" id="transaction-tab">
                                Transaction history
                            </button>
                            <button class="tab-button " data-tab="payables" role="tab" aria-selected="true" aria-controls="payables-content" id="payables-tab">
                                Payables
                            </button>
                            <button class="tab-button" data-tab="payment" role="tab" aria-selected="true" aria-controls="payment-content" id="payment-tab">
                                Payments
                            </button>

                        </div>

                        {{-- Transaction history --}}
                        <div id="transaction-content" class="tab-content active" role="tabpanel" aria-labelledby="transaction-tab">
                            <div class="table-body">
                                <p style="margin: 5px; font-weight: bold;">Transaction history</p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff; position:sticky; z-index: 1; top: 0;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Order ID</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($transactionHistory as $transaction)
                                                {{-- uvire receipt modal --}}
                                                <div class="modal fade" id="view-receipt-modal-{{ $transaction->receipt_id }}" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content" >
                                                            @csrf
                                                            <div class="modal-header">
                                                                <p class="modal-title" id="requestActionLabel">Receipt #{{  $transaction->receipt_id  }}</p>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="height: 600px; overflow: auto; display: flex; flex-direction: column;">
                                                                <p class="note-notify">
                                                                    <span class="material-symbols-outlined"> info </span>
                                                                    <span> Make sure image selected is 2MB or less, scanned image is recommended.</span>
                                                                </p>
                                                                <div class="modal-option-groups">
                                                                    @if ($transaction->receipt)
                                                                        <p style="display:flex; flex-direction: row; justify-content: space-between;">
                                                                            <span style="font-size: 13px">{{ $transaction->receipt->status }}</span>     
                                                                            <span style="font-size: 13px; color: #666;">Updated at {{ \Carbon\Carbon::parse($transaction->receipt->action_at)->format('F j, Y') }}</span>
                                                                        </p>
                                                                        <button class="collection-btn" onclick="window.location.href='{{ route('orders.receipt', ['order_id' => $transaction->order_id]) }}'" style="border-radius: 5px; min-width: 150px; max-width: 180px;">
                                                                            <span class="material-symbols-outlined" style="width:auto">
                                                                            grain
                                                                            </span>
                                                                            Receipt collection
                                                                        </button> 

                                                                        @php
                                                                            $imgSrc = $transaction->receipt && $transaction->receipt->image
                                                                                ? 'data:' . $transaction->receipt->image_mime_type . ';base64,' . base64_encode($transaction->receipt->image)
                                                                                : asset('assets/default-company-logo.png');
                                                                        @endphp

                                                                        <img src="{{ $imgSrc }}" alt="Receipt image" style="height: 70%; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-radius: 5px">
                                                                    @endif


                                                                    {{-- <div class="document-card text-center" style="width: 220px;">
                                                                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden" style="cursor: pointer; height: 300px;"
                                                                            data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                                                            
                                                                            <div class="ratio ratio-4x3 bg-light" style="height: 80%">
                                                                                @if ($pdfData)
                                                                                    <iframe
                                                                                        src="{{ $pdfData }}#toolbar=0&navpanes=0&scrollbar=0&page=1&"
                                                                                        style="width: 100%; height: 100%; pointer-events: none; border: none;"
                                                                                        title="PDF Preview"
                                                                                    ></iframe>
                                                                                @else
                                                                                    <p class="text-danger">No document</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div> --}}

                                                                    {{-- <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">{{ $document->type }}</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                                </div>
                                                                                <div class="modal-body text-center" style="height: 80vh;">
                                                                                    @if ($pdfData)
                                                                                        <iframe
                                                                                            src="{{ $pdfData }}"
                                                                                            width="100%"
                                                                                            height="100%"
                                                                                            style="border: none;"
                                                                                            title="{{ $document->type }} Full View"
                                                                                        ></iframe>
                                                                                    @else
                                                                                        <p class="text-danger">Unable to load PDF.</p>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> --}}
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="display: flex; flex-direction: row; gap: 5px; align-items: center; font-size: 13px;"><span class="material-symbols-outlined" style="font-size: 13px">picture_as_pdf</span>Pdf viewer</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                                {{-- <tr onclick="window.location.href='{{ route('orders.receipt', ['order_id' => $transaction->order_id]) }}'"> --}}
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->action_at)->format('M d, Y') }}</td>
                                                    <td>{{ $transaction->order_id }}</td>
                                                    <td>{{ $transaction->status }}</td>
                                                    @if ($transaction->label === 'Receipt' && $transaction->status === 'Verified' )
                                                       <td style="color: green">₱ +{{ number_format($transaction->amount, 2) }}</td>
                                                    @elseif ($transaction->label === 'Order')
                                                       <td style="color: #dc3545">₱ -{{ number_format($transaction->amount, 2) }}</td>
                                                    @elseif ($transaction->label === 'Receipt' || $transaction->status === 'Rejected' )
                                                       <td style="color: #666">Rejected</td>
                                                    @endif
                                                    <td>
                                                        <div class="dropdown" style="">
                                                            <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                <span class="material-symbols-outlined">
                                                                expand_circle_down
                                                                </span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('orders.order', ['order_id' => $transaction->order_id]) }}"><span class="material-symbols-outlined">package_2</span>Go to Order</a></li>
                                                                @if ($transaction->label === 'Order')
                                                                    @if($transaction->delivery)
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                            href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}">
                                                                                <span class="material-symbols-outlined">orders</span>
                                                                                Go to Delivery
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                @elseif ($transaction->label === 'Receipt')
                                                                    {{-- <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#add-receipt-modal" href="{{ route('receipts.receipt', $transaction->receipt->receipt_id) }}"><span class="material-symbols-outlined">receipt</span>View Receipt</a></li> --}}
                                                                    <li><a class="dropdown-item"   data-bs-toggle="modal" data-bs-target="#view-receipt-modal-{{ $transaction->receipt_id }}"><span class="material-symbols-outlined">receipt</span>View Receipt</a></li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>
                        </div>
                        {{-- Payables --}}
                        <div id="payables-content" class="tab-content" role="tabpanel" aria-labelledby="payables-tab">
                            <div class="table-body" style="margin-top: 10px">
                                <p style="margin: 5px; font-weight: bold;">Payables</p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff; position:sticky; z-index: 1; top: 0;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Order ID</th>
                                                <th>Balance</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>
                                        <tbody>                             
                                        
                                            @foreach ($oustandingPayments as $oustandingPayment)
                                                <tr onclick="window.location.href='{{ route('orders.receipt', ['order_id' => $oustandingPayment->order_id]) }}'">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($oustandingPayment->order_date)->format('M d, Y') }}</td>
                                                    <td>{{ $oustandingPayment->order_id }}</td>
                                                    {{-- <td>
                                                        @foreach ($oustandingPayment->items as $item)
                                                            {{$item->quantity}} {{ $item->product->name }},
                                                        @endforeach
                                                    </td> --}}
                                                    <td><strong>₱{{ number_format($oustandingPayment->outstanding_balance, 2) }}</strong></td>
                                                    <td>--</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>
                        </div>
                        {{-- Payments --}}
                        <div id="payment-content" class="tab-content" role="tabpanel" aria-labelledby="payment-tab">
                            <div class="table-body" style="margin-top: 10px">
                                <p style="margin: 5px; font-weight: bold;">Payments</p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Receipt ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($receipts as $receipt)
                                                <tr onclick="window.location.href='{{ route('receipts.receipt', ['receipt_id' => $receipt->receipt_id]) }}'">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($receipt->created_at)->format('F j, Y') }}</td>
                                                    <td>{{$receipt->receipt_id}}</td>
                                                    <td>₱{{$receipt->total_amount}}</td>
                                                    <td>{{$receipt->status}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>
                        </div>

                    </div>

                



                
            
                </div>


        </div>
@endsection



@push('scripts')
    <script src="{{ asset('js/global/x/profile-tab.js') }}"></script>

    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/file-preview.js') }}"></script>
@endpush