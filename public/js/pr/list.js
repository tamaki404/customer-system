
            document.addEventListener('DOMContentLoaded', function() {
                // Generate next 14 days for selection
                generatePreferredDays();
                
                // Setup event listeners
                setupProductCheckboxes();
                setupQuantityInputs();
                setupFormValidation();
            });

            function generatePreferredDays() {
                const container = document.getElementById('preferredDaysContainer');
                const today = new Date();
                
                for (let i = 1; i <= 14; i++) {
                    const date = new Date(today);
                    date.setDate(today.getDate() + i);
                    
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'day-checkbox-container';
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'preferred_days[]';
                    checkbox.value = date.toISOString().split('T')[0];
                    checkbox.id = `day_${i}`;
                    checkbox.className = 'form-check-input preferred-day-checkbox';
                    
                    const label = document.createElement('label');
                    label.htmlFor = `day_${i}`;
                    label.className = 'day-label';
                    label.textContent = date.toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric',
                        weekday: 'short'
                    });
                    
                    dayDiv.appendChild(checkbox);
                    dayDiv.appendChild(label);
                    
                    // Click handler for the container
                    dayDiv.addEventListener('click', function(e) {
                        if (e.target !== checkbox) {
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                    
                    // Change handler for checkbox
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            dayDiv.classList.add('selected');
                        } else {
                            dayDiv.classList.remove('selected');
                        }
                        validateForm();
                    });
                    
                    container.appendChild(dayDiv);
                }
            }

            function setupProductCheckboxes() {
                const checkboxes = document.querySelectorAll('.product-checkbox');
                
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const row = this.closest('tr');
                        const headsInput = row.querySelector('.planned-heads');
                        const kilosInput = row.querySelector('.planned-kilos');
                        
                        if (this.checked) {
                            headsInput.disabled = false;
                            kilosInput.disabled = false;
                            row.style.backgroundColor = '#f8f9fa';
                        } else {
                            headsInput.disabled = true;
                            kilosInput.disabled = true;
                            headsInput.value = '';
                            kilosInput.value = '';
                            row.style.backgroundColor = '';
                            updateRowTotal(row);
                        }
                        
                        validateForm();
                    });
                });
            }

            function setupQuantityInputs() {
                const headsInputs = document.querySelectorAll('.planned-heads');
                const kilosInputs = document.querySelectorAll('.planned-kilos');
                
                [...headsInputs, ...kilosInputs].forEach(input => {
                    input.addEventListener('input', function() {
                        const row = this.closest('tr');
                        updateRowTotal(row);
                        validateForm();
                    });
                });
            }

            function updateRowTotal(row) {
                const checkbox = row.querySelector('.product-checkbox');
                const headsInput = row.querySelector('.planned-heads');
                const kilosInput = row.querySelector('.planned-kilos');
                const totalCell = row.querySelector('.row-total');
                const price = parseFloat(checkbox.dataset.price) || 0;
                const measurement = checkbox.dataset.measurement;
                
                let total = 0;
                
                if (measurement === 'Heads') {
                    const heads = parseFloat(headsInput.value) || 0;
                    total = heads * price;
                } else if (measurement === 'Kilos') {
                    const kilos = parseFloat(kilosInput.value) || 0;
                    total = kilos * price;
                }
                
                totalCell.textContent = '₱' + total.toFixed(2);
                updateGrandTotal();
            }

            function updateGrandTotal() {
                const rows = document.querySelectorAll('#productsTable tbody tr');
                let grandTotal = 0;
                
                rows.forEach(row => {
                    const checkbox = row.querySelector('.product-checkbox');
                    if (checkbox.checked) {
                        const totalText = row.querySelector('.row-total').textContent;
                        const total = parseFloat(totalText.replace('₱', '').replace(',', '')) || 0;
                        grandTotal += total;
                    }
                });
                
                document.getElementById('grandTotal').textContent = '₱' + grandTotal.toFixed(2);
            }

            function validateForm() {
                const selectedDays = document.querySelectorAll('.preferred-day-checkbox:checked');
                const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
                const submitBtn = document.getElementById('submitBtn');
                const daysError = document.getElementById('daysError');
                const productsError = document.getElementById('productsError');
                
                let isValid = true;
                
                // Validate days
                if (selectedDays.length === 0) {
                    daysError.textContent = 'Please select at least one delivery day';
                    daysError.style.display = 'block';
                    isValid = false;
                } else {
                    daysError.style.display = 'none';
                }
                
                // Validate products
                if (selectedProducts.length === 0) {
                    productsError.textContent = 'Please select at least one product';
                    productsError.style.display = 'block';
                    isValid = false;
                } else {
                    // Check if selected products have quantities
                    let hasQuantities = false;
                    selectedProducts.forEach(checkbox => {
                        const row = checkbox.closest('tr');
                        const heads = parseFloat(row.querySelector('.planned-heads').value) || 0;
                        const kilos = parseFloat(row.querySelector('.planned-kilos').value) || 0;
                        
                        if (heads > 0 || kilos > 0) {
                            hasQuantities = true;
                        }
                    });
                    
                    if (!hasQuantities) {
                        productsError.textContent = 'Please enter quantities for selected products';
                        productsError.style.display = 'block';
                        isValid = false;
                    } else {
                        productsError.style.display = 'none';
                    }
                }
                
                submitBtn.disabled = !isValid;
            }
