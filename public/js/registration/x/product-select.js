
document.addEventListener('DOMContentLoaded', function() {
    const categoryCards = document.querySelectorAll('.category-card');
    const formsContainer = document.getElementById('product-forms-container');
    let selectedProducts = new Set();
    let activeCategory = null;

    console.log('Category product selector loaded');

    // Count products per category
    function updateProductCounts() {
        document.querySelectorAll('.category-count').forEach(function(countElement) {
            const category = countElement.getAttribute('data-category');
            const card = countElement.closest('.category-card');
            const tags = card.querySelectorAll('.product-tag');
            countElement.textContent = tags.length;
        });
    }

    updateProductCounts();

    // Handle category card clicks
    categoryCards.forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking on a product tag
            if (e.target.closest('.product-tag')) return;
            
            const dropdown = this.querySelector('.products-dropdown');
            const isExpanded = this.classList.contains('expanded');
            
            // Close all other categories
            categoryCards.forEach(function(otherCard) {
                if (otherCard !== card) {
                    otherCard.classList.remove('expanded', 'active');
                    otherCard.querySelector('.products-dropdown').classList.remove('show');
                }
            });
            
            // Toggle current category
            if (isExpanded) {
                this.classList.remove('expanded', 'active');
                dropdown.classList.remove('show');
                activeCategory = null;
            } else {
                this.classList.add('expanded', 'active');
                dropdown.classList.add('show');
                activeCategory = this.getAttribute('data-category');
            }
        });
    });

    // Handle product tag clicks
    document.querySelectorAll('.product-tag').forEach(function(tag) {
        tag.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            if (selectedProducts.has(productId)) {
                // Deselect
                selectedProducts.delete(productId);
                this.classList.remove('selected');
                removeProductForm(productId);
            } else {
                // Select
                selectedProducts.add(productId);
                this.classList.add('selected');
                addProductForm(productId, productName);
            }
        });
    });

    // Add product form
    function addProductForm(productId, productName) {
        const template = document.getElementById('product-form-template');
        if (!template) {
            console.error('Template not found!');
            return;
        }
        
        if (!formsContainer) {
            console.error('Forms container not found!');
            return;
        }
        
        let filledTemplate = template.innerHTML
            .replace(/__PRODUCT_ID__/g, productId)
            .replace(/__PRODUCT_NAME__/g, productName);
        
        const wrapper = document.createElement('div');
        wrapper.innerHTML = filledTemplate.trim();
        const formElement = wrapper.firstElementChild;
        formElement.setAttribute('data-id', productId);
        
        formsContainer.appendChild(formElement);
        
        // Scroll and highlight
        formElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        formElement.style.background = '#e8f5e9';
        setTimeout(function() {
            formElement.style.transition = 'background 1.5s';
            formElement.style.background = '#f9f9f9';
        }, 100);
    }

    // Remove product form
    function removeProductForm(productId) {
        const form = document.querySelector('.product-form-main[data-id="' + productId + '"]');
        if (form) {
            form.remove();
        }
    }

    // Global function for remove button (accessible from inline onclick)
    window.removeProductSelection = function(productId) {
        selectedProducts.delete(productId);
        removeProductForm(productId);
        
        // Update tag visual state
        const tag = document.querySelector('.product-tag[data-product-id="' + productId + '"]');
        if (tag) {
            tag.classList.remove('selected');
        }
    };


});