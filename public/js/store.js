        document.getElementById('submitForm').addEventListener('submit', function (e) {
            let submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;          
                submitBtn.innerText = "Processing..."; 
            }
        });