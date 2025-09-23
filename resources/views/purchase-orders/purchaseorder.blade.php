
@extends('layouts.main')


@push('styles')
@endpush



@section('content')


    {{-- purchase order placing --}}
    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
        <div class="modal-dialog" style="width: auto">
            <form class="modal-content" method="POST" enctype="multipart/form-data" style="width: 800px" action="{{ route('purchaseorders.place', $po->po_id) }}">
                @csrf
                
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
                
                @if (session('success'))
                    <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin: 10px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                        <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Hidden field for PO ID -->
                <input type="hidden" name="po_id" value="{{ $po->po_id }}">
                <input type="hidden" name="supplier_id" value="{{ $po->supplier_id }}">


                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Place purchase order</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                

                
                <div class="modal-body" style="width: auto">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>Any changes will be logged.</span>
                    </p>

                    <div style="width: auto">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                            <thead style="background-color: #f9f9f9;">
                                <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                    <td>Select</td>
                                    <td>#</td>
                                    <td>Product ID</td>
                                    <td>Product name</td>
                                    <td>Category</td>
                                    <td>Unit</td>
                                    <td>Weight</td>
                                    <td>Price</td>
                                    <td>Quantity</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($setProducts as $setProduct)
                                <tr class="product-row" data-set-id="{{ $setProduct->set_id }}" 
                                    data-product-id="{{ $setProduct->product->product_id }}" 
                                    data-price="{{ $setProduct->price }}">
                                    <td class="checkbox-cell">
                                        <input type="checkbox" 
                                            name="selected_products[]" 
                                            value="{{ $setProduct->set_id }}"
                                            class="product-checkbox"
                                            onchange="toggleProductRow(this, '{{ $setProduct->set_id }}')">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $setProduct->set_id }}</td>
                                    <td>{{ $setProduct->product->name }}</td>
                                    <td>{{ $setProduct->product->category }}</td>
                                    <td>{{ $setProduct->product->unit }}</td>
                                    <td>{{ $setProduct->product->weight }}</td>
                                    <td>${{ $setProduct->price }}</td>
                                    <td>
                                        <input type="number" 
                                            name="quantities[{{ $setProduct->set_id }}]" 
                                            value="1" 
                                            min="1"
                                            class="form-control quantity-input"
                                            onchange="calculateRowTotal('{{ $setProduct->set_id }}')"
                                            disabled>
                                    </td>
                                    <td>
                                        <span id="total_{{ $setProduct->set_id }}" class="row-total">${{ number_format($setProduct->price, 2) }}</span>
                                    </td>
                                    <!-- Hidden fields for backend validation -->
                                    <input type="hidden" name="product_ids[{{ $setProduct->set_id }}]" value="{{ $setProduct->product->product_id }}">
                                    <input type="hidden" name="unit_prices[{{ $setProduct->set_id }}]" value="{{ $setProduct->price }}">
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                 
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Finalize Order</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('purchaseorders.list') }}">< Purchase orders list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Purchase order</p>
                @if ( auth()->user()->role !== 'Supplier')
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Place this order</button>
                    </div>
                @endif
            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="purchase-order-div" style="display: flex; flex-direction: row; gap: 20px">
                <div class="po-image-div">
                    @php
                        $imgSrc = $po->image 
                            ? ('data:' . $po->image_mime_type . ';base64,' . base64_encode($po->image))
                            : asset('assets/default-image.jpg');
                    @endphp
                    <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image"> 
                </div>


            </div>

       
        </div>


   </div>

@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Track selected products and their data
    let selectedProducts = new Map();

    function toggleAllProducts() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        
        productCheckboxes.forEach(checkbox => {
            if (checkbox.checked !== selectAllCheckbox.checked) {
                checkbox.checked = selectAllCheckbox.checked;
                toggleProductRow(checkbox, checkbox.value);
            }
        });
    }

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
            document.getElementById(`total_${setId}`).textContent = '$0.00';
        }
        
        updateSummary();
        updateHiddenFields();
        updateSelectAllState();
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
            
            totalSpan.textContent = `$${total.toFixed(2)}`;
            
            // Update stored data
            if (selectedProducts.has(setId)) {
                selectedProducts.get(setId).quantity = quantity;
            }
            
            updateSummary();
        } else {
            totalSpan.textContent = '$0.00';
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
        document.getElementById('grandTotal').textContent = `$${grandTotal.toFixed(2)}`;
        document.getElementById('submitCount').textContent = selectedProducts.size;
        
        // Enable/disable submit button
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = selectedProducts.size === 0;
    }

    function updateHiddenFields() {
        const hiddenFieldsContainer = document.getElementById('hiddenFields');
        hiddenFieldsContainer.innerHTML = '';
        
        selectedProducts.forEach((data, setId) => {
            // Add hidden field for product_id
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `product_ids[${setId}]`;
            productIdInput.value = data.productId;
            hiddenFieldsContainer.appendChild(productIdInput);
            
            // Add hidden field for unit_price
            const unitPriceInput = document.createElement('input');
            unitPriceInput.type = 'hidden';
            unitPriceInput.name = `unit_prices[${setId}]`;
            unitPriceInput.value = data.price;
            hiddenFieldsContainer.appendChild(unitPriceInput);
        });
    }

    function updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === productCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Form validation before submit
    function validateForm() {
        if (selectedProducts.size === 0) {
            showError(['Please select at least one product.']);
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
            showError(errors);
            return false;
        }
        
        return true;
    }

    function showError(errors) {
        const errorContainer = document.getElementById('errorContainer');
        const errorList = document.getElementById('errorList');
        
        errorList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        
        errorContainer.style.display = 'block';
        
        // Hide after 5 seconds
        setTimeout(() => {
            errorContainer.style.display = 'none';
        }, 5000);
    }

    function showSuccess(message) {
        const successContainer = document.getElementById('successContainer');
        successContainer.textContent = message;
        successContainer.style.display = 'block';
        
        // Hide after 5 seconds
        setTimeout(() => {
            successContainer.style.display = 'none';
        }, 5000);
    }

    // Add form submit event listener
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSummary();
        updateSelectAllState();
    });
</script>

<!-- Demo button to open modal -->
<div class="container mt-5">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modify-action">
        Open Purchase Order Modal
    </button>
</div>
@endpush