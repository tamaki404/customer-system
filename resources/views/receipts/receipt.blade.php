
@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">



@endpush



@section('content')


    {{-- purchase order placing --}}
    @if(Auth()->user()->role !== "Supplier" && $receipt->status === 'Pending')
        <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
            <div class="modal-dialog" style="width: auto">
                <form class="modal-content" method="POST" enctype="multipart/form-data"
                    style="width: 800px"
                    action="{{ route('receipts.action', $receipt->receipt_id) }}">
                    @csrf

                    <div class="modal-header">
                        <p class="modal-title">Receipt Actions</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Verified receipts will deduct from the total amount of the tagged order.</span>
                        </p>

                        <p>
                            <strong>Receipt ID: </strong>
                            <span>{{ $receipt->receipt_id }}</span>
                        </p>
                        <p>
                            <strong>Order ID: </strong>
                            <a href="{{ route('orders.order', $receipt->order_id) }}"  target="_blank">
                                {{ $receipt->order_id }}
                            </a>
                        </p>

                        <div class="mt-2">
                            <label class="form-label">Set status for this receipt <span class="text-danger">*</span></label>
                            <select name="status" id="statusSelect"  required>
                                <option value="">-- Select status --</option>
                                <option value="Verified">Verify receipt</option>
                                <option value="Rejected">Reject receipt</option>
                            </select>
                        </div>

                        {{-- Verified Section --}}
                        <div id="verifiedSection" style="display: none; border-radius: 5px;">
                            @if($remainingAmount <= 0)
                                <div class="alert alert-info mt-3">
                                    <strong>Notice:</strong> This order has been fully paid. No additional payment can be added.
                                </div>
                            @endif

                            <table class="table table-bordered mt-3" style="width:100%;  border-radius: 5px;;">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <td>Order Total</td>
                                        <td>Already Paid</td>
                                        <td>Remaining</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-end">₱{{ number_format($receipt->order->total_amount, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($totalPaid, 2) }}</td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($remainingAmount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="form-group mt-2">
                                <label class="form-label">Amount Paying <span class="text-danger">*</span></label>
                                <input type="number" 
                                    name="amount" 
                                    id="amountInput"
                                    step="0.01"
                                    min="0.01"
                                    max="{{ $remainingAmount }}"
                                    placeholder="Enter amount">
                                <small class="text-muted">
                                    Maximum remaining balance: ₱{{ number_format($remainingAmount, 2) }}
                                    @if($totalPaid > 0)
                                        <br><span class="text-success">✓ Previous payments: ₱{{ number_format($totalPaid, 2) }}</span>
                                    @endif
                                </small>
                            </div>
                            

                            <div class="form-group mt-2">
                                <label class="form-label">Remarks (Optional)</label>
                                <input type="text" 
                                    name="remarks" 
                                    id="remarksInput"
                                    maxlength="200" 
                                    placeholder="Add any additional notes">
                            </div>
                        </div>

                        {{-- Rejected Section --}}
                        <div id="rejectedSection" style="display: none;">
                            <div class="alert alert-warning mt-3">
                                <strong>Warning:</strong> Rejecting this receipt will mark it as invalid. Please provide a clear reason.
                            </div>

                            <div class="form-group mt-2">
                                <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="reason" 
                                        id="reasonInput"
                                        rows="3"
                                        maxlength="200" 
                                        placeholder="Explain why this receipt is being rejected"></textarea>
                                <small class="text-muted">Required when rejecting a receipt</small>
                            </div>
                        </div>

                        <input type="hidden" name="order_id" value="{{ $receipt->order_id }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary" disabled>Confirm Action</button>
                    </div>

                    <input type="hidden" id="remainingAmount" value="{{ $remainingAmount }}">

                </form>
            </div>
        </div>
    @endif

                    <!-- error / success alerts -->
                    @if ($errors->any())
                        <div class="alert alert-danger m-2">
                            <h6><strong>Validation Errors:</strong></h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size: 14px;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success m-2">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger m-2">
                            <h6><strong>Error:</strong></h6>
                            <p class="mb-0" style="font-size: 14px;">{{ session('error') }}</p>
                        </div>
                    @endif

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('receipts.list') }}">< Receipts list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Receipt</p>
                @if(Auth()->user()->role !== "Supplier" && $receipt->status === 'Pending')
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">File an action</button>
                    </div>
                @endif
            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="purchase-order-div" style="display: flex; flex-direction: row; gap: 20px">
                <div class="po-image-div">
                    @php
                        $imgSrc = $receipt->image 
                            ? ('data:' . $receipt->image_mime_type . ';base64,' . base64_encode($receipt->image))
                            : asset('assets/default-image.jpg');
                    @endphp
                    <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image"> 
                </div>


            </div>

       
        </div>


   </div>

@endsection



@push('scripts')
    <script src="{{ asset('js/receipts/receipt-action.js') }}"></script>
<script src="{{ asset('js/receipt-actions.js') }}"></script>




@endpush