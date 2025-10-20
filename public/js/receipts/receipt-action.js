document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('statusSelect');
    const verifiedSection = document.getElementById('verifiedSection');
    const rejectedSection = document.getElementById('rejectedSection');
    const submitBtn = document.getElementById('submitBtn');
    const amountInput = document.getElementById('amountInput');
    const reasonInput = document.getElementById('reasonInput');
    const remarksInput = document.getElementById('remarksInput');
    const remainingAmountElement = document.getElementById('remainingAmount');
    const remainingAmount = remainingAmountElement ? parseFloat(remainingAmountElement.value) : 0;

    // Auto-format amount input with thousand separators
    if (amountInput) {
        // Change input type to text to support comma formatting
        amountInput.type = 'text';
        
        amountInput.addEventListener('input', function (e) {
            // Get cursor position
            const cursorPos = e.target.selectionStart;
            const oldValue = e.target.value;
            const oldLength = oldValue.length;
            
            // Remove all commas to get raw value
            let value = e.target.value.replace(/,/g, '');
            
            // Remove any non-digit and non-decimal characters
            value = value.replace(/[^\d.]/g, '');
            
            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Limit to 2 decimal places
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            
            // Validate against remaining amount
            const numValue = parseFloat(value);
            if (numValue > remainingAmount) {
                value = remainingAmount.toFixed(2);
            }
            
            // Format with thousand separators
            if (value) {
                const valueParts = value.split('.');
                valueParts[0] = valueParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                e.target.value = valueParts.join('.');
                
                // Restore cursor position accounting for added/removed commas
                const newLength = e.target.value.length;
                const newCursorPos = cursorPos + (newLength - oldLength);
                e.target.setSelectionRange(newCursorPos, newCursorPos);
            } else {
                e.target.value = '';
            }
        });

        // Format on blur (when user leaves the field)
        amountInput.addEventListener('blur', function (e) {
            const value = e.target.value.replace(/,/g, '');
            if (value && !isNaN(value) && value !== '') {
                const numValue = parseFloat(value);
                const formatted = numValue.toFixed(2);
                const parts = formatted.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                e.target.value = parts.join('.');
            }
        });

        // Remove commas before form submission to ensure proper value
        const form = amountInput.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                amountInput.value = amountInput.value.replace(/,/g, '');
            });
        }
    }

    if (!statusSelect) return;

    statusSelect.addEventListener('change', function () {
        const status = this.value;

        // Hide both sections
        if (verifiedSection) verifiedSection.style.display = 'none';
        if (rejectedSection) rejectedSection.style.display = 'none';
        if (submitBtn) submitBtn.disabled = true;

        // Clear fields
        if (amountInput) amountInput.value = '';
        if (reasonInput) reasonInput.value = '';
        if (remarksInput) remarksInput.value = '';

        if (amountInput) amountInput.removeAttribute('required');
        if (reasonInput) reasonInput.removeAttribute('required');

        if (status === 'Verified') {
            if (verifiedSection) verifiedSection.style.display = 'block';

            if (remainingAmount <= 0) {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Order Fully Paid';
                    submitBtn.className = 'btn btn-secondary';
                }
                if (amountInput) amountInput.disabled = true;
            } else {
                if (amountInput) {
                    amountInput.setAttribute('required', 'required');
                    amountInput.disabled = false;
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Verify Receipt';
                    submitBtn.className = 'btn btn-success';
                }
            }
        } else if (status === 'Rejected') {
            if (rejectedSection) rejectedSection.style.display = 'block';
            if (reasonInput) reasonInput.setAttribute('required', 'required');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reject Receipt';
                submitBtn.className = 'btn btn-danger';
            }
        }
    });
});