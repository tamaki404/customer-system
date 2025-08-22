        // Simplified Address Manager
        const AddressManager = {
            // Save all form data as default address
            saveDefaultAddress: function() {
                if (!this.validateForm()) {
                    return;
                }
                
                const addressData = this.getFormData();
                localStorage.setItem('default_address', JSON.stringify(addressData));
                
                this.showMessage('Default address saved successfully!', 'success');
                this.updateSavedAddressDisplay();
            },

            // Load default address
            loadDefaultAddress: function() {
                const savedAddress = localStorage.getItem('default_address');
                
                if (savedAddress) {
                    const addressData = JSON.parse(savedAddress);
                    this.populateForm(addressData);
                    this.showMessage('Default address loaded!', 'info');
                    return true;
                }
                
                this.showMessage('No default address found.', 'warning');
                return false;
            },

            // Clear saved address
            clearDefaultAddress: function() {
                localStorage.removeItem('default_address');
                this.showMessage('Default address cleared!', 'info');
                this.updateSavedAddressDisplay();
            },

            // Get all current form data
            getFormData: function() {
                return {
                    postal_code: document.getElementById('postal_code').value,
                    region: document.getElementById('region').value,
                    region_text: document.getElementById('region').selectedOptions[0]?.text || '',
                    province: document.getElementById('province').value,
                    province_text: document.getElementById('province').selectedOptions[0]?.text || '',
                    municipality: document.getElementById('municipality').value,
                    municipality_text: document.getElementById('municipality').selectedOptions[0]?.text || '',
                    barangay: document.getElementById('barangay').value,
                    barangay_text: document.getElementById('barangay').selectedOptions[0]?.text || '',
                    street: document.getElementById('street').value,
                    company_name: document.getElementById('company_name').value,
                    billing_address: document.getElementById('billing_address').value,
                    saved_at: new Date().toISOString()
                };
            },

            // Populate form with saved data
            populateForm: async function(addressData) {
                // Fill simple inputs
                document.getElementById('postal_code').value = addressData.postal_code || '';
                document.getElementById('street').value = addressData.street || '';
                document.getElementById('company_name').value = addressData.company_name || '';
                document.getElementById('billing_address').value = addressData.billing_address || '';
                
                // Handle cascading dropdowns
                if (addressData.region) {
                    document.getElementById('region').value = addressData.region;
                    
                    if (addressData.province) {
                        await this.loadProvinces(addressData.region);
                        document.getElementById('province').value = addressData.province;
                        
                        if (addressData.municipality) {
                            await this.loadMunicipalities(addressData.province);
                            document.getElementById('municipality').value = addressData.municipality;
                            
                            if (addressData.barangay) {
                                await this.loadBarangays(addressData.municipality);
                                document.getElementById('barangay').value = addressData.barangay;
                            }
                        }
                    }
                }
            },

            // Helper functions for dropdown loading
            loadProvinces: function(regionId) {
                return fetch(`/regions/${regionId}/provinces`)
                    .then(res => res.json())
                    .then(data => {
                        let provinceSelect = document.getElementById('province');
                        provinceSelect.innerHTML = '<option value="">-- Select Province --</option>';
                        data.forEach(province => {
                            provinceSelect.innerHTML += `<option value="${province.id}">${province.name}</option>`;
                        });
                    });
            },

            loadMunicipalities: function(provinceId) {
                return fetch(`/provinces/${provinceId}/municipalities`)
                    .then(res => res.json())
                    .then(data => {
                        let municipalitySelect = document.getElementById('municipality');
                        municipalitySelect.innerHTML = '<option value="">-- Select City / Municipality --</option>';
                        data.forEach(municipality => {
                            municipalitySelect.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
                        });
                    });
            },

            loadBarangays: function(municipalityId) {
                return fetch(`/municipalities/${municipalityId}/barangays`)
                    .then(res => res.json())
                    .then(data => {
                        let barangaySelect = document.getElementById('barangay');
                        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                        data.forEach(barangay => {
                            barangaySelect.innerHTML += `<option value="${barangay.id}">${barangay.name}</option>`;
                        });
                    });
            },

            // Form validation
            validateForm: function() {
                const requiredFields = ['postal_code', 'street', 'company_name', 'billing_address'];
                const missingFields = [];
                
                requiredFields.forEach(field => {
                    const element = document.getElementById(field);
                    if (!element.value.trim()) {
                        missingFields.push(field.replace('_', ' ').toUpperCase());
                    }
                });
                
                if (missingFields.length > 0) {
                    this.showMessage(`Please fill in required fields: ${missingFields.join(', ')}`, 'error');
                    return false;
                }
                
                return true;
            },

            // Show notification messages
            showMessage: function(message, type) {
                const existingMessage = document.getElementById('address-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.id = 'address-message';
                messageDiv.style.cssText = `
                    color: #fff;
                    background-color: #f8d7da;
                    padding: 10px;
                    margin: 10px 0;
                    border-radius: 10px;
                    font-size: 14px;
                    z-index: 9999;
                    position: fixed;
                    top: 50px;
                    left: 50%;
                    width: 90%;
                    transform: translateX(-50%);
                    max-width: 800px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                `;
                
                switch(type) {
                    case 'success': messageDiv.style.backgroundColor = '#28a745'; break;
                    case 'error': messageDiv.style.backgroundColor = '#dc3545'; break;
                    case 'warning': 
                        messageDiv.style.backgroundColor = '#dc3545';
                        messageDiv.style.color = '#fff';
                        break;
                    case 'info': messageDiv.style.backgroundColor = '#28a745'; break;
                    default: messageDiv.style.backgroundColor = '#6c757d';
                }
                
                messageDiv.textContent = message;
                document.body.appendChild(messageDiv);
                
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 3000);
            },

            // Update saved address display
            updateSavedAddressDisplay: function() {
                const displayDiv = document.getElementById('savedAddressDisplay');
                const addressPreview = document.getElementById('addressPreview');
                const savedAddress = localStorage.getItem('default_address');
                
                if (savedAddress) {
                    const addr = JSON.parse(savedAddress);
                    displayDiv.style.display = 'block';
                    
                    addressPreview.innerHTML = `
                        <strong>Saved Default Address:</strong><br>
                        <strong>Street:</strong> ${addr.street}<br>
                        <strong>Location:</strong> ${addr.barangay_text}, ${addr.municipality_text}, ${addr.province_text}, ${addr.region_text}<br>
                        <strong>Postal Code:</strong> ${addr.postal_code}<br>
                        <strong>Company:</strong> ${addr.company_name}<br>
                        <strong>Billing Address:</strong> ${addr.billing_address}<br>
                        <small style="color: #666;">Saved: ${new Date(addr.saved_at).toLocaleString()}</small>
                    `;
                } else {
                    displayDiv.style.display = 'none';
                }
            },

            // Auto-save functionality
            checkAutoSave: function() {
                const autoSaveCheckbox = document.getElementById('auto_save');
                if (autoSaveCheckbox.checked && this.isFormComplete()) {
                    this.saveDefaultAddress();
                }
            },

            // Check if form is reasonably complete
            isFormComplete: function() {
                const requiredFields = ['postal_code', 'street', 'company_name', 'billing_address'];
                return requiredFields.every(field => {
                    const element = document.getElementById(field);
                    return element.value.trim() !== '';
                });
            }
        };

        // Your existing cascade functionality
        document.getElementById('region').addEventListener('change', function() {
            let regionId = this.value;
            fetch(`/regions/${regionId}/provinces`)
                .then(res => res.json())
                .then(data => {
                    let provinceSelect = document.getElementById('province');
                    provinceSelect.innerHTML = '<option value="">-- Select Province --</option>';
                    data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.id}">${province.name}</option>`;
                    });
                    document.getElementById('municipality').innerHTML = '<option value="">-- Select City / Municipality --</option>';
                    document.getElementById('barangay').innerHTML = '<option value="">-- Select Barangay --</option>';
                    
                    // Check auto-save after dropdown change
                    setTimeout(() => AddressManager.checkAutoSave(), 500);
                })
                .catch(error => {
                    console.error('Error fetching provinces:', error);
                    AddressManager.showMessage('Error loading provinces', 'error');
                });
        });

        document.getElementById('province').addEventListener('change', function() {
            let provinceId = this.value;
            fetch(`/provinces/${provinceId}/municipalities`)
                .then(res => res.json())
                .then(data => {
                    let municipalitySelect = document.getElementById('municipality');
                    municipalitySelect.innerHTML = '<option value="">-- Select City / Municipality --</option>';
                    data.forEach(municipality => {
                        municipalitySelect.innerHTML += `<option value="${municipality.id}">${municipality.name}</option>`;
                    });
                    document.getElementById('barangay').innerHTML = '<option value="">-- Select Barangay --</option>';
                    
                    setTimeout(() => AddressManager.checkAutoSave(), 500);
                })
                .catch(error => {
                    console.error('Error fetching municipalities:', error);
                    AddressManager.showMessage('Error loading municipalities', 'error');
                });
        });

        document.getElementById('municipality').addEventListener('change', function() {
            let municipalityId = this.value;
            fetch(`/municipalities/${municipalityId}/barangays`)
                .then(res => res.json())
                .then(data => {
                    let barangaySelect = document.getElementById('barangay');
                    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                    data.forEach(barangay => {
                        barangaySelect.innerHTML += `<option value="${barangay.id}">${barangay.name}</option>`;
                    });
                    
                    setTimeout(() => AddressManager.checkAutoSave(), 500);
                })
                .catch(error => {
                    console.error('Error fetching barangays:', error);
                    AddressManager.showMessage('Error loading barangays', 'error');
                });
        });

        // Global functions
        function saveDefaultAddress() {
            AddressManager.saveDefaultAddress();
        }

        function loadDefaultAddress() {
            AddressManager.loadDefaultAddress();
        }

        function clearDefaultAddress() {
            if (confirm('Are you sure you want to clear your saved default address?')) {
                AddressManager.clearDefaultAddress();
            }
        }

        // Add auto-save listeners to input fields
        document.addEventListener('DOMContentLoaded', function() {
            AddressManager.updateSavedAddressDisplay();
            
            // Add change listeners for auto-save
            const inputFields = ['postal_code', 'street', 'billing_address'];
            inputFields.forEach(fieldId => {
                document.getElementById(fieldId).addEventListener('input', function() {
                    setTimeout(() => AddressManager.checkAutoSave(), 1000); // Delay to avoid too frequent saves
                });
            });
        });