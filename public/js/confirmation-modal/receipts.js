document.addEventListener("DOMContentLoaded", () => {
    const confirmModalEl = document.getElementById("confirmModal");
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    const openBtn = document.getElementById("openConfirm");
    const confirmBtn = document.getElementById("confirmSaveBtn");
    const form = document.getElementById("submitForm");

    // Required fields
    const receiptNumber = document.querySelector('input[name="receipt_number"]');
    const receiptImage = document.querySelector('input[name="receipt_image"]');
    const purchaseDate = document.querySelector('input[name="purchase_date"]');
    const totalAmount = document.querySelector('input[name="total_amount"]');
    const paymentMethod = document.querySelector('input[name="payment_method"]');

    const requiredFields = [receiptNumber, receiptImage, purchaseDate, totalAmount, paymentMethod];

    // Check if all required fields are filled
    function checkFormValidity() {
        let allFilled = true;

        requiredFields.forEach(field => {
            if (field.type === 'file') {
                if (!field.files.length) allFilled = false;
            } else if (!field.value.trim()) {
                allFilled = false;
            }
        });

        openBtn.disabled = !allFilled; // Disable open modal button if not filled
    }

    // Run check on input or change
    requiredFields.forEach(field => {
        field.addEventListener('input', checkFormValidity);
        field.addEventListener('change', checkFormValidity);
    });

    // Initial check
    checkFormValidity();

    // When clicking "Submit Receipt", show modal only if button enabled
    openBtn.addEventListener("click", () => {
        if (!openBtn.disabled) {
            confirmModal.show();
        }
    });

    // When clicking confirm -> disable button + submit form
    confirmBtn.addEventListener("click", () => {
        confirmBtn.disabled = true;
        confirmBtn.innerText = "Processing...";
        confirmModal.hide();
        form.submit();
    });
});
