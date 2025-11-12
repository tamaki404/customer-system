@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/pr/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/status-btn.css')}}">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
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
        <div class="modal fade" id="create-order-modal" style="overflow: hidden;" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" style="overflow: hidden; height: 90%; overflow: auto;">
                <form class="modal-content" method="POST" style="width: 900px; overflow: auto;" action="{{ route('pr.create') }}" id="purchaseRequestForm">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Create Purchase Order</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined">info</span>
                            <span>Select products from your available inventory and specify quantities.</span>
                        </p>

                        <!-- Notes Section -->
                        <div class="form-group" style="margin-bottom: 20px; flex-direction: column; display: flex;">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" style="font-size: 14px; padding: 8px;" id="notes" rows="3" placeholder="Add any additional notes for this purchase order..."></textarea>
                        </div>

                        <!-- Preferred Days Selection -->
                        <div style="margin-bottom: 25px;">
                            <label style="font-weight: 600; margin-bottom: 10px; display: block;">
                                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 20px;">calendar_month</span>
                                Choose Preferred Delivery Days *
                            </label>
                            <div id="preferredDaysContainer" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <!-- Days will be generated here -->
                            </div>
                            <small style="color: #666; display: block; margin-top: 8px;">Select one or more delivery dates</small>
                            <div id="daysError" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: none;"></div>
                        </div>

                        <!-- Products Table -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 600; margin-bottom: 10px; display: block;">Select Products *</label>
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
    <div class="modal fade" id="set-promo-modal" tabindex="-1" aria-labelledby="setPromoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form class="modal-content shadow-sm border-0" method="POST" action="{{ route('set.sale_discount') }}">
                    @csrf
            
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel"> 
                        
                            Set sale & discounts
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body --}}
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Products added will be automatically listed.</span>
                        </p>

                        {{-- Type --}}
                        <div class="form-group mt-3">
                            <label for="type" class="form-label">
                                <span class="req-asterisk">*</span> What will it be?
                            </label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>-- Select type --</option>
                                <option value="Sale" {{ old('type') == 'Sale' ? 'selected' : '' }}>Sale</option>
                                <option value="Discount" {{ old('type') == 'Discount' ? 'selected' : '' }}>Discount</option>
                            </select>
                        </div>

                        {{-- Name --}}
                        <div class="form-group mt-3">
                            <label for="name" class="form-label">
                                <span class="req-asterisk">*</span> What would you like it to be called?
                            </label>
                            <input type="text" id="name" name="name" class="form-control" maxlength="100" placeholder="e.g. Summer Sale, Dealer Discount" required>
                        </div>

                        {{-- Description --}}
                        <div class="form-group mt-3">
                            <label for="description" class="form-label">Add a description (Recommended)</label>
                            <textarea id="description" name="description" maxlength="255" class="form-control" rows="2" placeholder="Optional short description...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <span class="req-asterisk">*</span> How many is available to sell?
                            </label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" required>
                            </div>
                        </div>

                        {{-- Value --}}
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <span class="req-asterisk">*</span> Set value
                            </label>
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
                            <label for="category" class="form-label">
                                <span class="req-asterisk">*</span> Select account type to apply to
                            </label>
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
                            <label for="product" class="form-label">
                                <span class="req-asterisk">*</span> Select product to apply to
                            </label>
                            <select name="product" id="product" class="form-select" required>
                                <option value="" disabled {{ old('product') ? '' : 'selected' }}>-- Select product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}">{{ ucfirst($product->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Effectivity Dates --}}
                        <div class="form-group mt-4 p-3 border rounded-3" style="background: #fafafa;">
                            <label class="form-label  mb-2">Effectivity Period</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label small">Start Date</label>
                                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label small">End Date</label>
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
                                    {{-- <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                        <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                        Create PO
                                    </button> --}}
                                    <button class="add-staff-btn btn-transition w3-disabled" disabled title="Maximum credit limit reached, kindly settle payment first.">
                                        <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">lock</span>
                                        Create PO
                                    </button>

                                </div>

                            @endif
                        </div>

                        <div class="status-btn">
                            <button class="pending">
                                <span>Pending</span>
                                <span>({{ $counts['Pending'] ?? 0 }}) </span>
                            </button>
                            <button>
                                <span>Accepted</span>
                                <span>({{ $counts['Accepted'] ?? 0 }}) </span>
                            </button>
                            <button>
                                <span>Progressing</span>
                                <span>({{ $counts['Progressing'] ?? 0 }} )</span>
                            </button>
                            <button>
                                <span>Completed</span>
                                <span>({{ $counts['Completed'] ?? 0 }} )</span>
                            </button>
                            <button>
                                <span>Cancelled</span>
                                <span>({{ $counts['Cancelled'] ?? 0 }} )</span>
                            </button>
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
                                    <th></th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($requests as $request)
                                    <tr onclick="window.location.href='{{ route('pr.request', ['po_id' => $request->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $request->created_at->format('F j, y') }}</td>
                                        <td>{{ $request->customer->company_name }}</td>
                                        <td>#{{ $request->po_id }}</td>
                                        <td>--</td>
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
                                        <td>{{ $request->status }}</td>
                        
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
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($requests as $request)
                                    <tr onclick="window.location.href='{{ route('pr.request', ['po_id' => $request->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $request->updated_at->format('F j, Y, g:i a') }}</td>
                                        <td>{{$request->po_id}}</td>
                                        <td>--</td>
                                        <td>--</td>
                                        <td>{{ $request->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                    </div>

                @endif

    </div>


@endsection

@push('scripts')

    <script src="{{ asset('js/pr/list.js') }}"></script>

@endpush
