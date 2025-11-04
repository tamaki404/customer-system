function shortNum(num) {
    if (num >= 1e9) return (num / 1e9).toFixed(1).replace(/\.0$/, '') + "B";
    if (num >= 1e6) return (num / 1e6).toFixed(1).replace(/\.0$/, '') + "M";
    if (num >= 1e3) return (num / 1e3).toFixed(1).replace(/\.0$/, '') + "K";
    return num;
}

document.addEventListener("DOMContentLoaded", () => {
    const currentSaleEl = document.getElementById('currentSale');
    const lastWeekSaleEl = document.getElementById('lastWeekSale');

    if (currentSaleEl) {
        currentSaleEl.textContent = shortNum(parseFloat(currentSaleEl.textContent));
    }

    if (lastWeekSaleEl) {
        lastWeekSaleEl.textContent = shortNum(parseFloat(lastWeekSaleEl.textContent));
    }
});
