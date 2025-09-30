document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("view-sign-action");
    modal.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const signature = button.getAttribute("data-signature"); // Get signature path/base64

        const img = modal.querySelector("#view-sign-image");
        img.src = signature ? signature : "";
    });
});
