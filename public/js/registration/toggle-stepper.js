document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".log-form > div"); 
    const nextBtn = document.querySelector(".next-btn");
    const prevBtn = document.querySelector(".prev-btn");
    let currentStep = 0;

    // function to show only the active step
    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = i === index ? "block" : "none";
        });
    }

    // function to validate required fields in current step
    function validateStep(stepIndex) {
        const step = steps[stepIndex];
        const requiredFields = step.querySelectorAll("[required]");
        let valid = true;

        requiredFields.forEach(field => {
            if (!field.value || (field.type === "checkbox" && !field.checked)) {
                valid = false;
                field.classList.add("invalid-field");
            } else {
                field.classList.remove("invalid-field");
            }
        });

        return valid;
    }

    // initial load
    showStep(currentStep);

    // next button
    nextBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (validateStep(currentStep)) {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        } else {
            alert("Please complete all required fields before proceeding.");
        }
    });

    // prev button
    prevBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });
});
