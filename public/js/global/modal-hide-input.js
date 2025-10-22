document.addEventListener("DOMContentLoaded", function () {
    const accStatus = document.getElementById("account_status");

    const reasonGroup = document.getElementById("reason_group");
    const reasonSelect = document.getElementById("reason_to_decline");
    const toChangeGroup = document.getElementById("to_change_group");
    const toChangeSelect = document.getElementById("to_change");
    const feedbackGroup = document.getElementById("feedback_group");
    const feedbackInput = document.getElementById("feedback");

    const assignAgent = document.getElementById("assign_agent");
    const creditLimit = document.getElementById("credit_limit");
    const ceilingPrice = document.getElementById("ceiling_price");
    const productList = document.getElementById("product_list");

    function toggleFields() {
        const status = accStatus.value;

        // Hide all first
        [reasonGroup, toChangeGroup, feedbackGroup, assignAgent, creditLimit, ceilingPrice, productList].forEach(el => {
            if (el) el.style.display = "none";
        });

        // Remove all requirements
        [reasonSelect, toChangeSelect, feedbackInput].forEach(el => el?.removeAttribute("required"));

        // Disable product inputs so they won't be submitted when hidden
        const productInputs = productList?.querySelectorAll('input[name*="nego_price"]');
        productInputs?.forEach(input => input.disabled = true);


        // Disable credit limit input
        const creditLimitInput = document.querySelector('input[name="credit_limit"]');
        if (creditLimitInput) creditLimitInput.disabled = true;

        // Disable staff select
        const staffSelect = assignAgent?.querySelector('select[name="staff_id"]');
        if (staffSelect) staffSelect.disabled = true;

        if (status === "Declined") {
            // Show decline fields
            reasonGroup.style.display = "block";
            toChangeGroup.style.display = "block";
            feedbackGroup.style.display = "block";

            // Make required
            reasonSelect.setAttribute("required", "required");
            toChangeSelect.setAttribute("required", "required");
            feedbackInput.setAttribute("required", "required");
        } 
        else if (status === "Accepted") {
            // Show approval fields
            assignAgent.style.display = "block";
            creditLimit.style.display = "block";
            ceilingPrice.style.display = "block";
            productList.style.display = "block";

            // Enable inputs for submission
            productInputs?.forEach(input => input.disabled = false);
            if (creditLimitInput) creditLimitInput.disabled = false;
            if (staffSelect) staffSelect.disabled = false;
        }
        // else (Pending or others): keep all hidden and disabled
    }

    // Initialize on load
    toggleFields();

    // Listen for status changes
    accStatus.addEventListener("change", toggleFields);
});