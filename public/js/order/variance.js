document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.received-input');

    function calculateVariance(input) {
        const planned = parseFloat(input.dataset.planned) || 0;
        const received = parseFloat(input.value) || 0;
        const variance = received - planned;

        // Find the correct variance element
        const row = input.closest('tr');
        let varianceCell;

        if (input.placeholder.toLowerCase().includes('heads')) {
            varianceCell = row.querySelector('.variance-text-heads');
        } else if (input.placeholder.toLowerCase().includes('kilos')) {
            varianceCell = row.querySelector('.variance-text-kilos');
        } else {
            varianceCell = row.querySelector('.variance-text');
        }

        if (!varianceCell) return;

        // Reset state if empty
        if (input.value === '' || input.value === null) {
            varianceCell.textContent = '—';
            varianceCell.className = varianceCell.className.split(' ').filter(c => !c.startsWith('text-')).join(' ') + ' text-muted';
            return;
        }

        // Determine unit
        const unit = input.placeholder.toLowerCase().includes('heads') ? ' heads' :
                     input.placeholder.toLowerCase().includes('kilos') ? ' kg' : '';

        // Update text and color based on variance
        let colorClass = '';
        let displayText = '';

        if (variance === 0) {
            displayText = 'Exact';
            colorClass = 'text-success';
        } else if (variance > 0) {
            displayText = `+${variance.toFixed(2)}${unit}`;
            colorClass = 'text-danger';
        } else {
            displayText = `${variance.toFixed(2)}${unit}`;
            colorClass = 'text-danger';
        }

        // Remove all text-* classes and add the new one
        varianceCell.className = varianceCell.className.split(' ').filter(c => !c.startsWith('text-')).join(' ') + ' ' + colorClass;
        varianceCell.textContent = displayText;
    }

    // Attach event listeners to all inputs
    inputs.forEach(input => {
        // Handle input event (fires on every change)
        input.addEventListener('input', function () {
            calculateVariance(this);
        });

        // Handle change event (fires when input loses focus)
        input.addEventListener('change', function () {
            calculateVariance(this);
        });

        // Handle keyup event for immediate feedback
        input.addEventListener('keyup', function () {
            calculateVariance(this);
        });
    });
});