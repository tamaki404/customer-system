// Prevent double submission
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        const saveBtn = form.querySelector(".save-btn");
        if (!saveBtn) return; 

        form.addEventListener("submit", function () {
            saveBtn.disabled = true;
            saveBtn.innerText = "Processing...";
        });
    });
});

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

window.onclick = function (event) {
    const modal = document.getElementById("editModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};
