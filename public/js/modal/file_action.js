
    document.querySelectorAll('.open-modify-modal').forEach(button => {
        button.addEventListener('click', function () {
            confirmAction = this.closest('form'); 
            let modalEl = document.getElementById('fileActionModal');
            let modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });

