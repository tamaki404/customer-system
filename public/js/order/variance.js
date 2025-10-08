document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.received-input');

    inputs.forEach(input => {
        input.addEventListener('input', function () {
            const planned = parseFloat(this.dataset.planned) || 0;
            const received = parseFloat(this.value) || 0;
            const varianceCell = this.closest('tr').querySelector('.variance-text');

            const variance = received - planned;

            if (this.value === '') {
                varianceCell.textContent = '—';
                varianceCell.classList.remove('text-success', 'text-danger', 'text-muted');
                varianceCell.classList.add('text-muted');
                return;
            }

            if (variance === 0) {
                varianceCell.textContent = 'Exact';
                varianceCell.classList.remove('text-danger', 'text-muted');
                varianceCell.classList.add('text-success');
            } else {
                varianceCell.textContent = (variance > 0 ? '+' : '') + variance.toFixed(2);
                varianceCell.classList.remove('text-success', 'text-muted');
                varianceCell.classList.add('text-danger');
            }
        });
    });
});
