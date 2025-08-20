// Global variables
let cart = {};
let currentStep = 1;
const totalSteps = 5; // Fixed: should be 5, not 6

// Function to display error messages
function showError(message, targetElement = null) {
    // Remove any existing error messages
    const existingErrors = document.querySelectorAll('.error-message');
    existingErrors.forEach(error => error.remove());
    
    // Create error message div
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = `
        color: #dc3545;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 10px;
        margin: 10px 0;
        border-radius: 10px;
        font-size: 14px;
        z-index: 9999;
        position: fixed;
        top: 50px;
        left: 50%;
        width: 90%;
        transform: translateX(-50%);
        max-width: 800px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    
    `;
    errorDiv.textContent = message;
    
    // Insert error message
    if (targetElement) {
        targetElement.parentNode.insertBefore(errorDiv, targetElement.nextSibling);
    } else {
        // Insert at the top of the current step
        const currentStepElement = document.querySelector(`[data-step="${currentStep}"]`);
        if (currentStepElement) {
            currentStepElement.insertBefore(errorDiv, currentStepElement.firstChild);
        } else {
            // Fallback: insert at top of body
            document.body.insertBefore(errorDiv, document.body.firstChild);
        }
    }
    
    // Auto-remove error after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

// Function to clear error messages
function clearErrors() {
    const existingErrors = document.querySelectorAll('.error-message');
    existingErrors.forEach(error => error.remove());
}

// Add product to cart
function addToCart(productId, name, price, stock) {
    if (!cart[productId]) {
        cart[productId] = { id: productId, name, price, stock, quantity: 1 };
    } else {
        if (cart[productId].quantity < stock) {
            cart[productId].quantity++;
        } else {
            showError(`No more stock available for ${name}`);
            return;
        }
    }
    clearErrors(); // Clear any existing errors on successful add
    renderCart();
    renderSummary();
}

// Remove product from cart
function removeFromCart(productId) {
    delete cart[productId];
    renderCart();
    renderSummary();
}

// Update quantity
function updateQuantity(productId, qty) {
    qty = parseInt(qty);
    if (qty <= 0) {
        removeFromCart(productId);
    } else if (qty <= cart[productId].stock) {
        cart[productId].quantity = qty;
        renderCart();
        renderSummary();
    } else {
        showError(`Quantity cannot exceed available stock (${cart[productId].stock})`);
        renderCart(); // Re-render to reset the input value
    }
}

// Render cart in "Edit Order" step
function renderCart() {
    let container = document.getElementById("cart-items");
    if (!container) return;

    if (Object.keys(cart).length === 0) {
        container.innerHTML = "<p style='color:#888;'>Your cart is empty.</p>";
        return;
    }

    let html = `
        <table class="cart-table"  style="width:100%; border-collapse:collapse;">
            <thead>
                <tr  style="background:#f7f7fa; text-align: center;">
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    `;

    Object.values(cart).forEach(item => {
        html += `
            <tr style="height: 50px; text-align: center; cursor:pointer;">
                <td style="padding:10px 8px; font-size: 13px;">${item.name}</td>
                <td style="padding:10px 8px; font-size: 13px;">
                    <input type="number" value="${item.quantity}" min="1" max="${item.stock}" 
                        onchange="updateQuantity(${item.id}, this.value)"
                        style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                </td>
                <td style="padding:10px 8px; font-size: 13px;">₱${parseFloat(item.price).toFixed(2)}</td>
                <td style="padding:10px 8px; font-size: 13px;">₱${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                <td style="padding:10px 8px; font-size: 13px;">
                    <button type="button" class="remove-product" onclick="removeFromCart(${item.id})" 
                        style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

// NEW: Render beautiful summary layout with dynamic data
function renderBeautifulSummary() {
    const summaryDiv = document.querySelector('.summary-div');
    if (!summaryDiv) return;

    // Calculate cart totals
    let subtotal = 0;
    let cartItemsHtml = '';
    
    if (Object.keys(cart).length === 0) {
        cartItemsHtml = '<p style="color:#888; text-align: center; padding: 20px;">No items in cart.</p>';
    } else {
        Object.values(cart).forEach(item => {
            const itemSubtotal = parseFloat(item.price) * parseInt(item.quantity);
            subtotal += itemSubtotal;
            cartItemsHtml += `
                <p style="font-size: 15px; border-bottom: 1px solid #88888832; margin: 5px 0; padding: 5px 0;">
                    <span>${item.quantity}x </span>
                    <span>........</span>
                    <span>${item.name}</span>
                    <span style="float: right">₱${itemSubtotal.toFixed(2)}</span>
                </p>
            `;
        });
    }

    // Get form data
    const getFieldValue = (selector, defaultValue = 'Not provided') => {
        const element = document.querySelector(selector);
        return element?.value?.trim() || defaultValue;
    };

    const getSelectValue = (selector, defaultValue = 'Not provided') => {
        const element = document.querySelector(`${selector} option:checked`);
        return element?.textContent?.trim() || defaultValue;
    };

    // Build shipping address
    const addressParts = [
        getFieldValue("[name='street']"),
        getSelectValue("[name='barangay']"),
        getSelectValue("[name='municipality']"),
        getSelectValue("[name='province']"),
        getSelectValue("[name='region']"),
        getFieldValue("[name='postal_code']")
    ].filter(part => 
        part && 
        part !== "Not provided" &&
        part !== "-- Select Region --" && 
        part !== "-- Select Province --" && 
        part !== "-- Select Municipality --" && 
        part !== "-- Select Barangay --"
    );
    
    const shippingAddress = addressParts.length > 0 ? addressParts.join(", ") : "Not provided";

    // Calculate totals (you can add shipping fee logic here)
    const shippingFee = 50.00; // Example shipping fee
    const grandTotal = subtotal + shippingFee;

    // Build the complete summary HTML
    const summaryHTML = `
        <h2>Order Summary</h2>
        <p>Review your order before finalizing.</p>

        <!-- Cart Items Section -->
        <div class="display-div" style="gap: 15px; flex-direction: column; flex-wrap: wrap; background-color: transparent; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; margin-bottom: 20px;">
            <h2 style="font-size: 17px;color: #666;font-weight: bold;margin: 0;margin-top: 15px;">Cart Items</h2>
            <div class="display-div">
                <div style="padding: 5px 0; border-bottom: 1px solid #eee;">
                    ${cartItemsHtml}
                </div>
            </div>
        </div>

        <!-- Receiving Section -->
        <div class="display-div" style="gap: 15px; flex-direction: column; flex-wrap: wrap; background-color: transparent;  box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; margin-bottom: 20px;">
            <h2 style="font-size: 17px;color: #666;font-weight: bold;margin: 0;margin-top: 15px;">Receiving</h2>
            <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: 15px;">
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px; width: auto;">Company Name</p>
                    <p style="color: #333; margin: 0; font-size: 15px; width: auto;">${getFieldValue("[name='company_name']")}</p>
                </div>
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px;">Shipping Address</p>
                    <p style="color: #333; margin: 0; font-size: 15px;">${shippingAddress}</p>
                </div>
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px;">Billing Address</p>
                    <p style="color: #333; margin: 0; font-size: 15px;">${getFieldValue("[name='billing_address']")}</p>
                </div>
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px;">Receiver</p>
                    <p style="color: #333; margin: 0; font-size: 15px;">${getFieldValue("[name='receiver_name']")}</p>
                </div>
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px;">Contact Phone</p>
                    <p style="color: #333; margin: 0; font-size: 15px;">${getFieldValue("[name='contact_phone']")}</p>
                </div>
                <div style="width: auto; border-radius: 10px; display: flex; flex-direction: column; padding: 5px; min-width: 300px; width: auto; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;">
                    <p style="color: #888; margin: 0; font-size: 13px;">Notes</p>
                    <p style="color: #333; margin: 0; font-size: 15px;">${getFieldValue("[name='order_notes']", "N/A")}</p>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="display-div" style="gap: 15px; flex-direction: column; flex-wrap: wrap; background-color: transparent; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; margin-bottom: 20px;">
            <h2 style="font-size: 17px;color: #666;font-weight: bold;margin: 0;margin-top: 15px;">Payment</h2>
            <div style="display: flex; flex-direction: column; flex-wrap: wrap;">
                <p style="font-size: 15px; margin: 0;">Subtotal: <span style="float: right; color: #333; font-weight: bold;">₱${subtotal.toFixed(2)}</span></p>
                <p style="font-size: 15px; margin: 0;">Shipping Fee: <span style="float: right; color: #333; font-weight: bold;">₱${shippingFee.toFixed(2)}</span></p>
                <p style="font-size: 15px; margin: 0;">Total Amount: <span style="float: right; color: #333; font-weight: bold;">₱${grandTotal.toFixed(2)}</span></p>
            </div>
        </div>
    `;

    summaryDiv.innerHTML = summaryHTML;
}

// Legacy render order summary (keep for compatibility with other steps)
function renderSummary() {
    let container = document.getElementById("order-summary");
    if (!container) return;
    
    container.innerHTML = "";
    let total = 0;
    
    if (Object.keys(cart).length === 0) {
        container.innerHTML = "<p style='color:#888;'>No items in cart.</p>";
        return;
    }
    
    Object.values(cart).forEach(item => {
        const subtotal = parseFloat(item.price) * parseInt(item.quantity);
        total += subtotal;
        container.innerHTML += `<div style="padding: 5px 0; border-bottom: 1px solid #eee;">${item.quantity}x ${item.name} - ₱${subtotal.toFixed(2)}</div>`;
    });
    container.innerHTML += `<div style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #333;"><strong>Total: ₱${total.toFixed(2)}</strong></div>`;
}

// Update shipping address summary - ENHANCED VERSION
function updateShippingAddressSummary() {
    const summaryShipping = document.querySelector("#summary-shipping");
    if (!summaryShipping) return;
    
    // Get all address components with better null checking
    const street = document.querySelector("[name='street']")?.value?.trim() || '';
    const barangay = document.querySelector("[name='barangay'] option:checked")?.textContent?.trim() || '';
    const municipality = document.querySelector("[name='municipality'] option:checked")?.textContent?.trim() || '';
    const province = document.querySelector("[name='province'] option:checked")?.textContent?.trim() || '';
    const region = document.querySelector("[name='region'] option:checked")?.textContent?.trim() || '';
    const postalCode = document.querySelector("[name='postal_code']")?.value?.trim() || '';
    
    // Filter out empty values and default select options
    const addressParts = [street, barangay, municipality, province, region, postalCode].filter(part => {
        return part && 
               part !== "-- Select Region --" && 
               part !== "-- Select Province --" && 
               part !== "-- Select Municipality --" && 
               part !== "-- Select Barangay --";
    });
    
    // Combine into one line
    const shippingAddress = addressParts.length > 0 ? addressParts.join(", ") : "Not provided";
    summaryShipping.textContent = shippingAddress;
    
    console.log('Shipping address updated:', shippingAddress);
}

// Update summary with form data - ENHANCED VERSION
function updateSummary() {
    console.log('Updating summary...');
    
    // Use the beautiful summary layout
    renderBeautifulSummary();
    
    // Also update legacy summary if it exists
    const fields = [
        { input: "[name='billing_address']", summary: "#summary-billing" },
        { input: "[name='contact_phone']", summary: "#summary-phone" },
        { input: "[name='contact_email']", summary: "#summary-email" },
        { input: "[name='receiver_name']", summary: "#summary-receiver" },
        { input: "[name='receiver_mobile']", summary: "#summary-receiver-mobile" },
        { input: "[name='order_notes']", summary: "#summary-notes" },
    ];
    
    fields.forEach(field => {
        const inputElement = document.querySelector(field.input);
        const summaryElement = document.querySelector(field.summary);
        
        if (inputElement && summaryElement) {
            const value = inputElement.value.trim();
            summaryElement.textContent = value || "Not provided";
            console.log(`Updated ${field.input}: ${value || "Not provided"}`);
        }
    });
    
    // Update shipping address summary
    updateShippingAddressSummary();
    
    // Also update the cart summary for other steps
    renderSummary();
}

// Setup real-time address field listeners - NEW FUNCTION
function setupAddressListeners() {
    const addressFields = ['[name="postal_code"]', '[name="region"]', '[name="province"]', '[name="municipality"]', '[name="barangay"]', '[name="street"]'];
    
    addressFields.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('change', updateShippingAddressSummary);
            element.addEventListener('input', updateShippingAddressSummary);
        }
    });
    
    console.log('Address field listeners setup complete');
}

