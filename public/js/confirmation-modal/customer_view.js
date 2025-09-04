let selectedForm = null;
let selectedAction = null;
let selectedButton = null;

// Attach to all buttons with .btn-confirm
document.querySelectorAll('.btn-confirm').forEach(button => {
    button.addEventListener('click', function () {
        selectedForm = this.closest('form'); 
        selectedAction = this.dataset.action; 
        selectedButton = this;

        // Update modal body text
        const modalBody = document.querySelector('#confirmModal .modal-body');
        if (modalBody) {
            modalBody.innerText = `Are you sure you want to ${selectedAction} this account?`;
        }

        let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    });
});

// Confirm button in modal
document.getElementById('confirmSaveBtn').addEventListener('click', function () {
    if (selectedForm && selectedAction) {
        // Create hidden input if missing
        let statusInput = selectedForm.querySelector('input[name="status"]');
        if (!statusInput) {
            statusInput = document.createElement("input");
            statusInput.type = "hidden";
            statusInput.name = "status";
            selectedForm.appendChild(statusInput);
        }
        statusInput.value = selectedAction;

        // Disable confirm button and clicked button
        this.disabled = true;
        this.innerText = "Processing...";

        if (selectedButton) {
            selectedButton.disabled = true;
            selectedButton.innerText = "Processing...";
        }

        selectedForm.submit();
    }
});

// Reset modal confirm button when closed
document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function () {
    const confirmBtn = document.getElementById('confirmSaveBtn');
    confirmBtn.disabled = false;
    confirmBtn.innerText = "Confirm";
});
