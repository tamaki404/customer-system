/**
 * Purchase Order Creation - Product Selection and Calculation with Credit Limit Validation
 * Handles product selection, quantity input, and total calculations with sale quantity limits
 * Displays real-time credit limit warnings
 */

let selectedProducts = new Map();

// Credit limit variables - these should be passed from your Blade template
let creditLimit = 0;
let usedCredit = 0;
let maxAllowed = 0;

/**
 * Initialize credit limit data from the page
 * Call this function in your Blade template with the actual values
 */
function initializeCreditData(limit, used, max) {
    creditLimit = limit;
    usedCredit = used;
    maxAllowed = max;
    updateSummary(); // Update display immediately after initialization
}

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
            headsInput.disabled = false;
            headsInput.value = 1;
            headsInput.required = true;
            kilosInput.disabled = false;
            kilosInput.value = 1;
            kilosInput.required = true;
            
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                kilosInput.setAttribute('max', saleQuantity);
            }
        } else if (measurementType === 'Kilos') {
            kilosInput.disabled = false;
            kilosInput.value = 1;
            kilosInput.required = true;
            headsInput.disabled = true;
            headsInput.value = 0;
            headsInput.required = false;
            
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                kilosInput.setAttribute('max', saleQuantity);
            }
        } else if (measurementType === 'Heads') {
            headsInput.disabled = false;
            headsInput.value = 1;
            headsInput.required = true;
            kilosInput.disabled = true;
            kilosInput.value = 0;
            kilosInput.required = false;
            
            if (onSale && !isNaN(saleQuantity) && saleQuantity > 0) {
                headsInput.setAttribute('max', saleQuantity);
            }
        }

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

    if (measurementType === 'Heads&Kilos' || measurementType === 'Kilos') {
        total = price * kilos;
    } else if (measurementType === 'Heads') {
        total = price * heads;
    }

    totalSpan.textContent = `₱${total.toFixed(2)}`;

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
    
    const warningDiv = document.createElement('div');
    warningDiv.className = 'alert alert-warning';
    warningDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; animation: slideIn 0.3s;';
    warningDiv.innerHTML = `
        <strong>Sale Limit Reached!</strong><br>
        Maximum ${maxQuantity} ${unitText} available for <strong>${productName}</strong>
    `;
    
    document.body.appendChild(warningDiv);
    
    setTimeout(() => {
        warningDiv.style.animation = 'slideOut 0.3s';
        setTimeout(() => warningDiv.remove(), 300);
    }, 3000);
}

/**
 * Update summary section with credit limit validation
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

    // Update selected count and grand total
    document.getElementById('selectedCount').textContent = selectedProducts.size;
    document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    });

    // Calculate credit usage - following the controller logic
    const totalCreditUsage = usedCredit + grandTotal; // newUsage = usedCredit + totalAmount
    const availableCredit = maxAllowed - usedCredit;
    const remaining = maxAllowed - totalCreditUsage;

    // Update credit display elements
    updateCreditDisplay(grandTotal, totalCreditUsage, availableCredit, remaining);

    // Update submit button state - following the controller logic
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;

    // Following: if ($newUsage > $maxAllowed) { throw exception }
    if (totalCreditUsage > maxAllowed) {
        submitBtn.disabled = true;
        submitBtn.innerText = "Exceeds Credit Limit";
        submitBtn.classList.add('btn-danger');
        submitBtn.classList.remove('btn-primary', 'btn-warning');
    } else if (grandTotal > availableCredit) {
        // Warning state - over available but within max allowed
        submitBtn.disabled = false;
        submitBtn.innerText = "⚠️ Create Order (Over Available Credit)";
        submitBtn.classList.add('btn-warning');
        submitBtn.classList.remove('btn-primary', 'btn-danger');
    } else {
        submitBtn.disabled = selectedProducts.size === 0;
        submitBtn.innerText = "Create Purchase Order";
        submitBtn.classList.remove('btn-warning', 'btn-danger');
        submitBtn.classList.add('btn-primary');
    }
}

/**
 * Update credit limit display with color-coded warnings
 */
/**
 * Update credit limit display with color-coded warnings
 */
