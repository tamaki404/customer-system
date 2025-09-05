document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        const submitBtn = form.querySelector("#submitBtn");
        if (!submitBtn) return;

        form.addEventListener("submit", function () {
            submitBtn.disabled = true;
            submitBtn.innerText = "Processing...";
        });
    });
});
