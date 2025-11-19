@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/pr/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/status-btn.css')}}">

    {{-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css"> --}}
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
  
    {{-- create order --}}
    @if (auth()->user()->role === 'Customer')
        <div class="modal fade" id="create-order-modal" style="overflow: hidden;" tabindex="-1" aria-spanledby="requestActionspan" aria-hidden="true">
            <div class="modal-dialog modal-xl" style="overflow: hidden; height: 90%; overflow: auto;">
                <form class="modal-content" method="POST" style="width: 900px; overflow: auto;" action="{{ route('pr.create') }}" id="purchaseRequestForm">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionspan">Create purchase order</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-span="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined">info</span>
                            <span>Select products from your available inventory and specify quantities.</span>
                        </p>

                        <!-- Notes Section -->
                        <div class="form-group" style="margin-bottom: 20px; flex-direction: column; display: flex;">
                            <span for="notes">Notes (Optional)</span>
                            <textarea name="notes" style="font-size: 14px; padding: 8px;" id="notes" rows="3" placeholder="Add any additional notes for this purchase order..."></textarea>
                        </div>

                        <!-- Preferred Days Selection -->
                        <div style="margin-bottom: 25px;">
                            <span style="font-weight: 600; margin-bottom: 10px; display: block;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 20px;">calendar_month</span>
                                Choose preferred delivery days <span style="color: #dc3545;">*</span>
                            </span>
                            <div id="preferredDaysContainer" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <!-- Days will be generated here -->
                            </div>
                            <small style="color: #666; display: block; margin-top: 8px;">Select one or more delivery dates</small>
                            <div id="daysError" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: none;"></div>
                        </div>

                        <!-- Products Table -->
                        <div style="margin-bottom: 20px;">
                            <span style="font-weight: 600; margin-bottom: 10px; display: block;">Select Products <span style="color: #dc3545;">*</span></span>
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered" id="productsTable">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th style="width: 60px;">Select</th>
                                            <th>Product</th>
                                            <th style="width: 120px;">Measurement</th>
                                            <th style="width: 100px;">Price (₱)</th>
                                            <th style="width: 100px;">Heads</th>
                                            <th style="width: 100px;">Kilos</th>
                                            <th style="width: 120px;">Subtotal (₱)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr data-product-id="{{ $product->product_id }}" data-set-id="{{ $product->set_id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" 
                                                        class="form-check-input product-checkbox" 
                                                        name="selected_products[]" 
                                                        value="{{ $product->product_id }}"
                                                        data-set-id="{{ $product->set_id }}"
                                                        data-price="{{ $product->nego_price }}"
                                                        data-measurement="{{ $product->product->measurement_type }}">
                                                </td>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ $product->product->measurement_type }}</td>
                                                <td class="text-end">{{ number_format($product->nego_price, 2) }}</td>
                                                <td>
                                                    <input type="number" 
                                                        class="form-control form-control-sm planned-heads" 
                                                        name="planned_heads[{{ $product->product_id }}]" 
                                                        min="0" 
                                                        step="1" 
                                                        placeholder="0"
                                                        disabled>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                        class="form-control form-control-sm planned-kilos" 
                                                        name="planned_kilos[{{ $product->product_id }}]" 
                                                        min="0" 
                                                        step="0.01" 
                                                        placeholder="0.00"
                                                        disabled>
                                                </td>
                                                <td class="text-end row-total">₱0.00</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="background-color: #f8f9fa; font-weight: 600;">
                                        <tr>
                                            <td colspan="7" class="text-end">Total Amount:</td>
                                            <td class="text-end" id="grandTotal">₱0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="productsError" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: none;"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Create Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>

        <style>

        </style>

    @endif
    {{-- Set Sale & Discounts Modal --}}
    <div class="modal fade" id="set-promo-modal" tabindex="-1" aria-spanledby="setPromospan" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form class="modal-content shadow-sm border-0" method="POST" action="{{ route('set.sale_discount') }}">
                    @csrf
            
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionspan"> 
                        
                            Set sale & discounts
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-span="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Products added will be automatically listed.</span>
                        </p>

                        {{-- Type --}}
                        <div class="form-group mt-3">
                            <span for="type" class="form-span">
                                <span class="req-asterisk">*</span> What will it be?
                            </span>
                            <select id="type" name="type" class="form-select" required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Select type --</option>
                                <option value="Sale" {{ old('type') == 'Sale' ? 'selected' : '' }}>Sale</option>
                                <option value="Discount" {{ old('type') == 'Discount' ? 'selected' : '' }}>Discount</option>
                            </select>
                        </div>

                        {{-- Name --}}
                        <div class="form-group mt-3">
                            <span for="name" class="form-span">
                                <span class="req-asterisk">*</span> What would you like it to be called?
                            </span>
                            <input type="text" id="name" name="name" class="form-control" maxlength="100" placeholder="e.g. Summer Sale, Dealer Discount" required>
                        </div>

                        {{-- Description --}}
                        <div class="form-group mt-3">
                            <span for="description" class="form-span">Add a description (Recommended)</span>
                            <textarea id="description" name="description" maxlength="255" class="form-control" rows="2" placeholder="Optional short description...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <span class="form-span">
                                <span class="req-asterisk">*</span> How many is available to sell?
                            </span>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" required>
                            </div>
                        </div>

                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <span class="form-span">
                                <span class="req-asterisk">*</span> Set value
                            </span>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="value" class="form-control" min="0" step="0.01" placeholder="Enter value" required>
                                <select name="value_type" id="value_type" class="form-select w-auto" required>
                                    <option value="percentage" {{ old('value_type') == 'Percentage' ? 'selected' : '' }}>%</option>
                                    <option value="fixed" {{ old('value_type') == 'Fixed' ? 'selected' : '' }}>₱ (Fixed)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Account Type --}}
                        <div class="form-group mt-3">
                            <span for="category" class="form-span">
                                <span class="req-asterisk">*</span> Select account type to apply to
                            </span>
                            <select name="category" id="category" class="form-select" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Select account type --</option>
                                <option value="Wholesale" {{ old('category') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                                <option value="Distributor" {{ old('category') == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="HRI" {{ old('category') == 'HRI' ? 'selected' : '' }}>HRI</option>
                                <option value="Dealer" {{ old('category') == 'Dealer' ? 'selected' : '' }}>Dealer</option>
                            </select>
                        </div>

                        {{-- Product --}}
                        <div class="form-group mt-3">
                            <span for="product" class="form-span">
                                <span class="req-asterisk">*</span> Select product to apply to
                            </span>
                            <select name="product" id="product" class="form-select" required>
                                <option value="" disabled {{ old('product') ? '' : 'selected' }}>-- Select product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}">{{ ucfirst($product->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Effectivity Dates --}}
                        <div class="form-group mt-4 p-3 border rounded-3" style="background: #fafafa;">
                            <span class="form-span  mb-2">Effectivity Period</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span for="start_date" class="form-span small">Start Date</span>
                                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <span for="end_date" class="form-span small">End Date</span>
                                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden --}}
                        <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}" required>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply Sale/Discount</button>
                    </div>
                </form>
            </div>
    </div>
    
    <div class="content-bg" >
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
            <div style="display: flex; flex-direction: column;">
                <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                    <p class="heading">Purchase requests</p>
                    @if ( auth()->user()->role === 'Customer')
                        <div style="display:flex; flex-direction:column; flex-wrap: wrap;">
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Create PO
                            </button>
                            {{-- <button class="add-staff-btn btn-transition w3-disabled" disabled title="Maximum credit limit reached, kindly settle payment first.">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">lock</span>
                                Create PO
                            </button> --}}
                        </div>
                    @endif
                </div>
                <div class="status-btn">
                    <button class="pending"><span>Pending</span><span>({{ $counts['Pending'] ?? 0 }}) </span></button>
                    <button><span>Accepted</span><span>({{ $counts['Accepted'] ?? 0 }}) </span></button>
                    <button><span>Progressing</span><span>({{ $counts['Progressing'] ?? 0 }} )</span></button>
                    <button><span>Completed</span><span>({{ $counts['Completed'] ?? 0 }} )</span></button>
                    <button><span>Cancelled</span><span>({{ $counts['Cancelled'] ?? 0 }} )</span></button>
                    <button>
                        <span>Rejected</span>
                        <span>({{ $counts['counts'] ?? 0 }}) </span>
                    </button>
                </div>
            </div>
        </div>



        @if (auth()->user()->role !== 'Customer')
            <div class="content-body" style="background: #fff">
                <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                    <thead style="background-color: #fff;">
                        <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                            <th>#</th>
                            <th>Timestamp</th>
                            <th>Customer</th>
                            <th>PO ID</th>
                            <th>Heads and kilos</th>
                            <th>Deliveries</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>                                
                        @foreach ($requests as $request)

                            <div class="modal fade" id="scheduling-modal-{{ $request->po_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <form method="POST" action="{{ route('rtn.schedule') }}">
                                    @csrf
                                    <input type="hidden" name="po_id" value="{{ $request->po_id }}">

                                    <div class="modal-header">
                                    <h5 class="modal-title">Schedule a Delivery</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                    <!-- Delivery Date -->
                                    <div class="mb-3">
                                        <span for="delivery_date_{{ $request->po_id }}" class="form-span">Delivery Date</span>
                                        <input type="date" class="form-control" name="delivery_date" id="delivery_date_{{ $request->po_id }}" required>
                                    </div>

                                    <!-- Products from previous deliveries -->
                                    <div class="list-group">
                       
                                        @php
                                            $deliveryItems = \App\Models\DeliveryItemRequest::where('po_id', $request->po_id)
                                                                ->with('set') // <-- load the ProductSetting
                                                                ->get()
                                                                ->groupBy('product_id');
                                        @endphp

                                        @foreach($deliveryItems as $product_id => $items)
                                            @php
                                                $productSetting = $items->first()->set; // ProductSetting instance
                                                if(!$productSetting) continue; // skip if no related product
                                            @endphp

                                            <a href="#" class="list-group-item list-group-item-action product-item" data-product-id="{{ $product_id }}">
                                                {{ $productSetting->product->name ?? 'No Product Name' }}
                                            </a>

                                            <div class="mt-2 ms-3 d-none" id="input-{{ $product_id }}">

                                                {{-- Hidden set_id --}}
                                                <input type="hidden"
                                                    name="items[{{ $product_id }}][set_id]"
                                                    value="{{ $productSetting->set_id }}">

                                                @if(in_array($productSetting->product->measurement_type, ['Heads', 'Heads & Kilos']))
                                                    <label>Planned Heads:</label>
                                                    <input type="number" class="form-control mb-2"
                                                        name="items[{{ $product_id }}][planned_heads]"
                                                        min="0" placeholder="Enter heads">
                                                @endif

                                                @if(in_array($productSetting->product->measurement_type, ['Kilos', 'Heads & Kilos']))
                                                    <label>Planned Kilos:</label>
                                                    <input type="number" class="form-control"
                                                        name="items[{{ $product_id }}][planned_kilos]"
                                                        min="0" placeholder="Enter kilos">
                                                @endif
                                            </div>

                                        @endforeach


                                    
                                    </div>

                                    </div>

                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Delivery</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                            </div>

                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $request->created_at->format('F j, y') }}</td>
                                <td>{{ $request->customer->company_name }}</td>
                                <td>#{{ $request->po_id }}</td>
                                @php
                                    $planned = $request->totalPlanned();
                                    $delivered = $request->totalDelivered();

                                    $remaining_heads = $planned['heads'] - $delivered['heads'];
                                    $remaining_kilos = $planned['kilos'] - $delivered['kilos'];
                                @endphp
                                <td>
                                    <div class="dropdown-div">
                                        <button class="dropdown-btn btn btn-sm dropdown-toggle" type="button" style="color: #fff; font-size: 13px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;"
                                            id="dropdownMenuButton-{{ $request->po_id }}" data-bs-toggle="dropdown" aria-expanded="false"> 
                                            {{ $remaining_heads }} H | {{ $remaining_kilos }} K   
                                        </button>
                        
                                        <ul class="dropdown-menu p-2" aria-spanledby="dropdownMenuButton-{{ $request->po_id }}" style="min-width: 250px;">
                                            
                                            @foreach ($request->deliveryRequests->sortByDesc('delivery_id') as $dr)
        
                                                <li class="dropdown-item hover-container" id="drop-down-li" style="color: #333; " >
                                                    <a href="{{ route('dlv.delivery', ['delivery_id' => $dr->delivery_id]) }}" target="_blank" style="display: flex; align-items: center; flex-direction: column; text-decoration: none; color: #333;">
                                                        <span style="color: #666">
                                                            #{{ $loop->iteration }} {{ $dr->delivery_id }}:
                                                        </span>
                                        


                                                        <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">

                                                            @php
                                                                $heads = $dr->deliveryItems->sum('received_heads');
                                                                $kilos = $dr->deliveryItems->sum('received_kilos');
                                                            @endphp

                                                            <span>
                                                                
                                                                <strong>{{ $heads }}</strong>H
                                                                | <strong>{{ $kilos }}</strong>K
                                                            </span>

                                                            <strong class="icon-display" style="display: flex; align-items: center;">
                                                                @if ($dr->status === "Cancelled")
                                                                    <span class="material-symbols-outlined text-danger">cancel</span>
                                                                @elseif ($dr->status === "Delivered" )
                                                                    <span class="material-symbols-outlined text-success">check_circle</span> 

                                                                @elseif ($dr->status === "Pending" || $dr->status === "Scheduled" || $dr->status === "In Transit")
                                                                    <span class="material-symbols-outlined " style="color: #f8912a">hourglass_top</span>
                                                                @endif
                                                            </strong> 
                                                        </div>
                                                        
                                                    </a>
                                                    <div class="popup-content-nested" style="z-index: 1000;">
                                                        <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                                            <p>
                                                                <span>ID:</span>
                                                                <strong>#{{ $dr->delivery_id }}</strong>
                                                            </p>
                                                            <p style="margin-left: 15px">
                                                                <strong>{{ $dr->status }}</strong>
                                                            </p>


                                                        </div>
                                                            <p style="display: flex; flex-direction: column;">
                                                                <span>Scheduled on <strong>{{ $dr->delivery_date->format('F j, Y') }}</strong></span>
                                                                @if ($dr->status === "Delivered")
                                                                    <span>Delivered on <strong>{{ $dr->delivered_date->format('F j, Y') }}</strong></span>
                                                                @elseif ($dr->status === "Cancelled")
                                                                    <span>Cancelled on <strong>{{ $dr->updated_at->format('F j, Y') }}</strong></span>
                                                                @endif
                                                            </p>
                                                        <div>
                                                            <p style="margin: 0">
                                                                <span>Items ({{  $dr->Delitems->count() }})</span>
                                                            </p>
                                                            <ul>
                                                                @foreach ($dr->Delitems as $item)
                                                                    <li style="display: flex; flex-direction: row; " class="li-delivery-items">
                                                                        <p style="margin-right: 10px; font-weight: bold;">
                                                                            <span>
                                                                                {{ $item->product->name }}
                                                                            </span>
                                                                        </p>
                                                                        <p class="font-size: 13px; margin-left: 10px; margin:0;">
                                                                            (
                                                                            <span>
                                                                                @if ($item->product->measurement_type == 'Heads')
                                                                                    {{ $item->planned_heads }}H 
                                                                                @elseif ($item->product->measurement_type == 'Kilos')
                                                                                    {{ $item->planned_kilos }}K
                                                                                @else
                                                                                    {{ $item->planned_heads }}H - {{ $item->planned_kilos }}K
                                                                                @endif
                                                                            </span>
                                                                            @if ($dr->status === "Delivered")
                                                                                <span>--> </span>
                                                                                <span>
                                                                                    @if ($item->product->measurement_type == 'Heads')
                                                                                        @if ($item->planned_heads === $item->received_heads)
                                                                                            <span style="color: green">{{ $item->received_heads }}H</span>
                                                                                        @else
                                                                                            <span style="color: #dc3545;">{{ $item->received_heads }}H</span>
                                                                                        @endif
                                                                                    @elseif ($item->product->measurement_type == 'Kilos')
                                                                                        @if ($item->planned_kilos === $item->received_kilos)
                                                                                            <span style="color: green">{{ $item->received_kilos }}K</span>
                                                                                        @else
                                                                                            <span style="color: #dc3545;">{{ $item->received_kilos }}K</span>
                                                                                        @endif
                                                                                    @elseif ($item->product->measurement_type == 'Heads&Kilos')
                                                                                        @if ($item->planned_heads === $item->received_heads)
                                                                                            <span style="color: green">{{ $item->received_heads }}H</span>
                                                                                        @else
                                                                                            <span style="color: #dc3545;">{{ $item->received_heads }}H</span>
                                                                                        @endif
                                                                                        @if ($item->planned_kilos === $item->received_kilos)
                                                                                            <span style="color: green">{{ $item->received_kilos }}K</span>
                                                                                        @else
                                                                                            <span style="color: #dc3545;">{{ $item->received_kilos }}K</span>
                                                                                    
                                                                                        @endif
                                                                                    @endif

                                                                                    {{-- display if there's variance --}}
                                                                                    @if($item->product->measurement_type === 'Heads')
                                                                                        @if($item->received_heads != $item->planned_heads)
                                                                                            <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H)</span>
                                                                                        @endif
                                                                                    @elseif($item->product->measurement_type === 'Kilos')
                                                                                        @if($item->received_kilos != $item->planned_kilos)
                                                                                            <span style="color: #dc3545">({{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                        @endif
                                                                                    @elseif($item->product->measurement_type === 'Heads&Kilos')
                                                                                        @if($item->received_heads != $item->planned_heads)
                                                                                            <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H)</span>
                                                                                        @endif
                                                                                        @if($item->received_kilos != $item->planned_kilos)
                                                                                            <span style="color: #dc3545">({{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                        @endif
                                                                                    @endif

                                                                                    {{-- @if ($item->received_heads != $item->planned_heads || $item->received_kilos != $item->planned_kilos)
                                                                                        <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H, {{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                    @endif --}}
                                                                                </span>
                                                                        
                                                                            @endif
                                                                            )

                                                                        </p>

                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>

                                                    </div>

                                                </li>
                                                

                                            @endforeach

                                            <li><hr class="dropdown-divider"></li>

                                            <li class="dropdown-item">
                                                <strong style="font-size: 13px; color: #666;">Planned:</strong><br>
                                                {{ $planned['heads'] }} H | {{ $planned['kilos'] }} K
                                            </li>

                                        </ul>
                                    </div>
                                </td>                                
                                @php
                                    $delivered_count = $request->deliveryRequests->where('status', 'Delivered')->count();
                                @endphp
                                <td>
                                    @if ( $request->status !== "Pending")
                                        {{ $delivered_count }}/{{ $request->deliveryRequests->count() }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>
                                    @if ($request->status == 'Completed' && $request->hasVariance())
                                    Has Variance
                                    @else
                                        {{ $request->status }}
                                    @endif
                                </td>  
                                <td>
                                    <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                        <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                            <span class="material-symbols-outlined">
                                            expand_circle_down
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $request->po_id]) }}"><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                            <li>
                                                <a class="dropdown-item"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#scheduling-modal-{{ $request->po_id }}">
                                                    <span class="material-symbols-outlined">calendar_clock</span>
                                                    Schedule a delivery
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        @elseif (auth()->user()->role === 'Customer')
            <div class="content-body" style="background: #fff">
                <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                    <thead style="background-color: #fff;">
                        <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                            <th>#</th>
                            <th>Last update</th>
                            <th>PO ID</th>
                            <th>Heads and kilos</th>
                            <th>Deliveries</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                                
                    @foreach ($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $request->updated_at->format('F j, Y, g:i a') }}</td>
                            <td>{{ $request->po_id }}</td>
                            @php
                                $planned = $request->totalPlanned();
                                $delivered = $request->totalDelivered();

                                $remaining_heads = $planned['heads'] - $delivered['heads'];
                                $remaining_kilos = $planned['kilos'] - $delivered['kilos'];
                            @endphp
                            <td>
                                <div class="dropdown-div">
                                    <button class="dropdown-btn btn btn-sm dropdown-toggle" type="button" style="color: #fff; font-size: 13px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;"
                                        id="dropdownMenuButton-{{ $request->po_id }}" data-bs-toggle="dropdown" aria-expanded="false"> 
                                        {{ $remaining_heads }} H | {{ $remaining_kilos }} K   
                                    </button>
                     
                                    <ul class="dropdown-menu p-2" aria-spanledby="dropdownMenuButton-{{ $request->po_id }}" style="min-width: 250px;">
                                        
                                        @foreach ($request->deliveryRequests->sortByDesc('delivery_id') as $dr)
    
                                            <li class="dropdown-item hover-container" id="drop-down-li" style="color: #333; " >
                                                <a href="{{ route('dlv.delivery', ['delivery_id' => $dr->delivery_id]) }}" target="_blank" style="display: flex; align-items: center; flex-direction: column; text-decoration: none; color: #333;">
                                                    <span style="color: #666">
                                                        #{{ $loop->iteration }} {{ $dr->delivery_id }}:
                                                    </span>
                                      


                                                    <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">

                                                        @php
                                                            $heads = $dr->deliveryItems->sum('received_heads');
                                                            $kilos = $dr->deliveryItems->sum('received_kilos');
                                                        @endphp

                                                        <span>
                                                            
                                                            <strong>{{ $heads }}</strong>H
                                                            | <strong>{{ $kilos }}</strong>K
                                                        </span>

                                                        <strong class="icon-display" style="display: flex; align-items: center;">
                                                            @if ($dr->status === "Cancelled")
                                                                <span class="material-symbols-outlined text-danger">cancel</span>
                                                            @elseif ($dr->status === "Delivered" )
                                                                <span class="material-symbols-outlined text-success">check_circle</span> 

                                                            @elseif ($dr->status === "Pending" || $dr->status === "Scheduled" || $dr->status === "In Transit")
                                                                <span class="material-symbols-outlined " style="color: #f8912a">hourglass_top</span>
                                                            @endif
                                                        </strong> 
                                                    </div>
                                                    
                                                </a>
                                                <div class="popup-content-nested" style="z-index: 1000;">
                                                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                                        <p>
                                                            <span>ID:</span>
                                                            <strong>#{{ $dr->delivery_id }}</strong>
                                                        </p>
                                                        <p style="margin-left: 15px">
                                                            <strong>{{ $dr->status }}</strong>
                                                        </p>


                                                    </div>
                                                        <p style="display: flex; flex-direction: column;">
                                                            <span>Scheduled on <strong>{{ $dr->delivery_date->format('F j, Y') }}</strong></span>
                                                            @if ($dr->status === "Delivered")
                                                                <span>Delivered on <strong>{{ $dr->delivered_date->format('F j, Y') }}</strong></span>
                                                            @elseif ($dr->status === "Cancelled")
                                                                <span>Cancelled on <strong>{{ $dr->updated_at->format('F j, Y') }}</strong></span>
                                                            @endif
                                                        </p>
                                                    <div>
                                                        <p style="margin: 0">
                                                            <span>Items ({{  $dr->Delitems->count() }})</span>
                                                        </p>
                                                        <ul>
                                                            @foreach ($dr->Delitems as $item)
                                                                <li style="display: flex; flex-direction: row; " class="li-delivery-items">
                                                                    <p style="margin-right: 10px; font-weight: bold;">
                                                                        <span>
                                                                            {{ $item->product->name }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="font-size: 13px; margin-left: 10px; margin:0;">
                                                                        (
                                                                        <span>
                                                                            @if ($item->product->measurement_type == 'Heads')
                                                                                {{ $item->planned_heads }}H 
                                                                            @elseif ($item->product->measurement_type == 'Kilos')
                                                                                {{ $item->planned_kilos }}K
                                                                            @else
                                                                                {{ $item->planned_heads }}H - {{ $item->planned_kilos }}K
                                                                            @endif
                                                                        </span>
                                                                        @if ($dr->status === "Delivered")
                                                                            <span>--> </span>
                                                                            <span>
                                                                                @if ($item->product->measurement_type == 'Heads')
                                                                                    @if ($item->planned_heads === $item->received_heads)
                                                                                        <span style="color: green">{{ $item->received_heads }}H</span>
                                                                                    @else
                                                                                        <span style="color: #dc3545;">{{ $item->received_heads }}H</span>
                                                                                    @endif
                                                                                @elseif ($item->product->measurement_type == 'Kilos')
                                                                                    @if ($item->planned_kilos === $item->received_kilos)
                                                                                        <span style="color: green">{{ $item->received_kilos }}K</span>
                                                                                    @else
                                                                                        <span style="color: #dc3545;">{{ $item->received_kilos }}K</span>
                                                                                    @endif
                                                                                @elseif ($item->product->measurement_type == 'Heads&Kilos')
                                                                                    @if ($item->planned_heads === $item->received_heads)
                                                                                        <span style="color: green">{{ $item->received_heads }}H</span>
                                                                                    @else
                                                                                        <span style="color: #dc3545;">{{ $item->received_heads }}H</span>
                                                                                    @endif
                                                                                    @if ($item->planned_kilos === $item->received_kilos)
                                                                                        <span style="color: green">{{ $item->received_kilos }}K</span>
                                                                                    @else
                                                                                        <span style="color: #dc3545;">{{ $item->received_kilos }}K</span>
                                                                                
                                                                                    @endif
                                                                                @endif

                                                                                {{-- display if there's variance --}}
                                                                                @if($item->product->measurement_type === 'Heads')
                                                                                    @if($item->received_heads != $item->planned_heads)
                                                                                        <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H)</span>
                                                                                    @endif
                                                                                @elseif($item->product->measurement_type === 'Kilos')
                                                                                    @if($item->received_kilos != $item->planned_kilos)
                                                                                        <span style="color: #dc3545">({{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                    @endif
                                                                                @elseif($item->product->measurement_type === 'Heads&Kilos')
                                                                                    @if($item->received_heads != $item->planned_heads)
                                                                                        <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H)</span>
                                                                                    @endif
                                                                                    @if($item->received_kilos != $item->planned_kilos)
                                                                                        <span style="color: #dc3545">({{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                    @endif
                                                                                @endif

                                                                                {{-- @if ($item->received_heads != $item->planned_heads || $item->received_kilos != $item->planned_kilos)
                                                                                    <span style="color: #dc3545">({{ $item->received_heads - $item->planned_heads }}H, {{ $item->received_kilos - $item->planned_kilos }}K)</span>
                                                                                @endif --}}
                                                                            </span>
                                                                    
                                                                        @endif
                                                                        )

                                                                    </p>

                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                </div>

                                            </li>
                                            

                                        @endforeach

                                        <li><hr class="dropdown-divider"></li>

                                        <li class="dropdown-item">
                                            <strong style="font-size: 13px; color: #666;">Planned:</strong><br>
                                            {{ $planned['heads'] }} H | {{ $planned['kilos'] }} K
                                        </li>

                                    </ul>
                                </div>
                            </td>
                            @php
                                $finished = $request->deliveryRequests->where('status', 'Delivered')->count();
                                $all = $request->deliveryRequests->count();
                            @endphp
                            <td>{{ $finished }}/{{ $all }}</td>

                            <td >
                                <p  style="margin: 0">
                                    @if ($request->status == 'Completed' && $request->hasVariance())
                                    Has Variance
                                    @else
                                        {{ $request->status }}
                                   
                                    @endif
                                </p>

                            </td>
                            <td>
                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                        <span class="material-symbols-outlined">
                                        expand_circle_down
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('pr.request', ['po_id' => $request->po_id]) }}"><span class="material-symbols-outlined">package_2</span>Purchase order</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        @endif
            <div class="pagination-div" style="margin-top: 15px;">
                <p>Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries</p>
                {{ $requests->links() }}
            </div>
    </div>


@endsection

@push('scripts')
    <script src="{{ asset('js\pr\list.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                const inputDiv = document.getElementById('input-' + productId);
                inputDiv.classList.toggle('d-none');
            });
        });
    });
</script>


@endpush
