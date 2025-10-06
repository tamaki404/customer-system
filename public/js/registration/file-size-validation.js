document.addEventListener('DOMContentLoaded', function(){
    var MAX = 2 * 1024 * 1024; // 2MB

    function bind(input){
        if (!input) return;
        input.addEventListener('change', function(){
            var files = input.files || [];
            var tooLarge = false;
            for (var i = 0; i < files.length; i++) {
                if (files[i].size > MAX) { tooLarge = true; break; }
            }
            if (tooLarge) {
                input.value = '';
                input.setCustomValidity('Each file must be 2MB or less.');
                input.reportValidity();
            } else {
                input.setCustomValidity('');
            }
        });
    }

    // Signup main company image
    bind(document.getElementById('company-image'));

    // Signup documents (multiple input groups)
    var docIds = [
        'affidavit-of-loss',
        'certificate-of-registration',
        'barangay-clearance',
        'business-permit',
        'sanitary-permit',
        'environmental-management-permit',
        'community-tax-certificate',
        'product-requirements'
    ];
    docIds.forEach(function(id){ bind(document.getElementById(id)); });

    // Add staff image input
    var addStaffModal = document.getElementById('add-staff-modal');
    if (addStaffModal){
        var staffImage = addStaffModal.querySelector('input[type="file"][name="image"]');
        bind(staffImage);
    }
});


