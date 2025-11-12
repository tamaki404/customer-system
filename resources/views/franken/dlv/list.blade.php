@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/pr/list.css')}}">
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">

@endpush

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" style="margin: 10px;">
            <h6 style="margin-bottom: 10px; font-weight: bold;">Validation Errors:</h6>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 14px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success') || session('error'))
        <div 
            id="flash-message"
            class="flash-message 
                {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            <strong>
                {{ session('success') ? 'Success:' : ' Error:' }}
            </strong>
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    
        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by PO ID, Customer, Status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>
                        <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
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

                <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                    <p class="heading">Delivery summary ({{$delivery->count()}})</p>
                </div>
                @if ($user->role === "Customer")
                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Updated on</th>
                                    <th>Heads/Kilos</th>
                                    <th>PO ID</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if ($delivery)
                                    @foreach ($delivery as $del)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $del->updated_at->format('F j, y g:i a') }}</td>
                                            <td>
                                                @php
                                                    $sum_heads = $del->items->sum('planned_heads');
                                                    $sum_kilos = $del->items->sum('planned_kilos');
                                                @endphp
                            
                                                {{ $sum_heads}} - {{ $sum_kilos}}
                                            </td>
                                            <td>#{{ $del->po_id }}</td>
                                            <td>{{$del->delivery_date->format('F j, y')}}</td>
                                            <td>{{ $del->status}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                        <p>No data</p>
                                @endif                            

                            </tbody>
                        </table>
                    </div>
                @elseif($user->role !== "Customer")
                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Updated on</th>
                                    <th>Customer</th>
                                    <th>PO ID</th>
                                    <th>Heads/Kilos</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if ($delivery)
                                    @foreach ($delivery as $del)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $del->updated_at->format('F j, y g:i a') }}</td>
                                            <td>{{ $del->customer->company_name}}</td>
                                            <td>#{{ $del->po_id }}</td>
                                            <td>
                                                @php
                                                    $sum_heads = $del->items->sum('planned_heads');
                                                    $sum_kilos = $del->items->sum('planned_kilos');
                                                @endphp
                            
                                                {{ $sum_heads}} - {{ $sum_kilos}}
                                            </td>
                                            <td>{{$del->delivery_date->format('F j, y')}}</td>
                                            <td>{{ $del->status}}</td>
                                            <td>
                                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                        <span class="material-symbols-outlined">
                                                        expand_circle_down
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $del->po_id]) }}">
                                                             <span class="material-symbols-outlined">package_2</span>
                                                             Purchase order
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('dlv.delivery', ['delivery_id' => $del->delivery_id]) }}">
                                                                <span class="material-symbols-outlined">call_made</span>
                                                                Delivery
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item">
                                                                {{-- href="{{ route('pym.collection', ['po_id' => $transaction->po_id]) }}"> --}}
                                                                <span class="material-symbols-outlined">download</span>
                                                                Save receipt
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                @else
                                        <p>No data</p>
                                @endif                            

                            </tbody>
                        </table>
                    </div>
                @endif



        </div>


@endsection

@push('scripts')

    <script src="{{ asset('js/pr/list.js') }}"></script>

@endpush
