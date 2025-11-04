document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-row-action");

    if (!modal) {
        console.error("Modal #edit-row-action not found!");
        return;
    }

    modal.addEventListener("show.bs.modal", function(event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget;
        
        if (!button) {
            console.error("No button triggered the modal");
            return;
        }

        // Extract data from button attributes
        const setId = button.getAttribute("data-set-id");
        const customerId = button.getAttribute("data-customer-id");
        const price = button.getAttribute("data-price");
        const name = button.getAttribute("data-name");

        console.log("Data retrieved:", { setId, customerId, price, name });

        // Populate Form 1 (Modify Product)
        const modifySetIdInput = document.getElementById("edit-modal-set-id");
        const modifyCustomerIdInput = document.getElementById("edit-modal-customer-id");
        const modifyProductNameInput = document.getElementById("modify-product-name");
        const modifyPriceInput = document.getElementById("modify-price");

        if (modifySetIdInput) modifySetIdInput.value = setId;
        if (modifyCustomerIdInput) modifyCustomerIdInput.value = customerId;
        if (modifyProductNameInput) modifyProductNameInput.value = name;
        if (modifyPriceInput) modifyPriceInput.value = price;

        // Populate Form 2 (Sale Price)
        const saleSetIdInput = document.getElementById("sale-form-set-id");
        const saleCustomerIdInput = document.getElementById("sale-form-customer-id");
        const saleProductNameInput = document.getElementById("sale-product-name");
        const saleBasePriceInput = document.getElementById("sale-base-price");

        if (saleSetIdInput) saleSetIdInput.value = setId;
        if (saleCustomerIdInput) saleCustomerIdInput.value = customerId;
        if (saleProductNameInput) saleProductNameInput.value = name;
        if (saleBasePriceInput) saleBasePriceInput.value = price;

        // Verify values were set
        console.log("Form 1 set_id value:", modifySetIdInput?.value);
        console.log("Form 2 set_id value:", saleSetIdInput?.value);

        // Show modify form by default
        showModifyForm();
        
        // Set first button as active
        const firstButton = document.querySelector('.modal-action-con .action-buttons button');
        if (firstButton) {
            setActiveButton(firstButton);
        }
    });
});

function showModifyForm() {
    const modifyForm = document.getElementById("modify-form");
    const saleForm = document.getElementById("sale-form");
    
    if (modifyForm) modifyForm.style.display = "block";
    if (saleForm) saleForm.style.display = "none";
}

function showSaleForm() {
    const modifyForm = document.getElementById("modify-form");
    const saleForm = document.getElementById("sale-form");
    
    if (modifyForm) modifyForm.style.display = "none";
    if (saleForm) saleForm.style.display = "block";
}

function setActiveButton(button) {
    document.querySelectorAll('.modal-action-con .action-buttons button')
        .forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
}