// Update step progress indicator
function updateStepProgress(step) {
    const stepItems = document.querySelectorAll(".steps-list .step-item");
    
    stepItems.forEach((item, index) => {
        const stepNumber = index + 1;
        
        // Remove all status classes
        item.classList.remove("active", "completed");
        
        if (stepNumber < step) {
            // Completed steps
            item.classList.add("completed");
        } else if (stepNumber === step) {
            // Current step
            item.classList.add("active");
        }
    });
    
    console.log(`Step progress updated to step ${step}`);
}

// Show specific step
function showStep(step) {
    const steps = document.querySelectorAll(".step-section");
    
    steps.forEach(sec => {
        sec.style.display = (parseInt(sec.dataset.step) === step) ? "block" : "none";
    });

    // Update step progress indicator
    updateStepProgress(step);
    
    // Clear errors when changing steps
    clearErrors();
    
    console.log(`Showing step ${step}`);
}

// Validate current step
function validateStep(step) {
    clearErrors(); // Clear previous errors
    
    switch(step) {
        case 1:
            if (Object.keys(cart).length === 0) {
                showError("Please add at least one product to your cart.");
                return false;
            }
            break;
        case 3:
            const requiredShippingFields = [ 
                { field: 'postal_code', label: 'Postal Code' },
                { field: 'region', label: 'Region' },
                { field: 'province', label: 'Province' },
                { field: 'municipality', label: 'Municipality' },
                { field: 'barangay', label: 'Barangay' },
                { field: 'street', label: 'Street Address' },
                { field: 'billing_address', label: 'Billing Address' }

            ];
            
            for (let fieldInfo of requiredShippingFields) {
                const element = document.querySelector(`[name='${fieldInfo.field}']`);
                if (!element || !element.value.trim()) {
                    showError(`${fieldInfo.label} is required.`, element);
                    element?.focus();
                    return false;
                }
            }

            // Validate email format
            const emailElement = document.querySelector("[name='contact_email']");
            const email = emailElement?.value || '';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError("Please enter a valid email address.", emailElement);
                emailElement?.focus();
                return false;
            }
            break;
        case 4:

            const requiedAdditionalInfo = [ 
                { field: 'receiver_name', label: 'Receiver Name' },
                { field: 'receiver_mobile', label: 'Mobile' },

            ];
            
            for (let fieldInfo of requiedAdditionalInfo) {
                const element = document.querySelector(`[name='${fieldInfo.field}']`);
                if (!element || !element.value.trim()) {
                    showError(`${fieldInfo.label} is required.`, element);
                    element?.focus();
                    return false;
                }
            }

            break;
    }
    return true;
}

