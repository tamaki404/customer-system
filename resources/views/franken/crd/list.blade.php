@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/credits.css') }}">

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
                                 <select name="delivery_id" id="">
                                    <option value="">-- Select order --</option> 
                                    @foreach($deliveryWithBalance as $unpaidOrder)
                                        <option value="{{ $unpaidOrder->delivery_id }}">
                                            {{ $unpaidOrder->delivery_id }} | {{ \Carbon\Carbon::parse($unpaidOrder->updated_at)->format('F j, Y') }} | ₱{{ number_format($unpaidOrder->total_balance, 2) }}
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
        
                    <div class="credit-summary">
                       @php
                            $available_credit = ($credit->credit_limit - $currentBalance);
                       @endphp
                        {{-- <div class="credit-row">
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
                        </div> --}}

                        <p class="info">
                            <span class="material-symbols-outlined icon">
                            info
                            </span>
                            <span>Ordering will be disabled when balance hits 20% of your credit limit</span>
                        </p>

                        <div class="credit-box">
                            <p>
                                <span class="balance">₱{{ number_format($currentBalance, decimals: 2) }}</span>
                            </p>
                            <hr>
                            <p class="label">Account balance</p>
                        </div>
                        <div style="display: flex; flex-direction: row; ">
                        <div class="credit-box">
                            <p>
                                <span class="available">₱{{ number_format($available_credit, decimals: 2) }}</span>
                            </p>
                            <p class="label">Available</p>
                        </div>
                        <hr class="vertical">
                        <div class="credit-box">
                            <p>
                                <span class="available">₱{{ number_format($totalDue, 2) }}</span>
                            </p>
                            <p class="label">Due (Next 15 Days)</p>
                        </div>

                        </div>

                    </div>

                    <div class="tab-div" style="margin-top: 20px;">
                        <div class="tabs" role="tablist">
                            <button class="tab-button active" data-tab="transaction" role="tab" aria-selected="false" aria-controls="transaction-content" id="transaction-tab">
                                Transaction history
                            </button>
                            {{-- <button class="tab-button " data-tab="payables" role="tab" aria-selected="true" aria-controls="payables-content" id="payables-tab">
                                Payables (by PO)
                            </button> --}}
                            <button class="tab-button " data-tab="payables-del" role="tab" aria-selected="true" aria-controls="payables-del-content" id="payables-del-tab">
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
                                            @if (!empty($transactions))
                                                @foreach ($transactions as $transaction)
                                                    {{-- payment-view-modal --}}
                                                        {{-- <div class="modal fade" id="view-receipt-modal-{{ $transaction->payment_id}}" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true"> 
                                                            <div class="modal-dialog">
                                                                <div class="modal-content" >
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <p class="modal-title" id="requestActionLabel">Receipt #{{ $transaction->payment_id}}</p>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body" style="height: 600px; overflow: auto; display: flex; flex-direction: column;">
                                                                        <p class="note-notify">
                                                                            <span class="material-symbols-outlined"> info </span>
                                                                            <span> Make sure image selected is 2MB or less, scanned image is recommended.</span>
                                                                        </p>
                                                                        <div class="modal-option-groups">
                                                                                <p style="display:flex; flex-direction: row; justify-content: space-between;">
                                                                                    <span style="font-size: 13px">{{ $transaction->payment->status }}</span>     
                                                                                    <span style="font-size: 13px; color: #666;">Updated at {{ \Carbon\Carbon::parse($transaction->payment->action_at)->format('F j, Y') }}</span>
                                                                                </p>
                                                                                @php
                                                                                    $imgSrc = $transaction->payment && $transaction->payment->image
                                                                                        ? 'data:' . $transaction->payment->image_mime_type . ';base64,' . base64_encode($transaction->payment->image)
                                                                                        : asset('assets/default-company-logo.png');
                                                                                @endphp
                                                                                <img src="{{ $imgSrc }}" alt="Receipt image" style="height: 70%; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-radius: 5px">
                                                                            
                                                                        </div>
                                                                    </div>
                                                        
                                                                    <div class="modal-footer" style="display: flex; flex-direction: row;">
                                                                            <button class="collection-btn" style="border-radius: 5px; background-color: #888"
                                                                                onclick="window.location.href='{{ route('crd.download', ['payment_id' => $transaction->payment->payment_id]) }}'">
                                                                                <span class="material-symbols-outlined" style="width:auto">
                                                                                download
                                                                                </span>
                                                                                Download image
                                                                            </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                
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
                                                                    <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $transaction->po_id]) }}"><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                    @if($transaction->label === "Delivery")
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('dlv.delivery', ['delivery_id' => $transaction->delivery_id]) }}"
                                                                            >
                                                                                <span class="material-symbols-outlined">arrow_outward</span>
                                                                                Go to Delivery
                                                                            </a>
                                                                        </li>
                                                                    @elseif($transaction->label === "Payment")
                                                                    
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="{{ route('pym.payment', ['payment_id' => $transaction->payment_id ?? NULL]) }}">
                                                                                <span class="material-symbols-outlined">arrow_outward</span>
                                                                                Go to receipt
                                                                            </a>
                                                                        </li>
                                                                        <li>

                                                                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view-receipt-modal-{{ $transaction->payment_id ?? NULL }}">
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
                                            @endif                                
                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>
                        </div>
                        {{-- Payables --}}
                        {{-- <div id="payables-content" class="tab-content" role="tabpanel" aria-labelledby="payables-tab">
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
                                                <th>Total balance</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>              
                                            @if (!empty($purchaseData))

                                                @foreach ($purchaseData as $payable)

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $payable['purchase_request']->updated_at->format("F j, Y, g:i a") }}</td>
                                                    <td>{{ $payable['po_id'] }}</td>
                                                    <td>₱{{ number_format($payable['remaining_balance'],2) }}</td>
                                                    @php
                                                        $dueDate = $payable['nearest_due_date'] 
                                                            ? \Carbon\Carbon::parse($payable['nearest_due_date'])->format('M d, Y') 
                                                            : '--';
                                                    @endphp          

                                                    <td>Partially paid</td>
                                                    <td>
                                                        <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                            <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                <span class="material-symbols-outlined">
                                                                expand_circle_down
                                                                </span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $payable['po_id']]) }}"> <span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('pym.collection', ['po_id' => $payable['po_id']]) }}">
                                                                        <span class="material-symbols-outlined">arrow_outward</span>
                                                                        Payments collection
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>
                        </div> --}}
                        {{-- Payables --}}
                        <div id="payables-del-content" class="tab-content" role="tabpanel" aria-labelledby="payables-del-tab">
                            <div class="table-body" style="margin-top: 10px">
                                <p style="display: flex; margin: 5px; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: bold;, font-size: 14px;">Payables by delivery</span>
                                    <span style="font-size: 13px; color: #666;">Here shows the active and closed payable purchase orders </span>
                                </p>                                
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: overflow-y:auto; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff; position:sticky; z-index: 1; top: 0;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Updated on</th>
                                                <th>PO ID</th>
                                                <th>Delivery ID</th>
                                                <th>Running balance</th>
                                                <th>Due date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($dels))

                                                @foreach ($dels as $del)
                                                    @php
                                                        // Total balance of delivery items
                                                        $totalItems = $del->deliveryItems->sum('balance');
                                                        // Sum of verified payments
                                                        $totalPaid = $del->payments->where('status', 'Verified')->sum('total_amount');
                                                        // Running balance
                                                        $runningBalance = $totalItems - $totalPaid;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $del->updated_at->format('F j, y g:i a') }}</td>
                                                        <td>{{ $del->po_id }}</td>
                                                        <td>{{ $del->delivery_id }}</td>

                                                        <td>₱{{ number_format($runningBalance, 2) }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($del->due_date)->format('F j, Y') }}</td>
                                                        <td>{{ $del->payment_status }}</td>
                                                        <td>
                                                            <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                                <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                    <span class="material-symbols-outlined">
                                                                    expand_circle_down
                                                                    </span>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $del->po_id]) }}"> <span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('pym.collection', ['po_id' => $del->po_id]) }}">
                                                                            <span class="material-symbols-outlined">arrow_outward</span>
                                                                            Payments collection
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
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
                                            @if (!empty($payments))
                                                @foreach ($payments as $receipt)
                                                    {{-- payment-view-modal --}}
                                                    <div class="modal fade" id="view-payment-modal-{{ $receipt->payment_id ?? NULL }}" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true"> 
                                                        <div class="modal-dialog">
                                                            <div class="modal-content" >
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <p class="modal-title" id="requestActionLabel">Receipt #{{  $receipt->payment_id ?? NULL  }}</p>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" receipt-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body" style="height: 600px; overflow: auto; display: flex; flex-direction: column;">
                                                                    <p class="note-notify">
                                                                        <span class="material-symbols-outlined"> info </span>
                                                                        <span> Make sure image selected is 2MB or less, scanned image is recommended.</span>
                                                                    </p>
                                                                    <div class="modal-option-groups">
                                                                        @if ($receipt->payment !== NULL)
                                                                            <p style="display:flex; flex-direction: row; justify-content: space-between;">
                                                                                <span style="font-size: 13px">{{ $receipt->payment->status }}</span>     
                                                                                <span style="font-size: 13px; color: #666;">Updated at {{ \Carbon\Carbon::parse($receipt->payment->action_at)->format('F j, Y') }}</span>
                                                                            </p>
                                                                            @php
                                                                                $imgSrc = $receipt->payment && $receipt->payment->image
                                                                                    ? 'data:' . $receipt->payment->image_mime_type . ';base64,' . base64_encode($receipt->payment->image)
                                                                                    : asset('assets/default-company-logo.png');
                                                                            @endphp
                                                                            <img src="{{ $imgSrc }}" alt="Receipt image" style="height: 70%; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; border-radius: 5px">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                    
                                                                <div class="modal-footer" style="display: flex; flex-direction: row;">
                                                                
                                                                    <button class="collection-btn" style="border-radius: 5px; background-color: #888"
                                                                        onclick="window.location.href='{{ route('crd.download', ['payment_id' => $receipt->payment_id]) }}'">
                                                                        <span class="material-symbols-outlined" style="width:auto">
                                                                        download
                                                                        </span>
                                                                        Download image
                                                                    </button> 
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                                
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($receipt->updated_at)->format("F j, Y, g:i a") }}</td>
                                                        <td>{{$receipt->po_id ?? NULL}}</td>
                                                        <td>₱{{ number_format($receipt->total_amount,2) }}</td>
                                                        <td>{{$receipt->status}}</td>
                                                        <td>
                                                            <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                                <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                                    <span class="material-symbols-outlined">
                                                                    expand_circle_down
                                                                    </span>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="{{ route('pr.request', ['po_id' => $receipt->po_id ?? NULL]) }}"  style="color:#f8a01d" ><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('pym.collection', ['po_id' => $receipt->po_id ?? NULL]) }}">
                                                                            <span class="material-symbols-outlined">arrow_outward</span>
                                                                            Payments collection
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view-payment-modal-{{ $receipt->payment_id ?? NULL }}">
                                                                            <span class="material-symbols-outlined">capture</span>
                                                                            View receipt
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>                                                 
                                                    </tr>
                                                @endforeach
                                            
                                            @endif                                

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