function updateCreditDisplay(orderTotal, totalUsage, available, remaining) {
    const creditInfoDiv = document.getElementById('creditInfo');
    if (!creditInfoDiv) return;

    const percentUsed = (totalUsage / maxAllowed) * 100;
    let statusClass = 'text-success';
    let statusIcon = '✓';
    let statusText = 'Within Limit';

    // Following the controller validation logic
    if (totalUsage > maxAllowed) {
        statusClass = 'text-danger';
        statusIcon = '✗';
        statusText = 'EXCEEDS LIMIT';
    } else if (percentUsed > 90) {
        statusClass = 'text-warning';
        statusIcon = '⚠';
        statusText = 'Near Limit';
    }

    creditInfoDiv.innerHTML = `
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid ${statusClass === 'text-danger' ? '#dc3545' : statusClass === 'text-warning' ? '#ffc107' : '#28a745'};">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong>Credit Status</strong>
                <span class="${statusClass}" style="font-weight: bold;">${statusIcon} ${statusText}</span>
            </div>
            <div style="font-size: 13px; color: #666;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Credit Limit:</span>
                    <span style="font-weight: 500;">₱${creditLimit.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Max Allowed (+20%):</span>
                    <span style="font-weight: 500;">₱${maxAllowed.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Current Outstanding:</span>
                    <span style="font-weight: 500;">₱${usedCredit.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Available Credit:</span>
                    <span style="font-weight: 500; color: ${available < 0 ? '#dc3545' : '#28a745'};">₱${Math.max(0, available).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>This Order:</span>
                    <span style="font-weight: 500; color: #007bff;">₱${orderTotal.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <hr style="margin: 10px 0; border-color: #ddd;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-weight: bold;">Total After Order:</span>
                    <span style="font-weight: bold;" class="${statusClass}">₱${totalUsage.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: bold;">Remaining Credit:</span>
                    <span style="font-weight: bold;" class="${remaining < 0 ? 'text-danger' : 'text-success'}">
                        ₱${Math.max(0, remaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}
                    </span>
                </div>
            </div>
            
            ${orderTotal > available && totalUsage <= maxAllowed ? `
                <p style="margin-top: 10px; margin-bottom: 0; padding: 10px; background: #fff3cd; border-radius: 4px; border: 1px solid #ffc107; font-size: 13px;">
                    <strong style="color: #856404;">⚠️ Notice:</strong>
                    <span style="color: #856404;">This order (₱${orderTotal.toLocaleString('en-PH', {minimumFractionDigits: 2})}) exceeds your available credit (₱${available.toLocaleString('en-PH', {minimumFractionDigits: 2})}) by <strong>₱${(orderTotal - available).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>, but is still within your maximum allowed limit.</span>
                </p>
            ` : ''}
            
            ${totalUsage > maxAllowed ? `
                <p style="margin-top: 10px; margin-bottom: 0; padding: 10px; background: #f8d7da; border-radius: 4px; border: 1px solid #dc3545; font-size: 13px;">
                    <strong style="color: #721c24;">⛔ Error:</strong>
                    <span style="color: #721c24;">This order exceeds your maximum allowed credit by <strong>₱${(totalUsage - maxAllowed).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>. Please reduce your order amount.</span>
                </p>
            ` : ''}
        </div>
    `;
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

        // Validate quantities based on measurement type
        if (data.measurementType === 'Heads&Kilos') {
            if (data.kilos <= 0 || data.heads <= 0) {
                errors.push(`${productName} requires both heads and kilos to be greater than 0.`);
                hasErrors = true;
            }
        } else if (data.measurementType === 'Kilos') {
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

    // Validate credit limit - matching controller logic
    let grandTotal = 0;
    selectedProducts.forEach((data) => {
        if (data.measurementType === 'Heads&Kilos' || data.measurementType === 'Kilos') {
            grandTotal += data.price * data.kilos;
        } else if (data.measurementType === 'Heads') {
            grandTotal += data.price * data.heads;
        }
    });

    const newUsage = usedCredit + grandTotal;
    if (newUsage > maxAllowed) {
        errors.push(`This purchase will exceed your available credit capacity. Maximum allowed: ₱${maxAllowed.toLocaleString('en-PH', {minimumFractionDigits: 2})}`);
        hasErrors = true;
    }

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
    // Add CSS for animations
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