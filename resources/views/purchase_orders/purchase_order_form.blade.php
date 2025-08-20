<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/purchase-order-form.css') }}">
    <title>Purchase Order</title>
</head>
<body>
    {{-- <a href="{{ route('purchase_order.pdf', $order->po_number) }}" 
   class="btn btn-primary" target="_blank">
   Download PDF
</a> --}}
<style>
            body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
            color: #333;
            overflow-x: auto;
            display: flex;
        }
        .form {
            background: white;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: auto;
            height: auto;
            display: flex;
            flex-direction: column;
        }
        .header-1{
            background-color: #ffde59;
            padding: 15px;

        }
        h2 {
            text-align: right;
            margin-bottom: 20px;
            color: #333;
            font-weight: normal;
            font-size: 30px;
        }
        .header, .shipping-info, .shipping-type, .items, .notes-signatory {
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .sunny-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .sunny-info img {
            max-height: 60px;
        }
        .address p {
            margin: 2px 0;
            font-size: 14px;
        }
        .order-info p {
            margin: 4px 0;
            font-size: 14px;
        }
        .shipping-info {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .shipping-info div {
            padding: 10px;
            border-radius: 6px;
            flex: 1;
            border-top: #ffde59 4px solid;
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
        }
        .shipping-info div h3 {
            margin-top: 0;
            font-size: 16px;
            font-weight: normal;
            color: #333;
            font-weight: bold;


        }
   
        /* .shipping-info div h3{
            background-color: #ffde59;
            padding: 5px;
        } */

        .vendor p{
            font-size: 14px;
        }
        .customer p{
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table, th, td {
            border: 1px solid #ccc;
            
        }
        th {
            background: #ffde59;
            text-align: left;
            padding: 8px;
            font-size: 15px;
        }
        td {
            padding: 8px;
            font-size: 14px;
        }
        .totals {
            text-align: right;
            margin-top: 15px;
            font-size: 14px;
        }
        .totals p {
            margin: 4px 0;
        }
        .notes-signatory {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .notes, .signatory {
            flex: 1;
        }
        input[type="text"] {
            width: 100%;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
</style>

    <div class="form">
        <div class="header-1">
            <h2>Purchase Order</h2> 
            <div class="header">
                <div class="sunny-info">
                    
                    <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="">
                    <img src="{{ public_path('assets/sunnyLogo1.png') }}" style="width:120px;">

                    <div class="address">
                        <p><strong>Sunny & Scramble</strong></p>
                        <p>123 Sunny Street</p>
                        <p>Sunnyville, 1860</p>
                    </div>
                </div>
                <div class="order-info">
                    <p><strong>Purchase ID:</strong> {{$order->po_number}}</p>
                    <p><strong>Date:</strong> {{$order->created_at}}</p>
                    <p><strong>Status:</strong> {{$order->status}}</p>
                </div>
            </div>
        </div>
        <div class="form-body" style="padding: 10px">
            <div class="shipping-info">
                <div class="vendor">
                    <h3>Vendor Information</h3>
                    <p>Sunny & Scramble</p>
                    <p>123 Sunny Street Sunnyville, 1860</p>
                    <p><strong>Sales person:</strong> Lisa Hanabishi</p>
                    <p><strong>Phone:</strong> (555) 987-6543</p>
                    <p><strong>Email:</strong> lisa.hanabishi@abcsupplies.com</p>
                </div>
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
                <h3>Shipping Type</h3>
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
                <h3>Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product no.</th>
                            <th>Product Name</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordersItem as $item)
                        <tr>
                            <td>1</td>
                            <td>{{$item->product_id}}</td>
                            <td>{{$item->product->name}}</td>
                            <td></td>
                            <td>{{$item->quantity}}</td>
                            <td>&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td></td>
                            <td>&#8369;{{ number_format($item->total_price, 2) }}</td>
                        
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>


            </div>
            <div class="totals">

                

                <table style="width: 300px; float: right; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Subtotal</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">&#8369;{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Tax</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">{{$order->tax_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Discount</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc; font-weight: bold;">Total</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right; font-weight: bold;">&#8369;{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="notes-signatory">
                <div class="notes">
                    <h3>Additional Notes</h3>
                    <p type="text" name="notes" style="white-space: wrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px; ">{{$order->order_notes}}</p>
                </div>
                <div class="signatory">
                    <h3>Authorized by</h3>
                    <p type="text" style="font-size: 13px;" name="authorized_by">{{$order->receiver_name}}</p></p>
                    <p style="font-size: 12px; color: #666;">Name with signature of Authorized person</p>
                </div>
            </div>

        </div>


    </div>
</body>
</html>