function addAddressToForm() {
    const form = document.getElementById("order-form");

    // Remove old hidden input if any
    const existing = document.querySelector("[name='shipping_data']");
    if (existing) existing.remove();

    const shipping = {
        postal_code: document.querySelector("[name='postal_code']").value,
        region: document.querySelector("[name='region']").value,
        province: document.querySelector("[name='province']").value,
        municipality: document.querySelector("[name='municipality']").value,
        barangay: document.querySelector("[name='barangay']").value,
        street: document.querySelector("[name='street']").value,
    };

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "shipping_data";
    input.value = JSON.stringify(shipping);

    form.appendChild(input);

    console.log("Shipping address added:", shipping);
}

// Add cart data to the form as a hidden input
function addCartDataToForm() {
    const form = document.getElementById("order-form");
    if (!form) return;

    // Remove existing hidden input for cart_data if any
    const existingCartInput = document.querySelector('input[name="cart_data"]');
    if (existingCartInput) {
        existingCartInput.remove();
    }

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cart_data";
    input.value = JSON.stringify(cart);

    form.appendChild(input);
    console.log("Cart data added:", cart);
}

// NEW: Navigate to step and show it (for form validation errors)
function navigateToStep(stepNumber) {
    if (stepNumber >= 1 && stepNumber <= totalSteps) {
        currentStep = stepNumber;
        showStep(currentStep);
        console.log(`Navigated to step ${stepNumber}`);
    }
}

