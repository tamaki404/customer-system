document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function() {
        if (submitBtn) {
            submitBtn.disabled = true; // prevent double click
            submitBtn.innerText = "Processing..."; // optional feedback
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.auto-hide');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500); // remove from DOM
        }, 5000); // 5 seconds
    });
});
