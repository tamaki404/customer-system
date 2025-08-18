    // Local cart object (can later be saved to session via AJAX)
    let cart = {};

    // Add product to cart
    function addToCart(productId, name, price, stock) {
        if (!cart[productId]) {
            cart[productId] = { id: productId, name, price, stock, quantity: 1 };
        } else {
            if (cart[productId].quantity < stock) {
                cart[productId].quantity++;
            } else {
                alert("No more stock available for " + name);
            }
        }
        renderCart();
        renderSummary();
    }

    // Remove product
    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
        renderSummary();
    }

    // Update quantity
    function updateQuantity(productId, qty) {
        if (qty <= 0) {
            removeFromCart(productId);
        } else if (qty <= cart[productId].stock) {
            cart[productId].quantity = qty;
        }
        renderCart();
        renderSummary();
    }

    // Render cart in "Edit Order"
    function renderCart() {
        let container = document.getElementById("cart-items");
        container.innerHTML = "";
        Object.values(cart).forEach(item => {
            container.innerHTML += `
                <div class="cart-item">
                    <span>${item.name}</span>
                    <input type="number" value="${item.quantity}" min="1" max="${item.stock}" 
                        onchange="updateQuantity(${item.id}, this.value)">
                    <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                    <button onclick="removeFromCart(${item.id})">Remove</button>
                </div>
            `;
        });
    }

    // Render order summary
    function renderSummary() {
        let container = document.getElementById("order-summary");
        container.innerHTML = "";
        let total = 0;
        Object.values(cart).forEach(item => {
            total += item.price * item.quantity;
            container.innerHTML += `<div>${item.quantity}x ${item.name} - ₱${(item.price * item.quantity).toFixed(2)}</div>`;
        });
        container.innerHTML += `<hr><strong>Total: ₱${total.toFixed(2)}</strong>`;
    }

    // Attach click listeners to "Add to cart" buttons
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".add-to-cart-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.dataset.productId;
                const name = this.dataset.productName;
                const price = parseFloat(this.dataset.productPrice);
                const stock = parseInt(this.dataset.productStock);
                addToCart(id, name, price, stock);
            });
        });
    });
