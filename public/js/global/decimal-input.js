document.addEventListener('DOMContentLoaded', function() {
    const decimalInputs = document.querySelectorAll('.decimal-input');
    
    decimalInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            
            // Allow dot (.) - ASCII 46 or key "."
            if (e.key === '.' || e.keyCode === 190 || e.keyCode === 110) {
                // Only allow one dot
                if (this.value.indexOf('.') !== -1) {
                    e.preventDefault();
                }
                return;
            }
            
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        // Replace comma with dot on paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const cleaned = pastedData.replace(/,/g, '.').replace(/[^\d.]/g, '');
            
            // Ensure only one dot
            const parts = cleaned.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            } else {
                this.value = cleaned;
            }
        });
    });
});