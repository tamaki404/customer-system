    let confirmAction = null;

    document.querySelectorAll('.open-confirm').forEach(button => {
        button.addEventListener('click', function () {
            confirmAction = this.closest('form'); 
            let modalEl = document.getElementById('confirmModal');
            let modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', function () {
        if (confirmAction) {
            let btn = confirmAction.querySelector('button');
            btn.disabled = true;
            btn.innerText = "Processing...";

            let modalEl = document.getElementById('confirmModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            confirmAction.submit();
        }
    });

    document.getElementById('cancelBtn').addEventListener('click', function () {
        location.reload();
    });

    setTimeout(() => {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);