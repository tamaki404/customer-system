
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.received-input');
    const EPS = 1e-6; // threshold for float zero comparison

    function removeTextClasses(el) {
        Array.from(el.classList).forEach(cls => {
            if (cls.startsWith('text-')) el.classList.remove(cls);
        });
    }

    function formatNumber(absValue, isHeads) {
        return isHeads ? String(Math.round(absValue)) : Number(absValue).toFixed(2);
    }

    function calculateVariance(input) {
        const plannedRaw = input.dataset.planned;
        const planned = plannedRaw === undefined || plannedRaw === '' ? 0 : parseFloat(plannedRaw) || 0;
        const receivedRaw = input.value;
        const received = (receivedRaw === '' || receivedRaw === null) ? null : parseFloat(receivedRaw);

        const inputName = input.getAttribute('name') || '';
        const row = input.closest('tr');
        if (!row) return;

        // Prefer specific variance elements, but fallback to generic .variance-text
        let varianceCell = null;
        if (inputName.includes('received_heads')) {
            varianceCell = row.querySelector('.variance-text-heads') || row.querySelector('.variance-text');
        } else if (inputName.includes('received_kilos')) {
            varianceCell = row.querySelector('.variance-text-kilos') || row.querySelector('.variance-text');
        } else {
            varianceCell = row.querySelector('.variance-text') || row.querySelector('.variance-text-heads') || row.querySelector('.variance-text-kilos');
        }
        if (!varianceCell) return;

        // Reset to muted if input empty
        removeTextClasses(varianceCell);
        varianceCell.classList.add('text-muted');

        if (received === null) {
            varianceCell.textContent = '—';
            return;
        }

        const variance = received - planned; // positive => surplus, negative => shortage
        const isHeads = inputName.includes('received_heads');

        if (Math.abs(variance) < EPS) {
            // treat as zero
            removeTextClasses(varianceCell);
            varianceCell.classList.add('text-success');
            varianceCell.textContent = 'Exact';
            return;
        }

        const absVal = Math.abs(variance);
        const sign = variance > 0 ? '+' : '-';
        const unit = isHeads ? ' heads' : ' kg';
        const display = sign + formatNumber(absVal, isHeads) + unit;

        // color: shortage (negative) => danger, surplus (positive) => primary (adjust as you like)
        removeTextClasses(varianceCell);
        varianceCell.classList.add(variance > 0 ? 'text-primary' : 'text-danger');
        varianceCell.textContent = display;
    }

    // Attach listeners
    inputs.forEach(input => {
        input.addEventListener('input', () => calculateVariance(input));
        input.addEventListener('change', () => calculateVariance(input));
        // initial calculate if value present
        if (input.value !== '' && input.value !== null) {
            calculateVariance(input);
        }
    });
});
