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
                                placeholder="Search by SUP ID. , Customer, Representative and status"
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


                @if (auth()->user()->role !== 'Customer')
                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Delivery_ID</th>
                                    <th>Customer</th>
                                    <th>Scheduled</th>
                                    <th>Heads</th>
                                    <th>Kilos</th>
                                    <th>Status</th>
                                    <th>Delivery receipt</th>

                                </tr>
                            </thead>
                                @foreach ($deliveries as  $del)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $del->delivery_id }}</td>
                                        <td>{{ $del->customer->company_name }}</td>
                                        @php
                                            $date = \Carbon\Carbon::parse($del->delivery_date);
                                            $receiving = \Carbon\Carbon::parse($del->customer->requirement->receiving_time);

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


                               

                                        @php
                                            $date = \Carbon\Carbon::parse($del->delivery_date);
                                            $today = \Carbon\Carbon::today();
                                        @endphp

                                        <td>
                                            @if ($del->status !== "Delivered")

                                                @if ($del->status === '7 days late')
                                                    <span class="text-success">Late</span>
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
                                            @elseif ($del->status === "Delivered")

                                                    {{ $del->status }}

                                            @endif
                                        </td>

                                        <td>
                                            @if ($del->status==="Delivered")
                                                @if($del->pod_file)
                                                    <button type="button" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewPOD{{ $del->delivery_id }}" 
                                                            class="btn-transition">
                                                            View POD
                                                    </button>
                                                @else
                                                    <span class="text-muted">No POD</span>
                                                @endif
                                            @elseif($del->status === 'Scheduled')
                                                <button type="button" 
                                                        data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                                        data-url="{{ route('orders.delivery.pdf', $del->order->order_id) }}"
                                                        class="btn-transition">
                                                    Delivery receipt
                                                </button>
                                                
                                            @endif


                                        </td>



                                    </tr>
                                    @if($del->pod_file)
                                        @php
                                            $podData = 'data:' . ($del->pod_mime ?? 'application/pdf') . ';base64,' . base64_encode($del->pod_file);
                                        @endphp

                                        <div class="modal fade" id="viewPOD{{ $del->delivery_id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Proof of Delivery - {{ $del->delivery_id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center" style="height: 80vh;">
                                                        <iframe
                                                            src="{{ $podData }}"
                                                            width="100%"
                                                            height="100%"
                                                            style="border: none;"
                                                            title="POD for {{ $del->delivery_id }}"
                                                        ></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="{{ $podData }}" 
                                                        download="POD_{{ $del->delivery_id }}.pdf" 
                                                        class="btn btn-primary">
                                                            Download POD
                                                        </a>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            <tbody>                                
         
                            </tbody>
                        </table>
                            <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content" style="width: 100%">
                                        <div class="modal-header">
                                            <p class="modal-title">PDF Preview</p>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="height: 80vh;">
                                            <iframe id="pdfFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>



                
                    </div>

                @endif


        </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/delivery/pdf_modal.js') }}"></script>

@endpush
