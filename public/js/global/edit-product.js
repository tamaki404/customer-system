document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("edit-row-action");

    modal.addEventListener("show.bs.modal", event => {
        // The button that triggered the modal
        const button = event.relatedTarget;

        // Extract data from button
        const setId = button.getAttribute("data-set-id");
        const supplierId = button.getAttribute("data-supplier-id");
        const price = button.getAttribute("data-price");
        const name = button.getAttribute("data-name");

        // Populate modal fields
        document.getElementById("edit-modal-set-id").value = setId;
        document.getElementById("edit-modal-supplier-id").value = supplierId;
        document.getElementById("modal-price").value = price;
        document.getElementById("modal-product-name").value = name;
    });
});
