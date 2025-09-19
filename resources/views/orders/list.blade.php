@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')

    {{-- create order --}}
    @if (auth()->user()->role === 'Supplier')
        <div class="modal fade" id="create-order-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content"  method="POST" action="{{ route('order.create') }}"  enctype="multipart/form-data">
                    @csrf
            
                    @if (session('success'))
                        <div class="alert alert-success" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Success:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" style="margin: 10px;">
                            <h6 style="margin-bottom: 5px; font-weight: bold;">Error:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                        </div>
                    @endif
                
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Create order form</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span> Make sure image selected is 2MB or less, scanned image is recommended.</span>

                        </p>

                        <div class="modal-option-groups">
                            <div class="form-group">
                                    <p><span class="req-asterisk">*</span> Upload purchase order file</p>
                                    <input type="file" name="image" id="image" required accept="image/*">
                                    <div id="file-preview" style="margin-top:10px;"></div>
                                    <div id="file-error" style="color:#dc3545; font-size:13px; margin-top:5px;"></div>
                            </div>
                            <input type="hidden" name="status" value="Pending">
                            <input type="hidden" name="supplier_id" value="{{ auth()->user()->supplier->supplier_id }}">
                        </div>
        

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="add-staff-submit">Submit order</button>
                    </div>


                
                </form>
            </div>
        </div>

    @endif

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

                    <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                        <p class="heading">Order list</p>
                        @if ( auth()->user()->role === 'Supplier')
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Create order
                            </button>
                        @endif

                    </div>

                </div>


                @if (auth()->user()->role !== 'Supplier')
                 
                @elseif (auth()->user()->role === 'Supplier')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Quantity</th>
                                    {{-- <th>Amount</th> --}}
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($orders as $order)
                                    <tr onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>{{$order->order_id}}</td>
                                        <td>--<td>
                                        {{-- <td>{{$order->amount}}</td> --}}
                                        <td>{{$order->status}}</td>

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
