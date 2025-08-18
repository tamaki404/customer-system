// Global variables
let cart = {};
let currentStep = 1;
const totalSteps = 6;

// Add product to cart
function addToCart(productId, name, price, stock) {
    if (!cart[productId]) {
        cart[productId] = { id: productId, name, price, stock, quantity: 1 };
    } else {
        if (cart[productId].quantity < stock) {
            cart[productId].quantity++;
        } else {
            alert("No more stock available for " + name);
            return;
        }
    }
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
        alert("Quantity cannot exceed available stock (" + cart[productId].stock + ")");
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
        <table class="cart-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Product</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Qty</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Subtotal</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
    `;

    Object.values(cart).forEach(item => {
        html += `
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">${item.name}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <input type="number" value="${item.quantity}" min="1" max="${item.stock}" 
                        onchange="updateQuantity(${item.id}, this.value)"
                        style="width: 60px; padding: 5px;">
                </td>
                <td style="padding: 10px; border: 1px solid #ddd;">₱${parseFloat(item.price).toFixed(2)}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">₱${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <button type="button" onclick="removeFromCart(${item.id})" 
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
    
    renderSummary(); // Also update the cart summary
}

// Show specific step
function showStep(step) {
    const steps = document.querySelectorAll(".step-section");
    
    steps.forEach(sec => {
        sec.style.display = (parseInt(sec.dataset.step) === step) ? "block" : "none";
    });

    // Update header step highlight
    document.querySelectorAll(".form-steps .step").forEach((el, idx) => {
        el.classList.toggle("active", idx + 1 === step);
    });
    
    console.log(`Showing step ${step}`);
}

// Validate current step
function validateStep(step) {
    switch(step) {
        case 1:
            if (Object.keys(cart).length === 0) {
                alert("Please add at least one product to your cart.");
                return false;
            }
            break;
      case 3:
            const requiredShippingFields = [ 'postal_code', 'region', 'province', 'municipality', 'barangay', 'street' ];
            for (let field of requiredShippingFields) {
                const element = document.querySelector(`[name='${field}']`);
                if (!element || !element.value.trim()) {
                    alert(`Please fill in the ${field.replace('_', ' ')}.`);
                    element?.focus();
                    return false;
                }
            }


            // Validate email format
            const email = document.querySelector("[name='contact_email']").value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                document.querySelector("[name='contact_email']").focus();
                return false;
            }
            break;
        case 4:
            
            const receiverName = document.querySelector("[name='receiver_name']");
            if (!receiverName || !receiverName.value.trim()) {
                alert("Receiver Name is required.");
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
                alert("Cannot submit order with empty cart.");
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