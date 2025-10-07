@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
@endpush

@section('content')


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('orders.order', ['order_id' => $delivery->order_id]) }}">< Order view</a>

                </p>
            </div>

        </div>

        <div class="delivery-status">
            <style>
                .delivery-status p{
                    margin: 0;
                    font-weight: bold;
                }
                .delivery-status p span{
                    font-weight: normal;
                }
            </style>
           <p>Status: <span>{{$delivery->status}}</span></p>
           <p>Delivery date: <span>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F j, Y') }}</span></p>
           @if($delivery->status === "Delivered")
                <p>Completed date: <span></span></p>
                <button>View delivery receipt</button>
           @endif

        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="table-body" style="margin-top: 50px">
                <p style="margin: 5px; font-weight: bold;">Delivery items</p>
                <div class="table-content"  style="background: #fff; border-radius: 10px; overflow: hidden;">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                        <thead style="background-color: #f8f8f8;">
                            <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                <th>#</th>
                                <th>Item ID</th>
                                <th>Product</th>
                                <th>Measurement</th>
                                <th>Planned Qty</th>
                                <th>Received Qty</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($delivery->deliveryItems as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->delivery_item_id }}</td>
                                    <td>{{ $item->orderItem->product->name ?? '—' }}</td>
                                    <td>{{ $item->orderItem->product->measurement_type ?? '—' }}</td>
                                    <td>
                                        @if ($item->planned_heads)
                                            {{ $item->planned_heads }} heads
                                        @elseif ($item->planned_kilos)
                                            {{ $item->planned_kilos }} kg
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->orderItem->product->measurement_type === "Heads")
                                            {{ $item->received_heads }} heads
                                        @elseif ($item->orderItem->product->measurement_type === "Kilos")
                                            {{ $item->received_kilos }} kg
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($item->status ?? 'Pending') }}</td>
                                    <td>{{ $item->remarks ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                       
            </div>
        </div>
   </div>


@endsection

@push('scripts')

@endpush