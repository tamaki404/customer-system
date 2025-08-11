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

    <title>Receipt Details</title>
</head>
<body>
    <script src="{{ asset('js/fadein.js') }}"></script>
    <div class="receiptFrame">
        <span style="display: flex; flex-direction: row; width: 100%; justify-content: space-between;"><h2>Receipt #{{ $receipt->receipt_number }}</h2> <p>{{ $receipt->created_at -> format ('F j, Y, g: i A') }}</p></span>

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
                        @if($receipt->status !== 'Verified')
                        <form action="{{ url('/receipts/verify/' . $receipt->receipt_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="verifyButton">Verify</button>
                        </form>
                        @endif
                        <form  action="{{ url('/receipts/cancel/' . $receipt->receipt_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="cancelAction" type="submit">Cancel</button>
                        </form>
                        <form action="{{ url('/receipts/reject/' . $receipt->receipt_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="rejectAction" type="submit">Reject</button>
                        </form>     
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
                        <a href="{{ url('/receipt_image/' . $receipt->receipt_id) }}" target="_blank">
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


<script>
function formatPeso(value) {
    value = Number(value);
    if (value >= 1_000_000_000) return `₱${(value / 1_000_000_000).toFixed(1)}B`;
    if (value >= 1_000_000)     return `₱${(value / 1_000_000).toFixed(1)}M`;
    if (value >= 1_000)         return `₱${(value / 1_000).toFixed(1)}K`;
    return `₱${value.toLocaleString()}`;
}

document.addEventListener('DOMContentLoaded', () => {
    const amountEl = document.getElementById('receiptAmount');
    const rawAmount = amountEl.dataset.amount;
    amountEl.textContent = formatPeso(rawAmount);
});
</script>


</body>
</html>

@endsection
