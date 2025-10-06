document.addEventListener("DOMContentLoaded", function () {
    const creditInput = document.getElementById("credit_limit");

    creditInput.addEventListener("input", function () {
        let raw = this.value.replace(/,/g, ""); // strip commas first

        // allow only digits + max 1 decimal point
        if (!/^\d*\.?\d*$/.test(raw)) {
            raw = raw.replace(/[^0-9.]/g, ""); 
            let firstDot = raw.indexOf(".");
            raw = raw.substring(0, firstDot + 1) + raw.substring(firstDot + 1).replace(/\./g, "");
        }

        // split integer/decimal parts
        let parts = raw.split(".");
        let integerPart = parts[0] || "";
        let decimalPart = parts.length > 1 ? "." + parts[1] : "";

        // format integer with commas
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        this.value = integerPart + decimalPart;
    });
});