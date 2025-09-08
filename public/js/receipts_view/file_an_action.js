document.addEventListener("DOMContentLoaded", function() {
    const paymentStatus = document.getElementById("payment_status");
    const rejectDiv = document.getElementById("rejectPaymentInput");
    const rejectInput = document.getElementById("reject-details");
    const notesDiv = document.getElementById("messAddInput");
    const notesInput = document.getElementById("payment-notes");

    function toggleRejectInput() {
        if (paymentStatus.value === "Rejected") {
            rejectDiv.style.display = "block";
            notesDiv.style.display = "none";
            rejectInput.setAttribute("required", "required");
            notesInput.removeAttribute("required");
        } else {
            notesDiv.style.display = "block";
            rejectDiv.style.display = "none";
            rejectInput.removeAttribute("required");
            rejectInput.value = "";
        }
    }

    // document.getElementById('confirmFileBtn').addEventListener('click', function () {
    //     this.disabled = true;
    //     this.innerText = "Processing...";
    // });

    paymentStatus.addEventListener("change", toggleRejectInput);
    toggleRejectInput(); 
});
