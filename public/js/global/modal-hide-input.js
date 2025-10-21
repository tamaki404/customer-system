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
        [assignAgent, creditLimit, ceilingPrice, productList].forEach(el => el?.removeAttribute("required"));

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

            // Make required
            assignAgent.setAttribute("required", "required");
            creditLimit.setAttribute("required", "required");
            ceilingPrice.setAttribute("required", "required");
            productList.setAttribute("required", "required");
        }
        // else (Pending or others): keep all hidden
    }

    // Initialize on load
    toggleFields();

    // Listen for status changes
    accStatus.addEventListener("change", toggleFields);
});
