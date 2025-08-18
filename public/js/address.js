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
            // Clear subsequent dropdowns
            document.getElementById('municipality').innerHTML = '<option value="">-- Select City / Municipality --</option>';
            document.getElementById('barangay').innerHTML = '<option value="">-- Select Barangay --</option>';
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
            // Clear subsequent dropdown
            document.getElementById('barangay').innerHTML = '<option value="">-- Select Barangay --</option>';
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
        });
});


