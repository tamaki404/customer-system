// Multi-step form validation script
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    
    // Add CSS for validation alert
    const style = document.createElement('style');
    style.textContent = `
        .alert-primary {
            background-color: #cfe2ff;
            color: #084298;
            border-left-color: #0d6efd;
            padding: 15px 20px;
            border-radius: 5px;
            border-left: 4px solid;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        
        .btn-close:hover {
            opacity: 0.7;
        }
        
        .validation-error {
            border: 2px solid #dc3545 !important;
            background-color: #fff5f5 !important;
        }
    `;
    document.head.appendChild(style);
    
    // Function to get current visible step
    function getCurrentStep() {
        return document.querySelector('.step-section:not([style*="display: none"])');
    }
    
    // Function to validate current step
    function validateCurrentStep() {
        const currentStep = getCurrentStep();
        if (!currentStep) return true;
        
        // Remove existing alert
        const existingAlert = document.querySelector('.validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Remove previous error highlights
        document.querySelectorAll('.validation-error').forEach(el => {
            el.classList.remove('validation-error');
        });
        
        // Get all required inputs in current step
        const requiredInputs = currentStep.querySelectorAll('[required]');
        const emptyFields = [];
        
        requiredInputs.forEach(input => {
            let isEmpty = false;
            
            if (input.type === 'checkbox') {
                const name = input.name;
                if (name.includes('[]')) {
                    // Check if at least one checkbox in group is checked
                    const checkboxGroup = currentStep.querySelectorAll(`[name="${name}"]`);
                    const isAnyChecked = Array.from(checkboxGroup).some(cb => cb.checked);
                    if (!isAnyChecked && !emptyFields.find(f => f.name === name)) {
                        isEmpty = true;
                    }
                } else if (!input.checked) {
                    isEmpty = true;
                }
            } else if (input.type === 'file') {
                if (!input.files || input.files.length === 0) {
                    // Check for "use default" checkbox exception
                    const useDefaultCheckbox = document.getElementById('use-default');
                    if (input.id === 'company-image' && useDefaultCheckbox && useDefaultCheckbox.checked) {
                        isEmpty = false;
                    } else {
                        isEmpty = true;
                    }
                }
            } else if (input.type === 'radio') {
                const name = input.name;
                const radioGroup = currentStep.querySelectorAll(`[name="${name}"]`);
                const isAnyChecked = Array.from(radioGroup).some(rb => rb.checked);
                if (!isAnyChecked && !emptyFields.find(f => f.name === name)) {
                    isEmpty = true;
                }
            } else if (input.tagName === 'SELECT') {
                if (!input.value || input.value === '') {
                    isEmpty = true;
                }
            } else {
                if (!input.value || input.value.trim() === '') {
                    isEmpty = true;
                }
            }
            
            if (isEmpty) {
                const label = input.closest('.input-forms')?.querySelector('label');
                const fieldName = label ? label.textContent.replace('*', '').trim() : input.name;
                
                // Add error highlight
                input.classList.add('validation-error');
                
                emptyFields.push({
                    name: fieldName,
                    element: input
                });
            }
        });
        
        // If there are empty fields, show alert and prevent navigation
        if (emptyFields.length > 0) {
            showValidationAlert(emptyFields);
            return false;
        }
        
        return true;
    }
    
    // Function to show validation alert
    function showValidationAlert(emptyFields) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-primary validation-alert';
        alert.role = 'alert';
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 400px;
            max-width: 600px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        let alertContent = `
            <div style="display: flex; align-items: start; gap: 10px;">
                <span class="material-symbols-outlined" style="color: #084298;">info</span>
                <div style="flex: 1;">
                    <strong>Please fill in all required fields</strong>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        `;
        
        const displayFields = emptyFields.slice(0, 5);
        displayFields.forEach(field => {
            alertContent += `<li style="font-size: 14px;">${field.name}</li>`;
        });
        
        if (emptyFields.length > 5) {
            alertContent += `<li style="font-size: 14px;">...and ${emptyFields.length - 5} more field(s)</li>`;
        }
        
        alertContent += `
                    </ul>
                </div>
                <button type="button" class="btn-close" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #084298;">×</button>
            </div>
        `;
        
        alert.innerHTML = alertContent;
        
        // Add close button event
        alert.querySelector('.btn-close').addEventListener('click', function() {
            alert.remove();
        });
        
        document.body.insertBefore(alert, document.body.firstChild);
        
        // Focus on first empty field
        if (emptyFields[0].element) {
            emptyFields[0].element.focus();
            emptyFields[0].element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Auto-hide after 8 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 8000);
    }
    
    // Validate on Next button click
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            if (!validateCurrentStep()) {
                e.stopImmediatePropagation();
            }
        }, true); // Use capture phase to intercept before other handlers
    }
    
    // Validate on form submission (final step)
    form.addEventListener('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });
    
    // Clear error highlighting on input
    form.addEventListener('input', function(e) {
        if (e.target.classList.contains('validation-error')) {
            e.target.classList.remove('validation-error');
        }
    });
    
    // Clear error highlighting on change (for selects and checkboxes)
    form.addEventListener('change', function(e) {
        if (e.target.classList.contains('validation-error')) {
            e.target.classList.remove('validation-error');
        }
    });
});