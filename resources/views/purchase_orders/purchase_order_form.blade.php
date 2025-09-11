<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/purchase-order-form.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <title>Purchase Order</title>
</head>
<body style="display: flex; flex-direction: column; flex-wrap: wrap;">
{{--<a href="{{ route('purchase_order.pdf', $order->po_number) }}" 
        class="btn btn-primary" target="_blank">
        Download PDF
    </a> --}}

    <a class="go-back-a" href="{{ route('purchase_order.view', $order->po_number) }}">
         <- Order details
    </a>

    <div class="form">
        <div class="header-1">
            <h2>Purchase Order</h2> 
            <div class="header">
                <div class="sunny-info">
                    
                    <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="">
                    {{-- <img src="{{ public_path('assets/sunnyLogo1.png') }}" style="width:120px;"> --}}

                    <div class="address">
                        <p><strong>Sunny & Scramble</strong></p>
                        <p>123 Sunny Street</p>
                        <p>Sunnyville, 1860</p>
                    </div>
                </div>
                <div class="order-info">
                    <p><strong>Purchase ID:</strong> {{$order->po_number}}</p>
                    <p><strong>Date:</strong> {{$order->created_at->format('d F Y h:i A')}}</p>
                    <p><strong>Status:</strong> {{$order->status}}</p>
                </div>
            </div>
        </div>
        <div class="form-body" style="padding: 10px">
            <div class="shipping-info">
              
                <div class="customer">
                    <h3>Customer Information</h3>
                    <p>{{$order->company_name}}</p>
                    <p style="white-space:wrap;">
                        {{ $order->street }},
                        {{ $order->barangay['barangay_name'] }},
                        {{ $order->municipality['municipality_name'] }},
                        {{ $order->province['province_name'] }},
                        {{ $order->region['region_name'] }}
                    </p>
                    <p><strong>Contact Person:</strong> {{$order->receiver_name}}</p>
                    <p><strong>Phone:</strong> {{$order->receiver_mobile}}</p>
                </div>

            </div>

            <div class="shipping-type">
                <h3 style="margin-top: 10px">Shipping Type</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Requisitioner</th>
                            <th>Ship via</th>
                            <th>F.O.B</th>
                            <th>Shipping terms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$order->receiver_name}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="items">
                <h3 style="margin-top: 10px">Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product no.</th>
                            <th>Product Name</th>
                            <th>Unit</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordersItem as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$item->product->product_id}}</td>
                            <td>{{$item->product->name}}</td>
                            <td>{{$item->product->unit}}</td>
                            <td>&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td>&#8369;{{ number_format($item->total_price, 2) }}</td>
                        
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>


            </div>
            <div class="totals">

                

                <table style="width: 300px; margin-left: auto; border-collapse: collapse; font-size: 14px;">
              
              
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc; font-weight: bold;">Total</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right; font-weight: bold;">&#8369;{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="notes-signatory">
                @if ($order->order_notes > 0)
                    <div class="notes">
                        <h3>Remarks</h3>
                        <p type="text" name="notes" style="white-space: wrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px; ">{{$order->order_notes}}</p>
                    </div>
                @endif

                <div class="signatory">
                    <h3 style="margin: 0">Authorized by</h3>
                    <p type="text" style="font-size: 13px; margin-top: 10px;" name="authorized_by">{{$order->receiver_name}}</p></p>
                    <p style="font-size: 12px; color: #666;">Name with signature of Authorized person</p>
                </div>
            </div>

        </div>


    </div>


    <a class="download-purchase-order"  onclick="window.print()">
        <span class="material-symbols-outlined">download</span>
         Download PDF
    </a>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentDate = new Date().toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long', 
                day: 'numeric'
            });
            
        });
    </script>
</body>
</html>
