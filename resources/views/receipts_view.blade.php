@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt Details</title>
</head>
<body>
    <div class="receiptFrame">
        <h2>Receipt Details</h2>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th colspan="2" style="text-align:left;">Date:</th><td>{{ $receipt->created_at }}</td></tr>
            <tr><th style="text-align:left;width:40%">Receipt #:</th><td>{{ $receipt->receipt_number }}</td></tr>
            <tr><th>Customer:</th><td>{{ $receipt->customer ? $receipt->customer->username : 'N/A' }}</td></tr>
            <tr><th>Store Name:</th><td>{{ $receipt->store_name }}</td></tr>
            <tr><th>Amount:</th><td>{{ $receipt->total_amount }}</td></tr>
            <tr><th>Purchase Date:</th><td>{{ $receipt->purchase_date }}</td></tr>
            <tr><th>Status:</th><td>{{ $receipt->status }}</td></tr>
            <tr><th>Notes:</th><td>{{ $receipt->notes }}</td></tr>
            <tr><th>Verified By:</th><td>{{ $receipt->verified_by ?? 'N/A' }}</td></tr>
            <tr><th>Verified At:</th><td>{{ $receipt->verified_at ?? 'N/A' }}</td></tr>
            <tr>
                <th>Image:</th>
                <td>
                    <tr onclick="window.location='{{ url('/receipt_image/' . $receipt->receipt_id) }}'" style="cursor: pointer;">
                        <td>
                            @if($receipt->receipt_image)
                                <img src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image" style="max-width:200px;max-height:200px;">
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </td>
            </tr>
        </table>
        <a href="{{ url('/receipts') }}" style="display:inline-block;margin-top:20px;">&larr; Back to Receipts</a>
    </div>
</body>
</html>
@endsection