/**
 * Purchase Order Creation - Product Selection and Calculation
 * Handles product selection, quantity input, and total calculations with sale quantity limits
 */

let selectedProducts = new Map();

/**
 * Toggle product row selection and enable/disable inputs
 */
function toggleProductRow(checkbox, setId) {
    const row = document.querySelector(`tr[data-set-id="${setId}"]`);
    const headsInput = document.querySelector(`input[name="placed_heads[${setId}]"]`);
    const kilosInput = document.querySelector(`input[name="placed_kilos[${setId}]"]`);
    const measurementType = row.dataset.measurementType;
    const onSale = row.dataset.onSale === 'true';
    const saleQuantity = parseFloat(row.dataset.saleQuantity);

    if (checkbox.checked) {
        row.classList.remove('disabled');

        // Enable inputs based on measurement type
        if (measurementType === 'Heads&Kilos') {
            // Enable BOTH inputs for Heads&Kilos
            headsInput.disabled = false;
            headsInput.value = 1;
            headsInput.required = true;
            kilosInput.disabled = false;
            kilosInput.value = 1;
            kilosInput.required = true;
            
            // Set max for kilos if on sale
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                kilosInput.setAttribute('max', saleQuantity);
            }
        } else if (measurementType === 'Kilos') {
            // Only kilos for Kilos products
            kilosInput.disabled = false;
            kilosInput.value = 1;
            kilosInput.required = true;
            headsInput.disabled = true;
            headsInput.value = 0;
            headsInput.required = false;
            
            // Set max for kilos if on sale
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                kilosInput.setAttribute('max', saleQuantity);
            }
        } else if (measurementType === 'Heads') {
            // Only heads for Heads products
            headsInput.disabled = false;
            headsInput.value = 1;
            headsInput.required = true;
            kilosInput.disabled = true;
            kilosInput.value = 0;
            kilosInput.required = false;
            
            // Set max for heads if on sale
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                headsInput.setAttribute('max', saleQuantity);
            }
        }

        // Store product data
        selectedProducts.set(setId, {
            productId: row.dataset.productId,
            price: parseFloat(row.dataset.price),
            originalPrice: parseFloat(row.dataset.originalPrice),
            onSale: onSale,
            saleQuantity: saleQuantity,
            measurementType: measurementType,
            heads: parseInt(headsInput.value) || 0,
            kilos: parseFloat(kilosInput.value) || 0
        });

        calculateRowTotal(setId);
    } else {
        row.classList.add('disabled');
        headsInput.disabled = true;
        kilosInput.disabled = true;
        headsInput.required = false;
        kilosInput.required = false;
        headsInput.value = 0;
        kilosInput.value = 0;
        
        // Remove max attributes
        headsInput.removeAttribute('max');
        kilosInput.removeAttribute('max');

        document.getElementById(`total_${setId}`).textContent = '₱0.00';
        selectedProducts.delete(setId);
    }

    updateSummary();
}

/**
 * Calculate individual row total based on measurement type
 */
function calculateRowTotal(setId) {
    const row = document.querySelector(`tr[data-set-id="${setId}"]`);
    const headsInput = document.querySelector(`input[name="placed_heads[${setId}]"]`);
    const kilosInput = document.querySelector(`input[name="placed_kilos[${setId}]"]`);
    const totalSpan = document.getElementById(`total_${setId}`);
    const checkbox = document.querySelector(`input[name="selected_products[]"][value="${setId}"]`);
    const measurementType = row.dataset.measurementType;

    if (!checkbox || !checkbox.checked) {
        totalSpan.textContent = '₱0.00';
        return;
    }

    const price = parseFloat(row.dataset.price);
    const onSale = row.dataset.onSale === 'true';
    const saleQuantity = parseFloat(row.dataset.saleQuantity);
    
    let heads = parseInt(headsInput.value) || 0;
    let kilos = parseFloat(kilosInput.value) || 0;

    // Validate against sale quantity limits
    if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
        if (measurementType === 'Heads' && heads > saleQuantity) {
            headsInput.value = saleQuantity;
            heads = saleQuantity;
            showSaleQuantityWarning(row, saleQuantity, 'heads');
        } else if ((measurementType === 'Kilos' || measurementType === 'Heads&Kilos') && kilos > saleQuantity) {
            kilosInput.value = saleQuantity;
            kilos = saleQuantity;
            showSaleQuantityWarning(row, saleQuantity, 'kilos');
        }
    }

    let total = 0;

    // For Heads&Kilos or Kilos: ALWAYS use kilos for calculation
    if (measurementType === 'Heads&Kilos' || measurementType === 'Kilos') {
        total = price * kilos;
    } else if (measurementType === 'Heads') {
        total = price * heads;
    }

    totalSpan.textContent = `₱${total.toFixed(2)}`;

    // Update stored product data
    if (selectedProducts.has(setId)) {
        selectedProducts.get(setId).heads = heads;
        selectedProducts.get(setId).kilos = kilos;
    }

    updateSummary();
}

