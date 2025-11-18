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
                    <p class="heading">Return reports</p>
                </div>
            </div>
            <div class="content-body" style="background: #fff">
                <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                    <thead style="background-color: #fff;">
                        <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                            <th>#</th>
                            <th>Delivered at</th>
                            <th>Customer</th>
                            <th>Delivery ID</th>
                            <th>Variance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>          
                    @if($varianceByDelivery->isNotEmpty())
                        @foreach($varianceByDelivery as $d)
                            <div class="modal fade" id="scheduling-modal-{{ $d->delivery_id }}" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable"> 
                                    <div class="modal-content">

                                        <form method="POST" action="{{ route('rtn.create') }}">
                                            @csrf

                                            <div class="modal-header">
                                                <p class="modal-title d-flex align-items-center gap-2" id="requestActionLabel">
                                                    <span class="material-symbols-outlined" style="font-size: 18px;">calendar_clock</span>
                                                    <span style="font-size: 14px;">Schedule Delivery for Variance</span>
                                                </p>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                {{-- Hidden Inputs --}}
                                                <input type="hidden" name="original_delivery_id" value="{{ $d->delivery_id }}">
                                                <input type="hidden" name="po_id" value="{{ $d->delivery->po_id }}">
                                                <input type="hidden" name="customer_id" value="{{ $d->delivery->customer_id }}">

                                                {{-- Customer Section --}}
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @php
                                                            $imgSrc = $d->delivery->customer->user->image
                                                                ? ('data:' . $d->delivery->customer->user->image_mime_type . ';base64,' . base64_encode($d->delivery->customer->user->image))
                                                                : asset('images/default-avatar.png');
                                                        @endphp
                                                        <img src="{{ $imgSrc }}" style="height:40px; border-radius:20px; border:2px solid #f8912a;">
                                                        <div>
                                                            <strong style="color:#666;">{{ $d->delivery->customer->company_name }}</strong><br>
                                                            <small style="color:#666;">{{ $d->delivery->customer->category }}</small>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 p-2 border rounded">
                                                        <p class="m-0"><span class="text-muted">PO ID: </span><strong>{{ $d->delivery->po_id }}</strong></p>
                                                        <p class="m-0"><span class="text-muted">Original Delivery ID: </span><strong>{{ $d->delivery_id }}</strong></p>
                                                        <p class="m-0"><span class="text-muted">Delivered at: </span>
                                                            <strong>{{ $d->delivery->delivered_date->format('F j, Y g:i a') }}</strong>
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- Schedule Date --}}
                                                <div class="p-2 border rounded mb-3">
                                                    <label class="fw-bold text-muted">Scheduled Delivery Date *</label>
                                                    <input type="datetime-local"
                                                        name="scheduled_date"
                                                        class="form-control"
                                                        required
                                                        min="{{ now()->format('Y-m-d\TH:i') }}">
                                                </div>

                                                {{-- Variance Items --}}
                                                <div class="p-2 border rounded">

                                                    @php
                                                        $itemsWithVariance = \App\Models\DeliveryItemRequest::where('delivery_id', $d->delivery_id)
                                                            ->whereRaw('planned_heads != received_heads OR planned_kilos != received_kilos')
                                                            ->with('product')
                                                            ->get();
                                                    @endphp

                                                    <p class="fw-bold text-muted">Select Items with Variance</p>

                                                    @foreach($itemsWithVariance as $item)
                                                        <div class="border rounded p-2 mb-3">

                                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                                <input type="checkbox" 
                                                                    name="selected_items[]" 
                                                                    value="{{ $item->delivery_item_id }}"
                                                                    id="item_{{ $item->delivery_item_id }}">
                                                                <label for="item_{{ $item->delivery_item_id }}" class="fw-bold">
                                                                    #{{ $loop->iteration }} — {{ $item->product->name }} (ID: {{ $item->delivery_item_id }})
                                                                </label>
                                                            </div>

                                                            <div class="row small">
                                                                <div class="col">
                                                                    <p class="m-0">Planned Heads: <strong>{{ $item->planned_heads }}</strong></p>
                                                                    <p class="m-0">Received Heads: <strong>{{ $item->received_heads }}</strong></p>
                                                                    <p class="m-0 text-warning fw-bold">
                                                                        Variance: {{ $item->planned_heads - $item->received_heads }}
                                                                    </p>
                                                                </div>
                                                                <div class="col">
                                                                    <p class="m-0">Planned Kilos: <strong>{{ $item->planned_kilos }}</strong></p>
                                                                    <p class="m-0">Received Kilos: <strong>{{ $item->received_kilos }}</strong></p>
                                                                    <p class="m-0 text-warning fw-bold">
                                                                        Variance: {{ number_format($item->planned_kilos - $item->received_kilos, 2) }} kg
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="mt-2 pt-2 border-top">
                                                                <label class="small text-muted">New Delivery Quantities:</label>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <input type="number" 
                                                                            name="planned_heads[{{ $item->delivery_item_id }}]"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ abs($item->planned_heads - $item->received_heads) }}"
                                                                            min="0"
                                                                            step="1">
                                                                    </div>
                                                                    <div class="col">
                                                                        <input type="number"
                                                                            name="planned_kilos[{{ $item->delivery_item_id }}]"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ number_format(abs($item->planned_kilos - $item->received_kilos),2,'.','') }}"
                                                                            min="0"
                                                                            step="0.01">
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="product_ids[{{ $item->delivery_item_id }}]" value="{{ $item->product_id }}">
                                                                <input type="hidden" name="set_ids[{{ $item->delivery_item_id }}]" value="{{ $item->set_id }}">
                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>

                                            <div class="modal-footer bg-white">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="yellow-btn">Schedule Delivery</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>


                            {{-- Table Row --}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->delivery->delivered_date->format('F j, Y') }}</td>
                                <td>{{ $d->delivery->customer->company_name }}</td>
                                <td>#{{ $d->delivery_id }}</td>
                                <td>{{ $d->heads_variance }} - {{ number_format($d->kilos_variance, 2) }}kg</td>
                                <td>Unresolved</td>
                                <td>
                                    <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                        <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px;">
                                            <span class="material-symbols-outlined">expand_circle_down</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" style="color: #f8912a"
                                                    href="{{ route('pr.request', ['po_id' => $d->delivery->po_id ?? NULL]) }}">
                                                    <span class="material-symbols-outlined">package_2</span>
                                                    Purchase order
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" style="color: #333"
                                                    href="{{ route('dlv.delivery', ['delivery_id' => $d->delivery_id ?? NULL]) }}">
                                                    <span class="material-symbols-outlined">arrow_outward</span>
                                                    Go to delivery
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    data-bs-toggle="modal" data-bs-target="#scheduling-modal-{{ $d->delivery_id ?? NULL }}">
                                                    <span class="material-symbols-outlined">calendar_clock</span>
                                                    Schedule a delivery
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
            </div>

            <div class="pagination-div" style="margin-top: 15px;">
                <p>Showing {{ $varianceByDelivery->firstItem() }} to {{ $varianceByDelivery->lastItem() }} of {{ $varianceByDelivery->total() }} entries</p>
                {{ $varianceByDelivery->links() }}
            </div>
        </div>
@endsection

@push('scripts')

@endpush
