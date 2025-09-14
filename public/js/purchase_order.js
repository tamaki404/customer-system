// Global state
let cart = {};
let currentStep = 1;
const totalSteps = 4;

// Error handling
function showError(message) {
    clearErrors();
    
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
        position: fixed;
        top: 50px;
        left: 50%;
        width: 90%;
        transform: translateX(-50%);
        max-width: 800px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 9999;
    `;
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => errorDiv.remove(), 5000);
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(error => error.remove());
}

// Cart management
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
    clearErrors();
    renderCart();
}

function removeFromCart(productId) {
    delete cart[productId];
    renderCart();
}

function updateQuantity(productId, qty) {
    qty = parseInt(qty);
    if (qty <= 0) {
        removeFromCart(productId);
    } else if (qty <= cart[productId].stock) {
        cart[productId].quantity = qty;
        renderCart();
    } else {
        showError(`Quantity cannot exceed available stock (${cart[productId].stock})`);
        renderCart();
    }
}

// Cart rendering
function renderCart() {
    const container = document.getElementById("cart-items");
    if (!container) return;

    if (Object.keys(cart).length === 0) {
        container.innerHTML = "<p style='color:#888;'>Your cart is empty.</p>";
        return;
    }

    let html = `
        <table class="cart-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa; text-align: center;">
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
        const subtotal = parseFloat(item.price) * parseInt(item.quantity);
        html += `
            <tr style="height: 50px; text-align: center;">
                <td style="padding:10px 8px; font-size: 13px;">${item.name}</td>
                <td style="padding:10px 8px; font-size: 13px;">
                    <input type="number" value="${item.quantity}" min="1" max="${item.stock}" 
                        onchange="updateQuantity(${item.id}, this.value)"
                        style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                </td>
                <td style="padding:10px 8px; font-size: 13px;">₱${parseFloat(item.price).toFixed(2)}</td>
                <td style="padding:10px 8px; font-size: 13px;">₱${subtotal.toFixed(2)}</td>
                <td style="padding:10px 8px; font-size: 13px;">
                    <button type="button" onclick="removeFromCart(${item.id})" 
                        style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;
}

// Summary rendering
function renderSummary() {
    const summaryDiv = document.querySelector('.summary-div');
    if (!summaryDiv) return;

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
                    <span>${item.quantity}x ${item.name}</span>
                    <span style="float: right">₱${itemSubtotal.toFixed(2)}</span>
                </p>
            `;
        });
    }

    // Get form values
    const getValue = (selector, defaultValue = 'Not provided') => {
        const element = document.querySelector(selector);
        return element?.value?.trim() || defaultValue;
    };

    const getSelectText = (selector, defaultValue = 'Not provided') => {
        const element = document.querySelector(`${selector} option:checked`);
        return element?.textContent?.trim() || defaultValue;
    };

    // Build address
    const addressParts = [
        getValue("[name='street']"),
        getSelectText("[name='barangay']"),
        getSelectText("[name='municipality']"),
        getSelectText("[name='province']"),
        getSelectText("[name='region']"),
        getValue("[name='postal_code']")
    ].filter(part => 
        part && part !== "Not provided" && !part.includes("-- Select")
    );
    
    const shippingAddress = addressParts.length > 0 ? addressParts.join(", ") : "Not provided";
    const shippingFee = 50.00;
    const grandTotal = subtotal + shippingFee;

    summaryDiv.innerHTML = `
        <h2>Order Summary</h2>
        <p>Review your order before finalizing.</p>

        <!-- Cart Items -->
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 17px; color: #666; margin: 0 0 10px 0;">Cart Items</h3>
            ${cartItemsHtml}
        </div>

        <!-- Order Details -->
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 17px; color: #666; margin: 0 0 15px 0;">Order Details</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                <div>
                    <strong>Company:</strong><br>
                    ${getValue("[name='company_name']")}
                </div>
                <div>
                    <strong>Shipping Address:</strong><br>
                    ${shippingAddress}
                </div>
                <div>
                    <strong>Billing Address:</strong><br>
                    ${getValue("[name='billing_address']")}
                </div>
                <div>
                    <strong>Receiver:</strong><br>
                    ${getValue("[name='receiver_name']")} - ${getValue("[name='receiver_mobile']")}
                </div>
                <div>
                    <strong>Notes:</strong><br>
                    ${getValue("[name='order_notes']", "N/A")}
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px;">
            <h3 style="font-size: 17px; color: #666; margin: 0 0 10px 0;">Payment Summary</h3>
            <p style="margin: 5px 0;">Subtotal: <span style="float: right; font-weight: bold;">₱${subtotal.toFixed(2)}</span></p>
            <p style="margin: 5px 0;">Shipping Fee: <span style="float: right; font-weight: bold;">₱${shippingFee.toFixed(2)}</span></p>
            <hr>
            <p style="margin: 5px 0; font-size: 18px;">
                <strong>Total: <span style="float: right; color: #28a745;">₱${grandTotal.toFixed(2)}</span></strong>
            </p>
        </div>
    `;
}

