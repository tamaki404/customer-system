@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')



        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('products.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by SUP ID. , Supplier, Representative and status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>


                        <form action="{{ route('products.list') }}" class="date-search" id="from-to-date" method="GET">
                            <p>Date range</p>
                            <div class="from-to-picker">
                                <div class="month-div">
                                    <span>From</span>
                                    <input type="date" name="from_date" class="input-date"
                                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="month-div">
                                    <span>To</span>
                                    <input type="date" name="to_date" class="input-date"
                                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>

    

                </div>


                @if (auth()->user()->role !== 'Supplier')
                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Heads/Kilos</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Running balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($orders as $order)
                                    <tr onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $order->created_at->format('F j, Y') }}</td>

                                        <td>{{$order->order_id}}</td>
                                        <td>{{$order->item->product->measurement_type}}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{$order->payment_status}}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>

                                        <td>{{$order->status}}</td>
                                        <td>
                                            @if($order->delivery_ratio !== '0/0')
                                                {{ $order->delivery_ratio }} {{ $order->delivery_note }}
                                            @else
                                                No deliveries yet
                                            @endif
                                        </td>





                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                    </div>
                @elseif (auth()->user()->role === 'Supplier')

                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Heads/Kilos</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($orders as $order)
                                    <tr onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $order->created_at->format('F j, Y') }}</td>

                                        <td>{{$order->order_id}}</td>
                                        <td>{{$order->item->product->measurement_type}}</td>
                                        <td>{{$order->payment_status}}</td>

                                        <td>{{$order->status}}</td>
                                        <td>
                                            @if($order->delivery_ratio !== '0/0')
                                                {{ $order->delivery_ratio }} {{ $order->delivery_note }}
                                            @else
                                                No deliveries yet
                                            @endif
                                        </td>
                                        





                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif

        </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/file-preview.js') }}"></script>

@endpush
