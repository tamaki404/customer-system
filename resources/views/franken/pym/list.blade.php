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
                                placeholder="Search by CUST ID. , Customer, Representative and status"
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
                    <p class="heading">Proof of payments</p>
                </div>
            </div>
            @if (auth()->user()->role !== 'Customer')
                <div class="content-body" style="background: #fff">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>PO ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @foreach ($payments as  $pay)
                                    <tr onclick="window.location.href='{{ route('pym.payment', ['payment_id' => $pay->payment_id]) }}'">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pay->updated_at->format('F j, y ') }}</td>
                                        <td>{{ $pay->po_id }}</td>
                                        <td>{{ $pay->customer->company_name }}</td>
                                        <td>
                                            @if ($pay->total_amount !== "0.00")
                                                ₱{{ number_format($pay->total_amount, 2) }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>{{ $pay->status }}</td>
                                    </tr>
                                @endforeach
                                   
                            </tbody>
                    </table>
                    {{ $payments->links() }}

                </div>
            @endif
        </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/delivery/pdf_modal.js') }}"></script>

@endpush
