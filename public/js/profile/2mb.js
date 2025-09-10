const companyImage = document.getElementById('companyImage');
const profileImagePreview = document.getElementById('profileImagePreview');
const editAvatarBtn = document.getElementById('editAvatarBtn');
const avatarActions = document.getElementById('avatarActions');
const cancelImageBtn = document.getElementById('cancelImageBtn');
const errorMsg = document.getElementById('file-error');

const originalImageSrc = profileImagePreview.src;

editAvatarBtn.addEventListener('click', () => {
    companyImage.click();
});
companyImage.addEventListener('change', (event) => {
    const file = event.target.files[0];
    
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            errorMsg.textContent = 'File must not exceed 2MB';
            errorMsg.style.display = 'block';
            avatarActions.style.display = 'none';
            companyImage.value = '';
            return;
        }
        
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
        
        const reader = new FileReader();
        reader.onload = (e) => {
            profileImagePreview.src = e.target.result;
            editAvatarBtn.style.display = "none";
            avatarActions.style.display = "flex";
        };
        reader.readAsDataURL(file);
    }
});

cancelImageBtn.addEventListener('click', () => {
    profileImagePreview.src = originalImageSrc;
    companyImage.value = "";
    avatarActions.style.display = "none";
    editAvatarBtn.style.display = "block";
    
    errorMsg.textContent = '';
    errorMsg.style.display = 'none';
});