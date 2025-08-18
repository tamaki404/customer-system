// Global variables
let cart = {};
let currentStep = 1;
const totalSteps = 6;

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

// Render order summary
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

// Update summary with form data
function updateSummary() {
    console.log('Updating summary...');
    
    const fields = [
        { input: "[name='billing_address']", summary: "#summary-billing" },
        { input: "[name='contact_phone']", summary: "#summary-phone" },
        { input: "[name='contact_email']", summary: "#summary-email" },
        { input: "[name='receiver_name']", summary: "#summary-receiver" },
        { input: "[name='order_notes']", summary: "#summary-notes" },
    ];
    
    fields.forEach(field => {
        const inputElement = document.querySelector(field.input);
        const summaryElement = document.querySelector(field.summary);
        
        if (inputElement && summaryElement) {
            const value = inputElement.value.trim();
            summaryElement.textContent = value || "Not provided";
            console.log(`Updated ${field.input}: ${value || "Not provided"}`);
        } else {
            console.log(`Missing element - Input: ${!!inputElement}, Summary: ${!!summaryElement}`);
        }
    });
    
    // Update shipping address summary
    updateShippingAddressSummary();
    
    renderSummary(); // Also update the cart summary
}

// Update shipping address summary
function updateShippingAddressSummary() {
    const summaryShipping = document.querySelector("#summary-shipping");
    if (!summaryShipping) return;
    
    const addressParts = [
        document.querySelector("[name='street']")?.value,
        document.querySelector("[name='barangay'] option:checked")?.textContent,
        document.querySelector("[name='municipality'] option:checked")?.textContent,
        document.querySelector("[name='province'] option:checked")?.textContent,
        document.querySelector("[name='region'] option:checked")?.textContent,
        document.querySelector("[name='postal_code']")?.value
    ].filter(part => part && part.trim() && part !== "-- Select Region --" && part !== "-- Select Province --" && part !== "-- Select Municipality --" && part !== "-- Select Barangay --");
    
    const shippingAddress = addressParts.length > 0 ? addressParts.join(", ") : "Not provided";
    summaryShipping.textContent = shippingAddress;
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
        // Future steps have no special class
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
                { field: 'street', label: 'Street Address' }
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
            const receiverName = document.querySelector("[name='receiver_name']");
            if (!receiverName || !receiverName.value.trim()) {
                showError("Receiver Name is required.", receiverName);
                receiverName?.focus();
                return false;
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

    // Handle form submission
    const form = document.getElementById("order-form");
    if (form) {
        form.addEventListener("submit", function(e) {
            console.log('Form submission triggered');
            
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                showError("Cannot submit order with empty cart.");
                return false;
            }
            
            // Add cart data to form
            addCartDataToForm();
            
            console.log('Form is being submitted...');
            return true;
        });
    }

    // Initial setup
    showStep(currentStep);
    renderCart();
    renderSummary();
    
    console.log('Initialization complete');
});