function showModifyForm() {
    document.getElementById("modify-form").style.display = "block";
    document.getElementById("sale-form").style.display = "none";
}
function showSaleForm() {
    document.getElementById("modify-form").style.display = "none";
    document.getElementById("sale-form").style.display = "block";
}
function setActiveButton(button) {
    // remove active from all buttons
    document.querySelectorAll('.modal-action-con .action-buttons button')
        .forEach(btn => btn.classList.remove('active'));

    // add active to clicked button
    button.classList.add('active');
}

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-row-action");

    if (!modal) {
        console.error("Modal #edit-row-action not found!");
        return;
    }

    modal.addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget;
        
        if (!button) {
            console.error("No button triggered the modal");
            return;
        }

        // Extract data from button attributes
        const setId = button.getAttribute("data-set-id");
        const supplierId = button.getAttribute("data-supplier-id");
        const price = button.getAttribute("data-price");
        const name = button.getAttribute("data-name");

        console.log("Data retrieved:", { setId, supplierId, price, name });

        // Populate Form 1 (Modify Product)
        const modifySetIdInput = document.getElementById("edit-modal-set-id");
        const modifySupplierIdInput = document.getElementById("edit-modal-supplier-id");
        const modifyProductNameInput = document.getElementById("modify-product-name");
        const modifyPriceInput = document.getElementById("modify-price");

        if (modifySetIdInput) modifySetIdInput.value = setId;
        if (modifySupplierIdInput) modifySupplierIdInput.value = supplierId;
        if (modifyProductNameInput) modifyProductNameInput.value = name;
        if (modifyPriceInput) modifyPriceInput.value = price;

        // Populate Form 2 (Sale Price)
        const saleSetIdInput = document.getElementById("sale-form-set-id");
        const saleSupplierIdInput = document.getElementById("sale-form-supplier-id");
        const saleProductNameInput = document.getElementById("sale-product-name");
        const saleBasePriceInput = document.getElementById("sale-base-price");

        if (saleSetIdInput) saleSetIdInput.value = setId;
        if (saleSupplierIdInput) saleSupplierIdInput.value = supplierId;
        if (saleProductNameInput) saleProductNameInput.value = name;
        if (saleBasePriceInput) saleBasePriceInput.value = price;

        // Initialize the preview with the product name and old price
        const promoOldPrice = document.getElementById("promo-old-price");
        const promoProductName = document.getElementById("promo-product-name");
        
        if (promoOldPrice) promoOldPrice.textContent = parseFloat(price || 0).toFixed(2);
        if (promoProductName) promoProductName.textContent = name || "Product";

        console.log("Form 1 set_id value:", modifySetIdInput?.value);
        console.log("Form 2 set_id value:", saleSetIdInput?.value);

        // Show modify form by default
        showModifyForm();
        
        const firstButton = document.querySelector('.modal-action-con .action-buttons button');
        if (firstButton) {
            setActiveButton(firstButton);
        }
    });

    // Setup live preview functionality
    setupPromoPreview();
});

function setupPromoPreview() {
    const salePrice = document.getElementById("sale_price");
    const startDate = document.getElementById("start_date");
    const endDate = document.getElementById("end_date");

    if (salePrice) salePrice.addEventListener("input", updatePromoPreview);
    if (startDate) startDate.addEventListener("change", updatePromoPreview);
    if (endDate) endDate.addEventListener("change", updatePromoPreview);
}

function updatePromoPreview() {
    const productName = document.getElementById("sale-product-name")?.value;
    const oldPrice = document.getElementById("sale-base-price")?.value;
    const newPrice = document.getElementById("sale_price")?.value;
    const startDate = document.getElementById("start_date")?.value;
    const endDate = document.getElementById("end_date")?.value;

    const promoPreview = document.getElementById("promo-preview");
    
    // Show preview only if we have the essential data
    if (newPrice && startDate && endDate) {
        promoPreview.style.display = "block";
        
        // Update product name
        document.getElementById("promo-product-name").textContent = productName || "Product";
        
        // Parse prices as floats
        const oldPriceFloat = parseFloat(oldPrice || 0);
        const newPriceFloat = parseFloat(newPrice);
        
        // Update prices
        document.getElementById("promo-old-price").textContent = oldPriceFloat.toFixed(2);
        document.getElementById("promo-new-price").textContent = newPriceFloat.toFixed(2);
        
        // Calculate percentage discount
        let discountPercent = 0;
        if (oldPriceFloat > 0 && newPriceFloat < oldPriceFloat) {
            discountPercent = ((oldPriceFloat - newPriceFloat) / oldPriceFloat) * 100;
        }
        
        // Update discount badge
        const discountBadge = document.getElementById("promo-discount-badge");
        if (discountPercent > 0) {
            discountBadge.textContent = discountPercent.toFixed(0) + "% OFF";
            discountBadge.style.display = "inline-block";
        } else {
            discountBadge.style.display = "none";
        }
        
        // Calculate duration
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        document.getElementById("promo-duration").textContent = diffDays;
        
        // Format dates
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        const formattedStart = start.toLocaleDateString('en-US', options);
        const formattedEnd = end.toLocaleDateString('en-US', options);
        
        document.getElementById("promo-dates").textContent = `${formattedStart} to ${formattedEnd}`;
    } else {
        promoPreview.style.display = "none";
    }
}

function showModifyForm() {
    const modifyForm = document.getElementById("modify-form");
    const saleForm = document.getElementById("sale-form");
    const promoPreview = document.getElementById("promo-preview");
    
    if (modifyForm) modifyForm.style.display = "block";
    if (saleForm) saleForm.style.display = "none";
    if (promoPreview) promoPreview.style.display = "none";
}

function showSaleForm() {
    const modifyForm = document.getElementById("modify-form");
    const saleForm = document.getElementById("sale-form");
    const promoPreview = document.getElementById("promo-preview");
    
    if (modifyForm) modifyForm.style.display = "none";
    if (saleForm) saleForm.style.display = "block";
    
    // Reset preview when showing the form
    if (promoPreview) promoPreview.style.display = "none";
    
    // Clear the sale form inputs (but keep the hidden fields)
    const salePriceInput = document.getElementById("sale_price");
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");
    
    if (salePriceInput) salePriceInput.value = "";
    if (startDateInput) startDateInput.value = "";
    if (endDateInput) endDateInput.value = "";
}

function setActiveButton(button) {
    document.querySelectorAll('.modal-action-con .action-buttons button')
        .forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
}
    