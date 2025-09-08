@foreach ( $receipts as $receipt )
    <div class="receipt-block">
        <p>Receipt generated on {{$receipt->created_at}}</p>
        <p>Receipt processed on {{$receipt->->payment_at}}</p>
    </div>
@endforeach