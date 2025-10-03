@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')

    {{-- create order --}}
    @if (auth()->user()->role === 'Supplier')
        <div class="modal fade" id="create-order-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form class="modal-content" method="POST"  style="width: 800px" action="{{ route('purchaseorders.create') }}">
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
                        <p class="modal-title" id="requestActionLabel">Create Purchase Order</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Select products from your available inventory and specify quantities.</span>
                        </p>

                        <div class="form-group" style="margin-bottom: 20px; flex-direction: column; display: flex;">
                            <label for="notes">Notes (Optional)</label>
                            <input name="notes" style="font-size: 14px" id="notes" rows="3" placeholder="Add any additional notes for this purchase order...">
                        </div>

                        <div style="overflow-x: auto;">
                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                <thead style="background-color: #f9f9f9;">
                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                        <td>Select</td>
                                        <td>#</td>
                                        <td>Product Name</td>
                                        <td>Category</td>
                                        <td>Unit</td>
                                        <td>Weight</td>
                                        <td>Price</td>
                                        <td>Quantity</td>
                                        <td>Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $supplier = auth()->user()->supplier;
                                        $setProducts = \App\Models\ProductSetting::where('supplier_id', $supplier->supplier_id)
                                            ->with('product')
                                            ->get();
                                    @endphp
                                    @foreach($setProds as $setProd)
                                    <tr class="product-row" 
                                        data-set-id="{{ $setProd->set_id }}" 
                                        data-product-id="{{ $setProd->product->product_id }}" 
                                        data-price="{{ $setProd->nego_price }}">

                                        <td class="checkbox-cell">
                                            <input type="checkbox" 
                                                name="selected_products[]" 
                                                value="{{ $setProd->set_id }}"
                                                class="product-checkbox"
                                                onchange="toggleProductRow(this, '{{ $setProd->set_id }}')">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $setProd->product->name }}</td>
                                        <td>{{ $setProd->product->category }}</td>
                                        <td>{{ $setProd->product->unit }}</td>
                                        <td>{{ $setProd->product->weight }}</td>
                                        <td>
                                            @if($setProd->on_sale)
                                                <span style="text-decoration: line-through; color: #888;">
                                                    ₱{{ number_format($setProd->original_price, 2) }}
                                                </span>
                                                <span style="color: #fe8d29; font-weight: bold; margin-left: 5px;">
                                                    ₱{{ number_format($setProd->nego_price, 2) }}
                                                </span>
                                            @else
                                                ₱{{ number_format($setProd->nego_price, 2) }}
                                            @endif
                                        </td>

                                        <td>
                                            <input type="number" 
                                                name="quantities[{{ $setProd->set_id }}]" 
                                                value="1" 
                                                min="1"
                                                class="form-control quantity-input"
                                                onchange="calculateRowTotal('{{ $setProd->set_id }}')"
                                                disabled>
                                        </td>
                                        <td>
                                            <span id="total_{{ $setProd->set_id }}" class="row-total">
                                                ₱{{ number_format($setProd->nego_price, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong>Selected Items: <span id="selectedCount">0</span></strong>
                                </div>
                                <div>
                                    <strong>Grand Total: <span id="grandTotal">₱0.00</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Create Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by PO ID, Supplier, Status"
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
                        <p class="heading">Purchase order list</p>
                        @if ( auth()->user()->role === 'Supplier')
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Create order
                            </button>
                        @endif

                    </div>

                </div>


                @if (auth()->user()->role !== 'Supplier')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>PO ID</th>
                                    <th>Supplier</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Confirmed By</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($pos as $po)
                                    <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $po->created_at->format('F j, Y') }}</td>
                                        <td>{{$po->po_id}}</td>
                                        <td>{{ $po->supplier->company_name ?? 'N/A' }}</td>
                                        <td>{{ $po->items->count() }}</td>
                                        <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                                @if($po->status === 'Pending') background-color: #fff3cd; color: #856404;
                                                @elseif($po->status === 'Accepted') background-color: #d4edda; color: #155724;
                                                @else background-color: #f8d7da; color: #721c24;
                                                @endif">
                                                {{$po->status}}
                                            </span>
                                        </td>
                                        <td>{{ $po->staff ? $po->staff->first_name . ' ' . $po->staff->last_name : '-' }}</td>
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
                                    <th>PO ID</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Confirmed By</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($pos as $po)
                                    <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $po->created_at->format('F j, Y') }}</td>
                                        <td>{{$po->po_id}}</td>
                                        <td>{{ $po->items->count() }}</td>
                                        <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                                @if($po->status === 'Pending') background-color: #fff3cd; color: #856404;
                                                @elseif($po->status === 'Accepted') background-color: #d4edda; color: #155724;
                                                @else background-color: #f8d7da; color: #721c24;
                                                @endif">
                                                {{$po->status}}
                                            </span>
                                        </td>
                                        <td>{{ $po->staff ? $po->staff->first_name . ' ' . $po->staff->last_name : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                    </div>

                @endif

        </div>


@endsection

@push('scripts')
<script>
    // Track selected products and their data
    let selectedProducts = new Map();

    function toggleProductRow(checkbox, setId) {
        const row = document.querySelector(`tr[data-set-id="${setId}"]`);
        const quantityInput = document.querySelector(`input[name="quantities[${setId}]"]`);
        
        if (checkbox.checked) {
            // Enable the row
            row.classList.remove('disabled');
            quantityInput.disabled = false;
            quantityInput.value = quantityInput.value || 1;
            
            // Store product data
            selectedProducts.set(setId, {
                productId: row.dataset.productId,
                price: parseFloat(row.dataset.price),
                quantity: parseInt(quantityInput.value) || 1
            });
            
            calculateRowTotal(setId);
        } else {
            // Disable the row
            row.classList.add('disabled');
            quantityInput.disabled = true;
            
            // Remove from selected products
            selectedProducts.delete(setId);
            
            // Reset total
            document.getElementById(`total_${setId}`).textContent = '₱0.00';
        }
        
        updateSummary();
    }

    function calculateRowTotal(setId) {
        const quantityInput = document.querySelector(`input[name="quantities[${setId}]"]`);
        const totalSpan = document.getElementById(`total_${setId}`);
        const checkbox = document.querySelector(`input[name="selected_products[]"][value="${setId}"]`);
        
        if (checkbox.checked && quantityInput && !quantityInput.disabled) {
            const row = document.querySelector(`tr[data-set-id="${setId}"]`);
            const price = parseFloat(row.dataset.price);
            const quantity = parseInt(quantityInput.value) || 0;
            const total = price * quantity;
            
            totalSpan.textContent = `₱${total.toFixed(2)}`;
            
            // Update stored data
            if (selectedProducts.has(setId)) {
                selectedProducts.get(setId).quantity = quantity;
            }
            
            updateSummary();
        } else {
            totalSpan.textContent = '₱0.00';
        }
    }

    function updateSummary() {
        let totalItems = 0;
        let grandTotal = 0;
        
        selectedProducts.forEach((data, setId) => {
            const quantityInput = document.querySelector(`input[name="quantities[${setId}]"]`);
            const currentQuantity = parseInt(quantityInput.value) || 0;
            
            totalItems += currentQuantity;
            grandTotal += data.price * currentQuantity;
        });
        
        document.getElementById('selectedCount').textContent = selectedProducts.size;
        document.getElementById('grandTotal').textContent = `₱${grandTotal.toFixed(2)}`;
        
        // Enable/disable submit button
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = selectedProducts.size === 0;
    }

    // Form validation before submit
    function validateForm() {
        if (selectedProducts.size === 0) {
            alert('Please select at least one product.');
            return false;
        }
        
        let hasErrors = false;
        const errors = [];
        
        selectedProducts.forEach((data, setId) => {
            const quantityInput = document.querySelector(`input[name="quantities[${setId}]"]`);
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (quantity <= 0) {
                errors.push(`Quantity for product ${setId} must be greater than 0.`);
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            alert(errors.join('\n'));
            return false;
        }
        
        return true;
    }

    // Add form submit event listener
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#create-order-modal form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        updateSummary();
    });
</script>
@endpush
