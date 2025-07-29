@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipts_view.css') }}">
    <title>Receipt Details</title>
</head>
<body>
    <div class="receiptFrame">
        <h2>Invoice</h2>

        <div class="mainBlock">

                <div class="receiptBlock">

                    <table style="width:100%; border-collapse:collapse; ">
                        <p class="receipt_num">Receipt #{{ $receipt->receipt_number }}</p>
                        <p class="receipt_num">Invoice {{ $receipt->invoice_number }}</p>

                        <p class="date">{{ $receipt->created_at -> format ('F j, Y, g: i A') }}</p>

                        <tr><th>Store:</th><td>{{ $receipt->customer ? $receipt->customer->store_name : 'N/A' }}</td></tr>
                        <tr><th>Representative:</th><td>{{ $receipt->name }}</td></tr>
                        <tr><th>Amount:</th>  <td id="receiptAmount" data-amount="{{ $receipt->total_amount }}">
                        {{ $receipt->total_amount }}
                        </td></tr>
                        <tr><th>Purchase Date:</th><td>{{ $receipt->purchase_date }}</td></tr>
                        <tr><th>Status:</th><td>{{ $receipt->status }}</td></tr>
                        <tr><th>Notes:</th><td>{{ $receipt->notes }}</td></tr>
                        <tr><th>Action By:</th><td>{{ $receipt->verified_by ?? 'N/A' }}</td></tr>
                        <tr><th></th>Action At:</th><td>{{ $receipt->verified_at ?? 'N/A' }}</td></tr>
                   
                    
                   </table>

                    @if(auth()->user()->user_type === 'Customer')

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


                </div>

                
                <div class="imageBlock">
                        @if($receipt->receipt_image)
                        <p>Receipt Image</p>
                        
                        <img onclick="window.location='{{ url('/receipt_image/' . $receipt->receipt_id) }}'" style="cursor: pointer;" src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image">
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
