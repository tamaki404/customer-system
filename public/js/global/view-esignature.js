document.addEventListener("DOMContentLoaded", function () {
    const modalImage = document.getElementById("view-sign-image");
    const modalName = document.getElementById("modal-signatory-name");

    document.querySelectorAll(".viewSignBtn").forEach(button => {
        button.addEventListener("click", () => {
            modalImage.src = button.getAttribute("data-image");
            modalName.textContent = button.getAttribute("data-name");
        });
    });
});