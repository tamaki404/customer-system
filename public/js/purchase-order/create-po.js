/**
 * Purchase Order Creation - Product Selection and Calculation
 * Handles product selection, quantity input, and total calculations
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
        } else if (measurementType === 'Kilos') {
            // Only kilos for Kilos products
            kilosInput.disabled = false;
            kilosInput.value = 1;
            kilosInput.required = true;
            headsInput.disabled = true;
            headsInput.value = 0;
            headsInput.required = false;
        } else if (measurementType === 'Heads') {
            // Only heads for Heads products
            headsInput.disabled = false;
            headsInput.value = 1;
            headsInput.required = true;
            kilosInput.disabled = true;
            kilosInput.value = 0;
            kilosInput.required = false;
        }

        // Store product data
        selectedProducts.set(setId, {
            productId: row.dataset.productId,
            price: parseFloat(row.dataset.price),
            originalPrice: parseFloat(row.dataset.originalPrice),
            onSale: row.dataset.onSale === 'true',
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

    const price = parseFloat(row.dataset.price); // Already includes sale price if active
    const heads = parseInt(headsInput.value) || 0;
    const kilos = parseFloat(kilosInput.value) || 0;

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
 * Update summary section (selected count and grand total)
 */
function updateSummary() {
    let grandTotal = 0;

    selectedProducts.forEach((data) => {
        // For Heads&Kilos or Kilos: use kilos
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
    if (submitBtn) {
        submitBtn.disabled = selectedProducts.size === 0;
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
    // Find the form (adjust selector if needed)
    const form = document.querySelector('form[action*="purchaseorders.create"]');
    
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }

    // Initialize summary on page load
    updateSummary();
});