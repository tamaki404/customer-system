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
        <span style="display: flex; flex-direction: row; width: 100%; justify-content: space-between;"><h2>Receipt #{{ $receipt->invoice_number }}</h2> <p>{{ $receipt->created_at -> format ('F j, Y, g: i A') }}</p></span>

        <div class="mainBlock">

                <div class="receiptBlock">

                    <table style="width:100%; border-collapse:collapse; ">
                        <p class="receipt_num">Invoice {{ $receipt->invoice_number }}</p>

                        <tr><th>Store:</th><td style="font-size: 20px; font-weight: bold;">{{ $receipt->customer ? $receipt->customer->store_name : 'N/A' }}</td></tr>
                        <tr><th>Customer:</th><td>{{ $receipt->customer ? $receipt->customer->name : 'N/A' }}</td></tr>
                    @if(auth()->user()->user_type === 'Staff' && auth()->user()->user_type === 'Admin')
                        <tr><th>Representative:</th><td>{{ $receipt->name }}</td></tr>
                    @endif
                        <tr><th>Amount:</th>  <td id="receiptAmount" style="font-size: 18px; font-weight: bold; color: green;" data-amount="{{ $receipt->total_amount }}">
                        {{ $receipt->total_amount }}
                        </td></tr>
                        <tr><th>Purchase Date:</th><td>{{ $receipt->purchase_date }}</td></tr>
                        <tr><th>Status:</th><td>{{ $receipt->status }}</td></tr>

                        

                    @if($receipt->verified_by !== NULL)
                        <tr><th>Action By:</th><td>{{ $receipt->verified_by ?? 'N/A' }}</td></tr>
                        <tr><th></th>Action At:</th><td>{{ $receipt->verified_at ?? 'N/A' }}</td></tr>
                    @endif
                   
                    
                   </table>

                    @if(auth()->user()->user_type === 'Staff' && auth()->user()->user_type === 'Admin')

                        <form action="{{ url('/receipts/verify/' . $receipt->receipt_id) }}" method="POST" style="display:inline-block;margin-right:10px;">
                            @csrf
                            <button type="submit" style="background:#1976d2;color:#fff;padding:10px 24px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Verify</button>
                        </form>
                        <form action="{{ url('/receipts/cancel/' . $receipt->receipt_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" style="background:#d32f2f;color:#fff;padding:10px 24px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Cancel</button>
                        </form>
                        <form action="{{ url('/receipts/reject/' . $receipt->receipt_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" style="background:#d32f2f;color:#fff;padding:10px 24px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Reject</button>
                        </form>                        <a href="{{ url('/receipts') }}" style="display:inline-block;margin-top:20px;">&larr; Back to Receipts</a> 
                    @endif

                    <div style="display: flex; flex-direction: column; width: 100%;">
                    <p style="font-size: 18px; font-weight: bold;">Notes:</p>
                    <div class="notes-display">{{ $receipt->notes }}</div>
                </div>


                </div>

                
                <div class="imageBlock">
                        @if($receipt->receipt_image)
                        <p>Receipt Image</p>
                        
                            <a href="{{ url('/receipt_image/' . $receipt->receipt_id) }}" target="_blank">
                                <img 
                                    src="{{ asset('images/' . $receipt->receipt_image) }}" 
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


{{-- <script>
function formatPeso(amount) {
    const absAmount = Math.abs(amount);
    let formatted;

    if (absAmount >= 1_000_000_000) {
        formatted = '₱' + (amount / 1_000_000_000).toFixed(2) + 'B';
    } else if (absAmount >= 1_000_000) {
        formatted = '₱' + (amount / 1_000_000).toFixed(2) + 'M';
    } else if (absAmount >= 1_000) {
        formatted = '₱' + (amount / 1_000).toFixed(2) + 'K';
    } else {
        formatted = '₱' + Number(amount).toFixed(2);
    }

    return formatted;
}

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('receiptAmount');
    if (el) {
        const amount = parseFloat(el.dataset.amount);
        el.innerText = formatPeso(amount);
    }
});
</script> --}}

</body>
</html>

@endsection
