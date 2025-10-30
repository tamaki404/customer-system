    document.getElementById("copy-btn").addEventListener("click", function () {
        const productId = document.getElementById("product_id").innerText;

        navigator.clipboard.writeText(productId).then(() => {
            const msg = document.getElementById("flash-message");
            msg.innerText = "Copied!";
            msg.classList.add("alert-success");
            msg.style.display = "block";

            setTimeout(() => {
                msg.style.display = "none";
                msg.classList.remove("alert-success");
            }, 2500);
        });
    });