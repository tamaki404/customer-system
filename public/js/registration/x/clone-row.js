    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('add-signatory-btn');
        const container = document.getElementById('signatory-container');

        addBtn.addEventListener('click', function () {
            // Count current signatory rows
            const rows = container.querySelectorAll('.signatory-row');
            if (rows.length >= 3) {
                alert('Maximum of 3 signatories allowed.');
                return;
            }

            // Clone the first row
            const newRow = rows[0].cloneNode(true);

            // Clear the inputs in the new row
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.type === 'file') {
                    input.value = null;
                } else {
                    input.value = '';
                }
            });

            // Insert the new row before the button
            container.insertBefore(newRow, addBtn);
        });
    });