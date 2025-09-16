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

        // initial load
        showStep(currentStep);

        // next button
        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
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