/**
 * Show warning when user exceeds sale quantity
 */
function showSaleQuantityWarning(row, maxQuantity, unit) {
    const productName = row.querySelector('td:nth-child(3)').textContent.trim();
    const unitText = unit === 'heads' ? 'pcs' : 'kg';
    
    // Create temporary warning message
    const warningDiv = document.createElement('div');
    warningDiv.className = 'alert alert-warning';
    warningDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; animation: slideIn 0.3s;';
    warningDiv.innerHTML = `
        <strong>Sale Limit Reached!</strong><br>
        Maximum ${maxQuantity} ${unitText} available for <strong>${productName}</strong>
    `;
    
    document.body.appendChild(warningDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        warningDiv.style.animation = 'slideOut 0.3s';
        setTimeout(() => warningDiv.remove(), 300);
    }, 3000);
}

/**
 * Update summary section (selected count and grand total)
 */
function updateSummary() {
    let grandTotal = 0;

    selectedProducts.forEach((data) => {
        if (data.measurementType === 'Heads&Kilos' || data.measurementType === 'Kilos') {
            grandTotal += data.price * data.kilos;
        } else if (data.measurementType === 'Heads') {
            grandTotal += data.price * data.heads;
        }
    });

    document.getElementById('selectedCount').textContent = selectedProducts.size;
    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    });

    const submitBtn = document.getElementById('submitBtn');

    if (!submitBtn) return;

    if (grandTotal > maxAllowed) {
        submitBtn.disabled = true;
        submitBtn.innerText = "Exceeds Credit Limit";
        submitBtn.classList.add('btn-warning');
        submitBtn.classList.remove('btn-primary');
    } else {
        submitBtn.disabled = selectedProducts.size === 0;
        submitBtn.innerText = "Create Purchase Order";
        submitBtn.classList.remove('btn-warning');
        submitBtn.classList.add('btn-primary');
    }
}


/**
 * Validate form before submission
 */
function validateForm() {
    if (selectedProducts.size === 0) {
        alert('Please select at least one product.');
        return false;
    }

    let hasErrors = false;
    const errors = [];

    selectedProducts.forEach((data, setId) => {
        const row = document.querySelector(`tr[data-set-id="${setId}"]`);
        const productName = row ? row.querySelector('td:nth-child(3)').textContent.trim() : `Product ${setId}`;

        // Validate sale quantity limits
        if (data.onSale && !isNaN(data.saleQuantity) && data.saleQuantity > 0) {
            if (data.measurementType === 'Heads' && data.heads > data.saleQuantity) {
                errors.push(`${productName}: Maximum ${data.saleQuantity} pcs available for this sale.`);
                hasErrors = true;
            } else if ((data.measurementType === 'Kilos' || data.measurementType === 'Heads&Kilos') && data.kilos > data.saleQuantity) {
                errors.push(`${productName}: Maximum ${data.saleQuantity} kg available for this sale.`);
                hasErrors = true;
            }
        }

        // For Heads&Kilos or Kilos: only kilos is required
        if (data.measurementType === 'Heads&Kilos' || data.measurementType === 'Kilos') {
            if (data.kilos <= 0) {
                errors.push(`${productName} requires kilos to be greater than 0.`);
                hasErrors = true;
            }
        } else if (data.measurementType === 'Heads') {
            if (data.heads <= 0) {
                errors.push(`${productName} requires heads to be greater than 0.`);
                hasErrors = true;
            }
        }
    });

    if (hasErrors) {
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
        return false;
    }

    return true;
}

/**
 * Initialize event listeners when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function () {
    // Add CSS for warning animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Find the form
    const form = document.querySelector('form[action*="purchaseorders.create"]');
    
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }

    // Add real-time validation for inputs
    document.querySelectorAll('.heads-input, .kilos-input').forEach(input => {
        input.addEventListener('input', function() {
            const max = parseFloat(this.getAttribute('max'));
            if (max && parseFloat(this.value) > max) {
                this.value = max;
                const row = this.closest('tr');
                const setId = row.dataset.setId;
                calculateRowTotal(setId);
            }
        });
        
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const setId = row.dataset.setId;
            calculateRowTotal(setId);
        });
    });

    // Initialize summary on page load
    updateSummary();
});