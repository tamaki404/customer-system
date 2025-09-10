
@foreach ( $receipts as $receipt )
    @if ($receipt->purchaseOrder->status !== 'Unpaid' ||$receipt->purchaseOrder->status !== 'Procesing')
        <div class="receipt-block">
            <p>{{ $loop->iteration }}</p>
            <p>Status: {{$receipt->status}}</p>
            <p>Receipt generated on {{\Carbon\Carbon::parse ($receipt->created_at) ->format('F j, Y, g:i A')}} </p>
            <p>Receipt processed on {{\Carbon\Carbon::parse ($receipt->payment_at) ->format('F j, Y, g:i A')}} </p>
            <p>amount paid: ₱{{ number_format($receipt->total_amount, 2) }}</p>
        </div>
    @endif

    <br>
@endforeach