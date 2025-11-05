@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
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
        .day-checkbox-container {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 140px;
        }
        
        .day-checkbox-container:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        
        .day-checkbox-container.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        
        .day-checkbox-container input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }
        
        .day-label {
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        #productsTable input[type="number"] {
            text-align: right;
        }

        .note-notify {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px;
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate next 14 days for selection
            generatePreferredDays();
            
            // Setup event listeners
            setupProductCheckboxes();
            setupQuantityInputs();
            setupFormValidation();
        });

        function generatePreferredDays() {
            const container = document.getElementById('preferredDaysContainer');
            const today = new Date();
            
            for (let i = 1; i <= 14; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                
                const dayDiv = document.createElement('div');
                dayDiv.className = 'day-checkbox-container';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'preferred_days[]';
                checkbox.value = date.toISOString().split('T')[0];
                checkbox.id = `day_${i}`;
                checkbox.className = 'form-check-input preferred-day-checkbox';
                
                const label = document.createElement('label');
                label.htmlFor = `day_${i}`;
                label.className = 'day-label';
                label.textContent = date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    weekday: 'short'
                });
                
                dayDiv.appendChild(checkbox);
                dayDiv.appendChild(label);
                
                // Click handler for the container
                dayDiv.addEventListener('click', function(e) {
                    if (e.target !== checkbox) {
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
                
                // Change handler for checkbox
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        dayDiv.classList.add('selected');
                    } else {
                        dayDiv.classList.remove('selected');
                    }
                    validateForm();
                });
                
                container.appendChild(dayDiv);
            }
        }

        function setupProductCheckboxes() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const headsInput = row.querySelector('.planned-heads');
                    const kilosInput = row.querySelector('.planned-kilos');
                    
                    if (this.checked) {
                        headsInput.disabled = false;
                        kilosInput.disabled = false;
                        row.style.backgroundColor = '#f8f9fa';
                    } else {
                        headsInput.disabled = true;
                        kilosInput.disabled = true;
                        headsInput.value = '';
                        kilosInput.value = '';
                        row.style.backgroundColor = '';
                        updateRowTotal(row);
                    }
                    
                    validateForm();
                });
            });
        }

        function setupQuantityInputs() {
            const headsInputs = document.querySelectorAll('.planned-heads');
            const kilosInputs = document.querySelectorAll('.planned-kilos');
            
            [...headsInputs, ...kilosInputs].forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    updateRowTotal(row);
                    validateForm();
                });
            });
        }

        function updateRowTotal(row) {
            const checkbox = row.querySelector('.product-checkbox');
            const headsInput = row.querySelector('.planned-heads');
            const kilosInput = row.querySelector('.planned-kilos');
            const totalCell = row.querySelector('.row-total');
            const price = parseFloat(checkbox.dataset.price) || 0;
            const measurement = checkbox.dataset.measurement;
            
            let total = 0;
            
            if (measurement === 'Heads') {
                const heads = parseFloat(headsInput.value) || 0;
                total = heads * price;
            } else if (measurement === 'Kilos') {
                const kilos = parseFloat(kilosInput.value) || 0;
                total = kilos * price;
            }
            
            totalCell.textContent = '₱' + total.toFixed(2);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            const rows = document.querySelectorAll('#productsTable tbody tr');
            let grandTotal = 0;
            
            rows.forEach(row => {
                const checkbox = row.querySelector('.product-checkbox');
                if (checkbox.checked) {
                    const totalText = row.querySelector('.row-total').textContent;
                    const total = parseFloat(totalText.replace('₱', '').replace(',', '')) || 0;
                    grandTotal += total;
                }
            });
            
            document.getElementById('grandTotal').textContent = '₱' + grandTotal.toFixed(2);
        }

        function validateForm() {
            const selectedDays = document.querySelectorAll('.preferred-day-checkbox:checked');
            const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
            const submitBtn = document.getElementById('submitBtn');
            const daysError = document.getElementById('daysError');
            const productsError = document.getElementById('productsError');
            
            let isValid = true;
            
            // Validate days
            if (selectedDays.length === 0) {
                daysError.textContent = 'Please select at least one delivery day';
                daysError.style.display = 'block';
                isValid = false;
            } else {
                daysError.style.display = 'none';
            }
            
            // Validate products
            if (selectedProducts.length === 0) {
                productsError.textContent = 'Please select at least one product';
                productsError.style.display = 'block';
                isValid = false;
            } else {
                // Check if selected products have quantities
                let hasQuantities = false;
                selectedProducts.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const heads = parseFloat(row.querySelector('.planned-heads').value) || 0;
                    const kilos = parseFloat(row.querySelector('.planned-kilos').value) || 0;
                    
                    if (heads > 0 || kilos > 0) {
                        hasQuantities = true;
                    }
                });
                
                if (!hasQuantities) {
                    productsError.textContent = 'Please enter quantities for selected products';
                    productsError.style.display = 'block';
                    isValid = false;
                } else {
                    productsError.style.display = 'none';
                }
            }
            
            submitBtn.disabled = !isValid;
        }
    </script>
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

                    <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                        <p class="heading">Purchase requests</p>
                        @if ( auth()->user()->role === 'Customer')
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Create purchase request
                            </button>
                        @endif

                    </div>

                </div>


                @if (auth()->user()->role !== 'Customer')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Timestamp</th>
                                    <th>PO ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($requests as $request)
                                    {{-- <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;"> --}}
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
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
                                    <th>Timestamp</th>
                                    <th>PO ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($requests as $request)
                                    {{-- <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;"> --}}
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $request->created_at->format('F j, Y') }}</td>
                                        <td>{{$request->po_id}}</td>
                                        <td>{{ $request->customer->company}}</td>
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


@endpush
