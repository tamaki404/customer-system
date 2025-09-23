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

        <div class="modal fade" id="add-receipt-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content"  method="POST" action="{{ route('receipt.create') }}"  enctype="multipart/form-data">
                    @csrf
            
                    @if (session('success'))
                        <div class="alert alert-success" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Success:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Error:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                        </div>
                    @endif
                
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
                            <input type="hidden" name="supplier_id" value="{{ auth()->user()->supplier->supplier_id }}">

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

                <div class="content-body" style="padding: 10px; border: none; height: auto;">

                <div>
                        <p>Total Credit Limit: {{ number_format($credit->credit_limit, 2) }}</p>
                        <p>Used Credit: {{ number_format($usedCredit, 2) }}</p>
                        <p>Available Credit: {{ number_format($availableCredit, 2) }}</p>


                </div>


                
                            <div class="table-body">
                                <p style="margin: 5px; font-weight: bold;">Transaction history</p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Order ID</th>
                                                <th>Label</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($transactionHistory as $transaction)
                                                <tr >
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $transaction->action_at }}</td>
                                                    <td>{{ $transaction->order_id }}</td>
                                                    <td>{{ $transaction->label }}</td>
                                                    <td>{{ $transaction->status }}</td>
                                                    @if ($transaction->label === 'Receipt')
                                                       <td>+{{ $transaction->amount }}</td>
                                                    @elseif ($transaction->label === 'Order')
                                                        <td>-{{ $transaction->amount }}</td>
                                                    @endif
                                                  
                                         
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>

                        
                            </div>

                            <div class="table-body" style="margin-top: 10px">
                                <p style="margin: 5px; font-weight: bold;">Outstanding Payments</p>
                                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                                        <thead style="background-color: #fff;">
                                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Order ID</th>
                                                <th>Description</th>
                                                <th>Running Balance</th>
                                                <th>Status</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                            @foreach ($oustandingPayments as $oustandingPayment)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{ $oustandingPayment->order_date }}</td>
                                                    <td>{{ $oustandingPayment->order_id }}</td>
                                                    <td>
                                                        @foreach ($oustandingPayment->items as $item)
                                                            x{{$item->quantity}} {{ $item->product->name }},
                                                        @endforeach
                                                    </td>
                                                    <td><strong>{{ number_format($oustandingPayment->outstanding_balance, 2) }}</strong></td>
                                                    <td>{{ $oustandingPayment->status }}</td>
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

    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/file-preview.js') }}"></script>
@endpush