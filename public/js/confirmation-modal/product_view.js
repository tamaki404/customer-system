document.addEventListener("DOMContentLoaded", () => {
    const confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
    const confirmBtn = document.getElementById("confirmSaveBtn");
    const confirmMessage = document.querySelector("#confirmModal .modal-body");
    let currentForm = null;
    let clickedButton = null;



    confirmBtn.addEventListener("click", () => {
        if (currentForm && clickedButton) {
            clickedButton.innerText = "Processing...";
            clickedButton.disabled = true; 
            currentForm.submit();
        }
    });
});


setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500); 
    });
}, 3000);
