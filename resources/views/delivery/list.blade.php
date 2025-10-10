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
                    <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                        <p class="heading">Deliveries</p>
                

                    </div>
    

                </div>


                @if (auth()->user()->role !== 'Supplier')
                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Supplier</th>
                                    <th>Scheduled</th>
                                    <th>Heads</th>
                                    <th>Kilos</th>
                                    <th>POD</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                                @foreach ($deliveries as  $del)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $del->supplier->company_name }}</td>
                                        @php
                                            $date = \Carbon\Carbon::parse($del->delivery_date);
                                            $receiving = \Carbon\Carbon::parse($del->supplier->requirement->receiving_time);

                                        @endphp

                                        <td>
                                                @if ($date->isToday())
                                                    Today ({{ $receiving->format('h:i A') }})
                                                @elseif ($date->isTomorrow())
                                                    Tomorrow ({{ $receiving->format('h:i A') }})
                                                @elseif ($date->isYesterday())
                                                    Yesterday ({{ $receiving->format('h:i A') }})
                                                @else
                                                    {{ $date->format('M d, Y h:i A') }}
                                                @endif
                                        </td>

                                        @php
                                            $heads = $del->items->sum('planned_heads');
                                            $kilos = $del->items->sum('planned_kilos');
                                        @endphp

                                        <td>{{ $heads > 0 ? $heads : '--' }}</td>
                                        <td>{{ $kilos > 0 ? $kilos : '--' }}</td>


                                        <td></td>
                               

                                        @php
                                            $date = \Carbon\Carbon::parse($del->delivery_date);
                                            $today = \Carbon\Carbon::today();
                                        @endphp

                                        <td>
                                            @if ($del->status === 'Delivered')
                                                <span class="text-success">Delivered</span>
                                            @elseif ($date->isToday())
                                                <span class="text-warning">Delivery today</span>
                                            @elseif ($date->isFuture())
                                                <span class="text-primary">Upcoming</span>
                                            @elseif ($date->isPast())
                                                @php $daysLate = $date->diffInDays($today); @endphp
                                                <span class="text-danger">
                                                    {{ $daysLate }} {{ Illuminate\Support\Str::plural('day', $daysLate) }} late
                                                </span>
                                            @endif
                                        </td>


                                    </tr>
                                
                                @endforeach
                            <tbody>                                
         
                            </tbody>
                        </table>
                
                    </div>

                @endif


        </div>


@endsection

@push('scripts')

@endpush
