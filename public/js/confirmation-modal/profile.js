document.getElementById('confirmSaveBtn').addEventListener('click', function () {
    let saveBtnCustomer = document.getElementById('customerSaveChanges');
    let saveBtnAdmin = document.getElementById('adminSaveChanges');

    if (saveBtnCustomer) {
        saveBtnCustomer.disabled = true;
        saveBtnCustomer.innerText = "Processing...";
    }
    if (saveBtnAdmin) {
        saveBtnAdmin.disabled = true;
        saveBtnAdmin.innerText = "Processing...";
    }

    let modalEl = document.getElementById('confirmModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    if (document.getElementById('customer_edit')) {
        document.getElementById('customer_edit').submit();
    } else if (document.getElementById('submitForm')) {
        document.getElementById('submitForm').submit();
    }
});

document.getElementById('cancelBtn').addEventListener('click', function () {
    location.reload();
});

setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500); 
    });
}, 3000);
