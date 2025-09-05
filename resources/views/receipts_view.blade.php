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
        <span style="display: flex; width: 100%; justify-content: space-between;"><h2>Receipt #{{ $receipt->receipt_number }}</h2> <p>{{ $receipt->created_at -> format ('F j, Y, g: i A') }}</p></span>

        <div class="mainBlock">
                <div class="receiptBlock" style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; ">
                        <tr><th>Invoice#:</th><td>{{ $receipt->invoice_number }}</td></tr>
                        <tr><th>Store:</th><td style="font-size: 20px; font-weight: bold;">{{ $receipt->customer ? $receipt->customer->store_name : 'N/A' }}</td></tr>
                        <tr><th>Representative:</th><td>{{ $receipt->customer ? $receipt->customer->name : 'N/A' }}</td></tr>
                        <tr><th>Amount:</th>  <td style="color: green">₱{{ number_format($receipt->total_amount, 2) }}</td></tr>
                        </td></tr>
                                                                    

                        <tr><th>Purchase Date:</th><td>{{ $receipt->purchase_date ? \Carbon\Carbon::parse($receipt->purchase_date)->format('F j, Y, g:i A') : 'N/A' }}</td></tr>
                        <tr>
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
                            @if($receipt->verified_by !== NULL)
                                <tr><th>Action By:</th><td>{{ $receipt->verified_by ?? 'N/A' }}</td></tr>
                                <tr><th>Action At:</th><td>    {{ $receipt->verified_at ? \Carbon\Carbon::parse($receipt->verified_at)->format('F j, Y, g:i A') : 'N/A' }}</td></tr>
                            @endif

                   </table>


                    <div style="display: flex; flex-direction: column; width: 100%;">
                        <p style="font-size: 16px; font-weight: bold; margin: 0; margin-top: 10px;">Orders' note</p>
                        <div class="notes-display">{{ $receipt->notes }}</div>

                        @if(auth()->user()->user_type === 'Staff' || auth()->user()->user_type === 'Admin')
                        <div class="actionBtn" >
                            @if($receipt->status === 'Verified')

                            @elseif($receipt->status === 'Cancelled')


                            @elseif($receipt->status=== 'Pending')
                                <form action="{{ url('/receipts/verify/' . $receipt->receipt_id) }}" method="POST" class="action-form">
                                    @csrf
                                    <button type="button" class="verifyButton open-confirm" data-action="verify">Verify</button>
                                </form>

                                <form action="{{ url('/receipts/cancel/' . $receipt->receipt_id) }}" method="POST" class="action-form" style="display:inline-block;">
                                    @csrf
                                    <button type="button" class="cancelAction open-confirm" data-action="cancel">Cancel</button>
                                </form>

                                <form action="{{ url('/receipts/reject/' . $receipt->receipt_id) }}" method="POST" class="action-form" style="display:inline-block;">
                                    @csrf
                                    <button type="button" class="rejectAction open-confirm" data-action="reject">Reject</button>
                                </form>
    
                            @endif

                        </div>
                        @endif

                    </div>


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


</body>
</html>

@endsection
