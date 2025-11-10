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
                <form class="modal-content"  method="POST" action="{{ route('pym.create') }}"  enctype="multipart/form-data">
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
                                 <select name="po_id" id="">
                                    <option value="">-- Select order --</option> 
                                    @foreach($purchasesWithBalance as $unpaidOrder)
                                        <option value="{{ $unpaidOrder->po_id }}">
                                            {{ $unpaidOrder->po_id }} | {{ \Carbon\Carbon::parse($unpaidOrder->created_at)->format('F j, Y') }} | ₱{{ number_format($unpaidOrder->total_balance, 2) }}
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
                       @php
                            $available_credit = ($credit->credit_limit - $currentBalance);
                       @endphp
                        <div class="credit-row">
                            <span class="credit-label" >Credit limit: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($credit->credit_limit, 2) }}</span>
                        </div>
                       <div class="credit-row">
                            <span class="credit-label" >Balance: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($currentBalance, decimals: 2) }}</span>
                        </div>
                       <div class="credit-row">
                            <span class="credit-label" >Paid: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($alreadyPaid, 2) }}</span>
                        </div>
                       <div class="credit-row">
                            <span class="credit-label" >Available: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($available_credit, 2) }}</span>
                        </div>
                       {{--<div class="credit-row">
                            <span class="credit-label">Outstanding balance: </span>
                            <span class="credit-value"style="margin-left: 10px">₱{{ number_format($usedCredit, 2) }}</span>
                        </div> --}}
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
                                <p style="display: flex; margin: 5px; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: bold;, font-size: 14px;">Transaction history</span>
                                    <span style="font-size: 13px; color: #666;">Here shows your history on activities involving credits, such as accepting deliveries, uploading receipt and accepted receipt</span>
                                </p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff; position:sticky; z-index: 1; top: 0;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Created at</th>
                                                <th>PO ID</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($transactions as $transaction)
                                            <div class="modal fade" id="view-receipt-modal-{{ $transaction->receipt_id }}" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true"> 
                                                <div>
                                                    <div class="modal-dialog">
                                                        <div class="modal-content" >
                                                            @csrf
                                                            <div class="modal-header">
                                                                <p class="modal-title" id="requestActionLabel">Receipt #{{  $transaction->payment_id  }}</p>
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


                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="display: flex; flex-direction: row; gap: 5px; align-items: center; font-size: 13px;"><span class="material-symbols-outlined" style="font-size: 13px">picture_as_pdf</span>Pdf viewer</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->action_at)->format('M d, Y') }}</td>
                                                    <td>{{ $transaction->po_id }}</td>
                                                    <td>{{ $transaction->status }}</td>
                                                    @if ($transaction->status ===  "Successful" && $transaction->label ===  "Payment")
                                                       <td style="color: green">₱ +{{ number_format($transaction->amount, 2) }}</td>
                                                    @elseif ($transaction->status ===  "Successful" && $transaction->label ===  "Delivery")
                                                       <td style="color: #dc3545">₱ -{{ number_format($transaction->amount, 2) }}</td>
                                                    @elseif ($transaction->status ===  "Pending" && $transaction->label ===  "Payment")
                                                       <td style="color: #666">--</td>
                                                    @elseif ($transaction->status ===  "Rejected" && $transaction->label ===  "Payment")
                                                       <td style="color: red">--</td>
                                                    @endif
                                                    <td>
                                                        <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                            <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                <span class="material-symbols-outlined">
                                                                expand_circle_down
                                                                </span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item"  style="color:#f8a01d" ><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                @if($transaction->label === "Delivery")
                                                                    <li>
                                                                        <a class="dropdown-item">
                                                                        {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                            <span class="material-symbols-outlined">arrow_outward</span>
                                                                            Go to Delivery
                                                                        </a>
                                                                    </li>
                                                                @elseif($transaction->label === "Payment")
                                                                
                                                                    <li>
                                                                        <a class="dropdown-item">
                                                                        {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                            <span class="material-symbols-outlined">arrow_outward</span>
                                                                            Go to receipt
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item">
                                                                        {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                            <span class="material-symbols-outlined">capture</span>
                                                                            View receipt
                                                                        </a>
                                                                    </li>
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
                                <p style="display: flex; margin: 5px; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: bold;, font-size: 14px;">Payables</span>
                                    <span style="font-size: 13px; color: #666;">Here shows the active and closed payable purchase orders </span>
                                </p>                                
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff; position:sticky; z-index: 1; top: 0;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Last update at</th>
                                                <th>PO ID</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>                             
                                            @foreach ($purchaseData as $payable)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $payable['purchase_request']->updated_at->format("F j, Y, g:i a") }}</td>
                                                <td>{{ $payable['po_id'] }}</td>
                                                <td>₱{{ number_format($payable['remaining_balance'],2) }}</td>
                                                <td>Partially paid</td>
                                                <td>
                                                    <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                        <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                            <span class="material-symbols-outlined">
                                                            expand_circle_down
                                                            </span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item"  style="color:#f8a01d" ><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                            <li>
                                                                <a class="dropdown-item">
                                                                {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                    <span class="material-symbols-outlined">arrow_outward</span>
                                                                    Receipt collection
                                                                </a>
                                                            </li>
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
                        {{-- Payments --}}
                        <div id="payment-content" class="tab-content" role="tabpanel" aria-labelledby="payment-tab">
                            <div class="table-body" style="margin-top: 10px">
                                <p style="display: flex; margin: 5px; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: bold;, font-size: 14px;">Payments</span>
                                    <span style="font-size: 13px; color: #666;">Here shows the accepted payments from sent uplaoded receipts</span>
                                </p>    
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Last update at</th>
                                                <th>PO ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($payments as $receipt)
                                                {{-- <tr onclick="window.location.href='{{ route('pym.payment', ['payment_id' => $receipt->payment_id]) }}'"> --}}
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ \Carbon\Carbon::parse($receipt->updated_at)->format("F j, Y, g:i a") }}</td>
                                                    <td>{{$receipt->po_id}}</td>
                                                    <td>₱{{$receipt->total_amount}}</td>
                                                    <td>{{$receipt->status}}</td>
                                                    <td>
                                                        <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                            <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                <span class="material-symbols-outlined">
                                                                expand_circle_down
                                                                </span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item"  style="color:#f8a01d" ><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                <li>
                                                                    <a class="dropdown-item">
                                                                    {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                        <span class="material-symbols-outlined">arrow_outward</span>
                                                                        Receipt collection
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item">
                                                                    {{-- href="{{ route('order.delivery_items', ['delivery_id' => $transaction->delivery->delivery_id]) }}"> --}}
                                                                        <span class="material-symbols-outlined">capture</span>
                                                                        View receipt
                                                                    </a>
                                                                </li>
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

                    </div>

                



                
            
                </div>


        </div>

@endsection



@push('scripts')
    <script src="{{ asset('js/global/x/profile-tab.js') }}"></script>

    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/file-preview.js') }}"></script>
@endpush