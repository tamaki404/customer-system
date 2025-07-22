@extends('layout')

@section('content')
<div class="receiptFrame" style="max-width:600px;margin:40px auto;padding:30px;background:#fff;border-radius:8px;box-shadow:0 2px 8px #0001;">
    <h2>Receipt Details</h2>
    <table style="width:100%;border-collapse:collapse;">
        <tr><th style="text-align:left;width:40%">Receipt #:</th><td>{{ $receipt->receipt_number }}</td></tr>
        <tr><th>Customer:</th><td>{{ $receipt->customer ? $receipt->customer->username : 'N/A' }}</td></tr>
        <tr><th>Store Name:</th><td>{{ $receipt->store_name }}</td></tr>
        <tr><th>Amount:</th><td>{{ $receipt->total_amount }}</td></tr>
        <tr><th>Date:</th><td>{{ $receipt->purchase_date }}</td></tr>
        <tr><th>Status:</th><td>{{ $receipt->status }}</td></tr>
        <tr><th>Notes:</th><td>{{ $receipt->notes }}</td></tr>
        <tr><th>Verified By:</th><td>{{ $receipt->verified_by ?? 'N/A' }}</td></tr>
        <tr><th>Verified At:</th><td>{{ $receipt->verified_at ?? 'N/A' }}</td></tr>
        <tr>
            <th>Image:</th>
            <td>
                @if($receipt->receipt_image)
                    <img src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image" style="max-width:200px;max-height:200px;">
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>
    <a href="{{ url('/receipts') }}" style="display:inline-block;margin-top:20px;">&larr; Back to Receipts</a>
</div>
@endsection