// document.addEventListener("DOMContentLoaded", function() {
//     const paymentStatus = document.getElementById("payment_status");
//     const rejectDiv = document.getElementById("rejectPaymentInput");
//     const rejectInput = document.getElementById("reject-details");
//     const notesDiv = document.getElementById("messAddInput");
//     const notesInput = document.getElementById("payment-notes");

//     function toggleRejectInput() {
//         if (paymentStatus.value === "Rejected") {
//             rejectDiv.style.display = "block";
//             notesDiv.style.display = "none";
//             rejectInput.setAttribute("required", "required");
//             notesInput.removeAttribute("required");
//         } else {
//             notesDiv.style.display = "block";
//             rejectDiv.style.display = "none";
//             rejectInput.removeAttribute("required");
//             rejectInput.value = "";
//         }
//     }

//     // document.getElementById('confirmFileBtn').addEventListener('click', function () {
//     //     this.disabled = true;
//     //     this.innerText = "Processing...";
//     // });

//     paymentStatus.addEventListener("change", toggleRejectInput);
//     toggleRejectInput(); 
// });
function toggleRejectInput() {
    const statusSelect = document.getElementById('status');
    const rejectInput = document.getElementById('rejectPaymentInput');
    const submitBtn = document.getElementById('submitBtnText');
    
    if (statusSelect.value === 'Rejected') {
        rejectInput.style.display = 'block';
        // Make rejected_note required when rejecting
        document.getElementById('reject-details').setAttribute('required', 'required');
        submitBtn.textContent = 'Reject Receipt';
    } else {
        rejectInput.style.display = 'none';
        // Remove required attribute when not rejecting
        document.getElementById('reject-details').removeAttribute('required');
        submitBtn.textContent = statusSelect.value === 'Verified' ? 'Verify Receipt' : 'Confirm Action';
    }
}