let selectedForm = null;
let selectedAction = null;
let selectedButton = null; 

document.querySelectorAll('.btn-confirm').forEach(button => {
    button.addEventListener('click', function () {
        selectedForm = this.closest('form');
        selectedAction = this.dataset.action;
        selectedButton = this;

        const modalBody = document.querySelector('#confirmModal .modal-body');
        if (modalBody) {
            modalBody.innerText = `Are you sure you want to update this into ${selectedAction} order?`;
        }

        let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    });
});

document.getElementById('confirmSaveBtn').addEventListener('click', function () {
    if (selectedForm && selectedAction) {
        let statusInput = selectedForm.querySelector('input[name="status"]');
        if (!statusInput) {
            statusInput = document.createElement("input");
            statusInput.type = "hidden";
            statusInput.name = "status";
            selectedForm.appendChild(statusInput);
        }
        statusInput.value = selectedAction;

        this.disabled = true;
        this.innerText = "Processing...";

        if (selectedButton) {
            selectedButton.disabled = true;
            selectedButton.innerText = "Processing...";
        }

        selectedForm.submit();
    }
});

document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function () {
    const confirmBtn = document.getElementById('confirmSaveBtn');
    confirmBtn.disabled = false;
    confirmBtn.innerText = "Confirm";
});

setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500);
    });
}, 3000);
