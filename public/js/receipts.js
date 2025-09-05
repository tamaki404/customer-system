setTimeout(function () {
    document.querySelectorAll('#alert-notify').forEach(flash => {
        flash.style.transition = "opacity 0.5s ease";
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    });
}, 3000);

document.addEventListener('DOMContentLoaded', () => {
    const paymentInput = document.getElementById('payment_method');
    const invoiceInput = document.getElementById('invoice_number');
    const input = document.getElementById('total_amount');

    input.addEventListener('input', () => {
    let rawValue = input.value.replace(/[^0-9.]/g, '');
    const parts = rawValue.split('.');
                                    
    let formatted = Number(parts[0]).toLocaleString('en-US');
                                    
        if (parts.length > 1) {
            formatted += '.' + parts[1].slice(0, 2);
        }
        input.value = formatted;
    });


    function toggleInvoiceField() {
        const method = paymentInput.value.trim().toLowerCase();

        if (method === 'gcash' || method === 'paymaya') {
            invoiceInput.disabled = false;
            invoiceInput.required = true;   
        } else {
            invoiceInput.disabled = true;
            invoiceInput.required = false;  
            invoiceInput.value = '';        
        }
    }

    toggleInvoiceField(); 
    paymentInput.addEventListener('change', toggleInvoiceField);
});
              