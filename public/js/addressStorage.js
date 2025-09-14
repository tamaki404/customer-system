document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Function to prepare form data before submission
    function prepareFormSubmission() {
        // Add cart data as hidden input
        let cartInput = document.getElementById('cart_data');
        if (!cartInput) {
            cartInput = document.createElement('input');
            cartInput.type = 'hidden';
            cartInput.name = 'cart_data';
            cartInput.id = 'cart_data';
            orderForm.appendChild(cartInput);
        }
        
        // Add address data as hidden inputs
        addAddressDataToForm();
        
        return true;
    }

    // Function to add address data to form
    function addAddressDataToForm() {
        const addressFields = [
            'postal_code', 'region', 'province', 'municipality', 
            'barangay', 'street', 'company_name', 'billing_address',
            'receiver_name', 'receiver_mobile'
        ];

        addressFields.forEach(fieldName => {
            const fieldElement = document.getElementById(fieldName);
            if (fieldElement && fieldElement.value) {
                // Check if hidden input already exists
                let hiddenInput = orderForm.querySelector(`input[name="${fieldName}"]`);
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = fieldName;
                    orderForm.appendChild(hiddenInput);
                }
                hiddenInput.value = fieldElement.value;
            }
        });

        // Handle order notes textarea
        const orderNotes = document.querySelector('textarea[name="order_notes"]');
        if (orderNotes) {
            let hiddenInput = orderForm.querySelector(`input[name="order_notes"]`);
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'order_notes';
                orderForm.appendChild(hiddenInput);
            }
            hiddenInput.value = orderNotes.value;
        }
    }

    // Fix for localStorage compatibility
    function isLocalStorageAvailable() {
        try {
            const test = '__localStorage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch(e) {
            return false;
        }
    }

    // Override AddressManager methods if localStorage is not available
    if (!isLocalStorageAvailable() && typeof AddressManager !== 'undefined') {
        console.warn('localStorage not available, using memory storage');
        let memoryStorage = {};
        
        AddressManager.saveDefaultAddress = function() {
            if (!this.validateForm()) return;
            const addressData = this.getFormData();
            memoryStorage['default_address'] = JSON.stringify(addressData);
            this.showMessage('Default address saved successfully!', 'success');
        };
        
        AddressManager.loadDefaultAddress = function() {
            const savedAddress = memoryStorage['default_address'];
            if (savedAddress) {
                const addressData = JSON.parse(savedAddress);
                this.populateForm(addressData);
                this.showMessage('Default address loaded!', 'info');
                return true;
            }
            this.showMessage('No default address found.', 'warning');
            return false;
        };
        
        AddressManager.clearDefaultAddress = function() {
            delete memoryStorage['default_address'];
            this.showMessage('Default address cleared!', 'info');
        };
    }

    // Add event listener to submit button
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Validate required fields first
            if (!validateRequiredFields()) {
                return false;
            }
            
            if (prepareFormSubmission()) {
                // Create a hidden input for status
                let statusInput = orderForm.querySelector('input[name="status"]');
                if (!statusInput) {
                    statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    orderForm.appendChild(statusInput);
                }
                statusInput.value = 'Pending';
                orderForm.submit();
            }
        });
    }

    // Handle form submit event directly
    orderForm.addEventListener('submit', function(e) {
        // This will catch any direct form submissions
        if (!document.getElementById('cart_data') || !document.getElementById('cart_data').value) {
            e.preventDefault();
            if (!prepareFormSubmission()) {
                return false;
            }
        }
    });
});

// Function to validate required fields before submission
function validateRequiredFields() {
    const requiredFields = [
        'receiver_name', 'company_name', 'street', 'billing_address', 'receiver_mobile'
    ];

    const missingFields = [];

    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            missingFields.push(fieldName.replace('_', ' ').toUpperCase());
        }
    });

    if (missingFields.length > 0) {
        alert(`Please fill in the following required fields:\n${missingFields.join('\n')}`);
        return false;
    }

    return true;
}