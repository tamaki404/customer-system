const profileImageInput = document.getElementById('profileImageInput');
const profileImagePreview = document.getElementById('profileImagePreview');
const editAvatarBtn = document.getElementById('editAvatarBtn');
const avatarActions = document.getElementById('avatarActions');
const cancelImageBtn = document.getElementById('cancelImageBtn');

const originalImageSrc = profileImagePreview.src;

editAvatarBtn.addEventListener('click', () => {
    profileImageInput.click();
});

profileImageInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
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
    profileImageInput.value = ""; 
    avatarActions.style.display = "none";
    editAvatarBtn.style.display = "block";
});