@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipts_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/confirmation-modal/receipts_view.css') }}">
    <title>Receipt Details</title>
</head>
<body>
    <script src="{{ asset('js/fadein.js') }}"></script>

    <!-- confirmation modal -->
    <div class="modal fade" id="confirmModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px;">
                    Are you sure you want to commit changes?
                </div>

                <div class="modal-footer" style="padding: 5px">
                    <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    {{-- file an action modal --}}
    <div class="modal" id="fileActionModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0; font-size: 15px; font-weight: bold;">File an action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px; gap: 6px">
              <form action="{{ route('receipts.receipt_status', $receipt->po_number) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <label for="status">Do you want to accept this receipt?</label>
                        <select name="status" id="status" required onchange="toggleRejectInput()">
                            <option value="">-- Select Action --</option>
                            <option value="Verified">Yes - Verify Receipt</option>
                            <option value="Rejected" style="color: red; font-weight: bold;">No - Reject Receipt</option>
                        </select>
                    </div>

                    <div class="form-row" id="messAddInput">
                        <label for="payment-notes">Any notes to add? (optional)</label>
                        <p>*Write your message briefly yet precisely below</p>
                        <input type="text" name="additional_note" id="payment-notes" placeholder="" maxlength="255">
                    </div>

                    <div class="form-row" id="rejectPaymentInput" style="display: none;">
                        <label for="reject-details">Is there any problem with the receipt image attached?</label>
                        <p>*Kindly specify below the error you noticed</p>
                        <input 
                            type="text"
                            name="rejected_note"
                            id="reject-details"
                            minlength="3"
                            maxlength="255"
                            placeholder="e.g., blurry, missing, wrong file, mismatched details"
                        >
                    </div>

                    <input type="hidden" name="action_by" value="{{ auth()->id() }}">
                    <input type="text" name="receipt_id" value="{{ $receipt->receipt_id }}">


                    <div class="modal-footer" style="padding: 5px; margin-top: 5px;">
                        <button type="submit" id="confirmFileBtn" class="btn" style="background: #ffde59; font-size: 14px;">
                            <span id="submitBtnText">Confirm Action</span>
                        </button>
                        <button type="button" id="cancelBtn" class="btn btn-secondary" style="font-size: 14px;" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
                </div>


            </div>
        </div>
    </div>


    <div class="receiptFrame">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('error') }}
            </div>
        @endif

        <a class="go-back-a" href="/receipts"><- Receipts</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: 80px;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>
        
        <span style="display: flex; width: 100%; justify-content: space-between;"><h2>Receipt #{{ $receipt->receipt_number }} <span></span> </h2> <p>{{ $receipt->created_at -> format ('F j, Y, g: i A') }}</p></span>

        <div class="mainBlock">

                <div class="receiptBlock" style="overflow-x:auto;">
                    @if(auth()->user()->user_type !== 'Customer')
                        <button class="open-modify-modal" style="">File an action</button>
                    @elseif(auth()->user()->user_type === 'Customer')
                        @if ($receipt->status === 'Pending')
                            <form id="cancel-status-form" action="{{ route('receipt.cancel', $receipt->receipt_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="button" class="cancel-receipt open-confirm">
                                    Cancel
                                </button>
                            </form>
                        @endif

                    @endif
                   <div class="payment-notes">
                        @if($receipt->purchaseOrder->payment_status==="Rejected")

                            <div class="rejected-note">
                                <p class="head-note" style="display: flex; flex-direction: row; justify-content: space-between; margin: 0;">
                                    <span class="payment-title" style="font-weight: bold; color: #333; font-size: 15px;">Your receipt has been rejected</span>
                                    <span>{{\Carbon\Carbon::parse ($receipt->purchaseOrder->payment_at) ->format('F j, Y, g:i A')}}</span>
                                </p>
                                @if ($receipt->purchaseOrder->payment_reject_details > 0)
                                    <p style="margin: 5px; font-size: 14px;">Note: {{$receipt->purchaseOrder->payment_reject_details}}</p>
                                @endif
                            </div>
                        @elseif($receipt->purchaseOrder->payment_status==="Paid")

                            <div class="paid-note">
                                <p class="head-note" style="display: flex; flex-direction: row; justify-content: space-between; margin: 0;">
                                    <span class="payment-title" style="font-weight: bold; color: #333; font-size: 15px;">Full payment received</span>
                                    <span>{{\Carbon\Carbon::parse ($receipt->purchaseOrder->payment_at) ->format('F j, Y, g:i A')}}</span>
                                </p>
                                @if ($receipt->purchaseOrder->payment_notes > 0)
                                    <p style="margin: 5px; font-size: 14px;">Note: {{$receipt->purchaseOrder->payment_notes}}</p>
                                @endif
                            </div>


                        @elseif($receipt->purchaseOrder->payment_status==="Partially")
                            <div class="partial-note">
                                <p class="head-note" style="display: flex; flex-direction: row; justify-content: space-between; margin: 0;">
                                    <span class="payment-title" style="font-weight: bold; color: #333; font-size: 15px;">Partial payment received</span>
                                    <span style="font-size: 14px">{{\Carbon\Carbon::parse ($receipt->purchaseOrder->payment_at) ->format('F j, Y, g:i A')}}</span>
                                </p>
                                @if ($receipt->purchaseOrder->payment_notes > 0)
                                    <p style="margin: 5px; font-size: 14px;">Note: {{$receipt->purchaseOrder->payment_notes}}</p>
                                @endif
                            </div>

                        @endif

                   </div>
                    <table style="width:100%; border-collapse:collapse; ">


                        <th>Status:</th>
                                <td style="color:
                                    @if($receipt->status === 'Verified') green
                                    @elseif($receipt->status === 'Pending') #333
                                    @elseif($receipt->status === 'Cancelled') orange
                                    @elseif($receipt->status === 'Rejected') red
                                    @else #333
                                    @endif
                                ;">{{ $receipt->status }}</td>
                        </tr>
                        <tr><th>Invoice#:</th><td>{{ $receipt->invoice_number }}</td></tr>
                        <tr><th>Store:</th><td style="font-size: 20px; font-weight: bold;">{{ $receipt->customer ? $receipt->customer->store_name : 'N/A' }}</td></tr>
                        <tr><th>Representative:</th><td>{{ $receipt->customer ? $receipt->customer->name : 'N/A' }}</td></tr>
                        <tr><th>Amount:</th>  <td style="color: green">₱{{ number_format($receipt->total_amount, 2) }}</td></tr>
                        </td></tr>
                                                                    

                        <tr><th>Purchase date:</th><td>{{ $receipt->purchase_date ? \Carbon\Carbon::parse($receipt->purchase_date)->format('F j, Y, g:i A') : 'N/A' }}</td></tr>

                        <tr><th>PO  number</th><td>{{ $receipt->po_number}}</td></tr>
                        <tr><th>Payment status</th><td>{{ $receipt->purchaseOrder->payment_status}}</td></tr>
                        @if ($receipt->purchaseOrder->payment_status === 'Partially Settled')
                            <tr>
                                <th>

                                </th>
                                <td>

                                </td>
                            </tr>
                        @elseif ($receipt->purchaseOrder->payment_status === 'Fully Paid' && !empty($receipt->additional_note))


                        @elseif ($receipt->purchaseOrder->payment_status === 'Rejected' && !empty($receipt->additional_note))


                        @endif


                   </table>





                    @if ($receipt->notes > 0)
                        <div style="display: flex; flex-direction: column; width: 100%;">
                            <p style="font-size: 16px; font-weight: bold; margin: 0; margin-top: 10px;">Orders' note</p>
                            <div class="notes-display">{{ $receipt->notes }}</div>                        
                        </div>
                    @endif


                </div>

                
                <div class="imageBlock">
                        @if($receipt->receipt_image)
                        <p>Receipt Image</p>
                        
                        @php
                            $isBase64 = !empty($receipt->receipt_image_mime);
                            $dataUri = $isBase64 ? ('data:' . $receipt->receipt_image_mime . ';base64,' . $receipt->receipt_image) : null;
                        @endphp
                        <a class="receipt-image" href="{{ url('/receipt_image/' . $receipt->receipt_id) }}" target="_blank">
                            <img 
                                src="{{ $dataUri ? $dataUri : asset('images/' . $receipt->receipt_image) }}" 
                                alt="Receipt Image" 
                                style="cursor: pointer;"
                            >
                        </a>
                        @else
                            N/A
                        @endif 
                </div>
        </div>

    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/confirmation-modal/receipts_view.js') }}"></script>
<script src="{{ asset('js/modal/file_action.js') }}"></script>
<script src="{{ asset('js/receipts_view/file_an_action.js') }}"></script>


</body>
</html>

@endsection
