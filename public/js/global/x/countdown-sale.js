document.addEventListener("DOMContentLoaded", function () {
    function format(ms) {
        const total = Math.floor(ms / 1000);
        const days = Math.floor(total / 86400);
        const hours = Math.floor((total % 86400) / 3600);
        const minutes = Math.floor((total % 3600) / 60);
        const seconds = total % 60;
        return `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }

    function updateAllCountdowns() {
        document.querySelectorAll('.countdown').forEach(el => {
            const start = Number(el.dataset.start);
            const end   = Number(el.dataset.end);
            const now   = Date.now();

            if (now < start) {
                el.innerHTML = "Sale starts in " + format(start - now);
            } else if (now >= start && now <= end) {
                el.innerHTML = "Sale ends in " + format(end - now);
            } else {
                el.innerHTML = "Sale has ended";
            }
        });
    }

    updateAllCountdowns();
    setInterval(updateAllCountdowns, 1000);
});

