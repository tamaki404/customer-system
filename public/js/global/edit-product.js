document.addEventListener("DOMContentLoaded", () => {
    const editButtons = document.querySelectorAll(".edit-product-btn");

    editButtons.forEach(button => {
        button.addEventListener("click", () => {
            document.getElementById("modal-set-id").value = button.dataset.setId;
            document.getElementById("modal-price").value = button.dataset.price;
            document.getElementById("modal-product-name").value = button.dataset.name;
        });
    });
});