// Step navigation
function updateStepProgress(step) {
    document.querySelectorAll(".steps-list .step-item").forEach((item, index) => {
        const stepNumber = index + 1;
        item.classList.remove("active", "completed");
        
        if (stepNumber < step) {
            item.classList.add("completed");
        } else if (stepNumber === step) {
            item.classList.add("active");
        }
    });
}

function showStep(step) {
    document.querySelectorAll(".step-section").forEach(sec => {
        sec.style.display = (parseInt(sec.dataset.step) === step) ? "block" : "none";
    });
    
    updateStepProgress(step);
    clearErrors();
    
    if (step === 4) {
        setTimeout(renderSummary, 100);
    }
}

// Validation
function validateStep(step) {
    clearErrors();
    
    switch(step) {
        case 1:
            if (Object.keys(cart).length === 0) {
                showError("Please add at least one product to your cart.");
                return false;
            }
            break;
            
        case 3:
            const requiredFields = [
                'postal_code', 'region', 'province', 'municipality', 
                'barangay', 'street', 'receiver_name', 'receiver_mobile', 'billing_address'
            ];
            
            for (let field of requiredFields) {
                const element = document.querySelector(`[name='${field}']`);
                if (!element || !element.value.trim()) {
                    showError(`${field.replace('_', ' ')} is required.`);
                    element?.focus();
                    return false;
                }
            }
            break;
    }
    return true;
}

// Form submission helpers
function addCartDataToForm() {
    const form = document.getElementById("orderForm");
    if (!form) return;

    const existingInput = form.querySelector('input[name="cart_data"]');
    if (existingInput) existingInput.remove();

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cart_data";
    input.value = JSON.stringify(cart);
    form.appendChild(input);
}

function validateAllSteps() {
    if (Object.keys(cart).length === 0) {
        currentStep = 1;
        showStep(currentStep);
        showError("Please add at least one product to your cart.");
        return false;
    }

    const requiredFields = [
        'postal_code', 'region', 'province', 'municipality', 
        'barangay', 'street', 'receiver_name', 'receiver_mobile', 'billing_address'
    ];
    
    for (let field of requiredFields) {
        const element = document.querySelector(`[name='${field}']`);
        if (!element || !element.value.trim()) {
            currentStep = 3;
            showStep(currentStep);
            showError(`${field.replace('_', ' ')} is required.`);
            setTimeout(() => element?.focus(), 300);
            return false;
        }
    }

    return true;
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
    // Add to cart buttons
    document.querySelectorAll(".add-to-cart-btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.dataset.productId;
            const name = this.dataset.productName;
            const price = parseFloat(this.dataset.productPrice);
            const stock = parseInt(this.dataset.productStock);
            
            addToCart(id, name, price, stock);
        });
    });

    // Navigation buttons
    document.body.addEventListener("click", e => {
        if (e.target.classList.contains("next-btn")) {
            e.preventDefault();
            if (validateStep(currentStep) && currentStep < totalSteps) {
                currentStep++;
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

    // Form submission
    const form = document.getElementById("orderForm");
    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            clearErrors();
            
            if (!validateAllSteps()) {
                return false;
            }
            
            addCartDataToForm();
            form.submit();
            return false;
        });
    }

    // Setup address field listeners for real-time updates
    const addressFields = ['postal_code', 'region', 'province', 'municipality', 'barangay', 'street'];
    addressFields.forEach(field => {
        const element = document.querySelector(`[name="${field}"]`);
        if (element) {
            element.addEventListener('change', () => {
                if (currentStep === 4) renderSummary();
            });
        }
    });

    // Initial setup
    showStep(currentStep);
    renderCart();
});