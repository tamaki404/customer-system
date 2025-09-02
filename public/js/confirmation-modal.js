document.getElementById('confirmSaveBtn').addEventListener('click', function () {
    let saveBtnCustomer = document.getElementById('customerSaveChanges');
    let saveBtnAdmin = document.getElementById('adminSaveChanges');

    // Disable whichever button was clicked
    if (saveBtnCustomer) {
        saveBtnCustomer.disabled = true;
        saveBtnCustomer.innerText = "Processing...";
    }
    if (saveBtnAdmin) {
        saveBtnAdmin.disabled = true;
        saveBtnAdmin.innerText = "Processing...";
    }

    // Hide modal
    let modalEl = document.getElementById('confirmModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Submit the right form
    if (document.getElementById('customer_edit')) {
        document.getElementById('customer_edit').submit();
    } else if (document.getElementById('submitForm')) {
        document.getElementById('submitForm').submit();
    }
});

// Cancel button reload
document.getElementById('cancelBtn').addEventListener('click', function () {
    location.reload();
});

// Auto dismiss success/error alerts after 3s
setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500); // remove after fade-out animation
    });
}, 3000);