// NEW: Enhanced form validation that navigates to the step with errors
function validateAllSteps() {
    // Check Step 1: Cart
    if (Object.keys(cart).length === 0) {
        navigateToStep(1);
        showError("Please add at least one product to your cart.");
        return false;
    }

    // Check Step 3: Shipping Address
    const requiredShippingFields = [ 
        { field: 'postal_code', label: 'Postal Code' },
        { field: 'region', label: 'Region' },
        { field: 'province', label: 'Province' },
        { field: 'municipality', label: 'Municipality' },
        { field: 'barangay', label: 'Barangay' },
        { field: 'street', label: 'Street Address' },
        { field: 'billing_address', label: 'Billing Address' }
    ];
    
    for (let fieldInfo of requiredShippingFields) {
        const element = document.querySelector(`[name='${fieldInfo.field}']`);
        if (!element || !element.value.trim()) {
            navigateToStep(3);
            showError(`${fieldInfo.label} is required.`);
            setTimeout(() => element?.focus(), 300);
            return false;
        }
    }

    // Validate email format
    const emailElement = document.querySelector("[name='contact_email']");
    const email = emailElement?.value || '';
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        navigateToStep(3);
        showError("Please enter a valid email address.");
        setTimeout(() => emailElement?.focus(), 300);
        return false;
    }

    // Check Step 4: Additional Info
    const requiredAdditionalInfo = [ 
        { field: 'receiver_name', label: 'Receiver Name' },
        { field: 'receiver_mobile', label: 'Mobile' },
    ];
    
    for (let fieldInfo of requiredAdditionalInfo) {
        const element = document.querySelector(`[name='${fieldInfo.field}']`);
        if (!element || !element.value.trim()) {
            navigateToStep(4);
            showError(`${fieldInfo.label} is required.`);
            setTimeout(() => element?.focus(), 300);
            return false;
        }
    }

    return true;
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    console.log('DOM Content Loaded');
    
    // Attach click listeners to "Add to cart" buttons
    document.querySelectorAll(".add-to-cart-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.dataset.productId;
            const name = this.dataset.productName;
            const price = parseFloat(this.dataset.productPrice);
            const stock = parseInt(this.dataset.productStock);
            
            console.log('Adding to cart:', {id, name, price, stock});
            addToCart(id, name, price, stock);
        });
    });

    // Setup address field listeners for real-time updates - NEW
    setupAddressListeners();

    // Handle navigation buttons (single event listener)
    document.body.addEventListener("click", e => {
        if (e.target.classList.contains("next-btn")) {
            e.preventDefault();
            
            console.log(`Attempting to go from step ${currentStep} to step ${currentStep + 1}`);
            
            if (!validateStep(currentStep)) {
                return;
            }
            
            if (currentStep < totalSteps) {
                currentStep++;
                
                // Update summary when reaching summary step (step 5)
                if (currentStep === 5) {
                    setTimeout(() => {
                        updateSummary();
                    }, 100);
                }
                
                showStep(currentStep);
            }
        }
        
        if (e.target.classList.contains("prev-btn")) {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }
    });

    // FIXED: Enhanced form submission handler
    const form = document.getElementById("order-form");
    if (form) {
        form.addEventListener("submit", function(e) {
            console.log('Form submission triggered');
            
            // Prevent default submission first
            e.preventDefault();
            
            // Clear any existing errors
            clearErrors();
            
            // Perform comprehensive validation
            if (!validateAllSteps()) {
                console.log('Form validation failed');
                return false;
            }
            
            // Add cart data to form
            addCartDataToForm();
            
            console.log('Form validation passed, submitting...');
            
            // Submit the form programmatically (this bypasses HTML5 validation)
            form.submit();
            
            return false; // Prevent double submission
        });
    }

    // Initial setup
    showStep(currentStep);
    renderCart();
    renderSummary();
    
    console.log('Initialization complete');
});