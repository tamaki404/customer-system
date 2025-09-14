
<div clas="body"> 
        <a class="go-back-a" href="/purchase_order_view/{{$receipt->po_id}}"><- Purchase Order</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: auto;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>   
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
</div>



<style>
.body{
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    height: 100%;
    overflow: auto;
}
.receipt-block{
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.receipt-block p{
    margin: 5px 0;
}
</style>