// get all the inputs and feedback elements we need
const passwordInput = document.getElementById('password');
const passwordFeedback = document.getElementById('password-feedback');
const confirmPasswordInput = document.getElementById('confirmPassword');
const confirmPasswordFeedback = document.getElementById('confirm-password-feedback');
const usernameInput = document.getElementById('username');
const usernameFeedback = document.getElementById('username-feedback');
const form = document.getElementById('registerForm');
const companyImageInput = document.getElementById('companyImage');
const companyImageFeedback = document.getElementById('company-image-feedback');

// variables to track if each part is valid
let isUsernameValid = false;
let isPasswordValid = false;
let isPasswordConfirmValid = false;
let isImageValid = false;
let usernameTimeout;

// check password length while typing
passwordInput.addEventListener('input', function () {
    if (passwordInput.value.length < 8) {
        passwordFeedback.textContent = "Password must be at least 8 characters.";
        passwordFeedback.style.color = "red";
        isPasswordValid = false;
    } else {
        passwordFeedback.textContent = "";
        isPasswordValid = true;
    }

    // also check if the confirm password matches
    checkPasswordConfirmation();
});

// run confirm password check every time user types in it
confirmPasswordInput.addEventListener('input', function () {
    checkPasswordConfirmation();
});

// function to see if passwords match
function checkPasswordConfirmation() {
    if (confirmPasswordInput.value === '') {
        confirmPasswordFeedback.textContent = "";
        isPasswordConfirmValid = false;
        return;
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordFeedback.textContent = "Passwords do not match";
        confirmPasswordFeedback.style.color = "red";
        isPasswordConfirmValid = false;
    } else {
        confirmPasswordFeedback.textContent = "✓ Passwords match";
        confirmPasswordFeedback.style.color = "green";
        isPasswordConfirmValid = true;
    }
}

// validate username when typing
usernameInput.addEventListener('input', function () {
    const username = usernameInput.value.trim();

    clearTimeout(usernameTimeout); // cancel previous check if still waiting

    if (!username) {
        usernameFeedback.textContent = "";
        isUsernameValid = false;
        return;
    }

    if (username.length < 4) {
        usernameFeedback.textContent = "Username must be at least 4 characters.";
        usernameFeedback.style.color = "red";
        isUsernameValid = false;
        return;
    }

    if (username.length > 15) {
        usernameFeedback.textContent = "Username must not exceed 15 characters.";
        usernameFeedback.style.color = "red";
        isUsernameValid = false;
        return;
    }

    // delay actual check to avoid hitting the server every keystroke
    usernameTimeout = setTimeout(() => {
        checkUsernameAvailability(username);
    }, 500);
});

// function that sends request to the backend to see if username is available
function checkUsernameAvailability(username) {
    usernameFeedback.textContent = "Checking availability...";
    usernameFeedback.style.color = "blue";

    fetch(`/check-username?username=${encodeURIComponent(username)}`)
        .then(response => {
            if (!response.ok) throw new Error('network error');
            return response.json();
        })
        .then(data => {
            if (data.available) {
                usernameFeedback.textContent = "✓ Username is available";
                usernameFeedback.style.color = "green";
                isUsernameValid = true;
            } else {
                usernameFeedback.textContent = data.message || "Username is already taken";
                usernameFeedback.style.color = "red";
                isUsernameValid = false;
            }
        })
        .catch(error => {
            console.error('username check failed:', error);
            usernameFeedback.textContent = "Error checking username availability";
            usernameFeedback.style.color = "orange";
            isUsernameValid = false;
        });
}

// do another username check when leaving the field
usernameInput.addEventListener('blur', function () {
    const username = usernameInput.value.trim();
    if (username && username.length >= 4 && username.length <= 15) {
        clearTimeout(usernameTimeout);
        checkUsernameAvailability(username);
    }
});

// validate image when user picks a file
companyImageInput.addEventListener('change', function () {
    const file = companyImageInput.files[0];
    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            companyImageFeedback.style.display = 'block';
            companyImageFeedback.textContent = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
            companyImageInput.value = '';
            isImageValid = false;
        } else {
            companyImageFeedback.textContent = "";
            isImageValid = true;
        }
    } else {
        companyImageFeedback.textContent = "";
        isImageValid = true; // no image selected yet, but maybe not required?
    }
});

// main form submit handler
// main form submit handler
form.addEventListener('submit', function (e) {
    let hasErrors = false;
    let errorMessages = [];

    // re-check password
    if (passwordInput.value.length < 8) {
        passwordFeedback.textContent = "Password must be at least 8 characters.";
        passwordFeedback.style.color = "red";
        hasErrors = true;
        errorMessages.push('Password too short');
    }

    // check if confirm password was filled
    if (confirmPasswordInput.value === '') {
        hasErrors = true;
        errorMessages.push('Password confirmation required');
        alert('Please confirm your password');
    } else if (passwordInput.value !== confirmPasswordInput.value) {
        hasErrors = true;
        errorMessages.push('Passwords do not match');
        alert('Passwords do not match');
    }

    // check username availability validation
    if (!isUsernameValid) {
        if (usernameInput.value.trim() === '') {
            usernameFeedback.textContent = "Username is required.";
        } else {
            usernameFeedback.textContent = "Please wait for username validation or choose a different username.";
        }
        usernameFeedback.style.color = "red";
        hasErrors = true;
        errorMessages.push('Username invalid');
    }

    // make sure all the required fields are filled
    const requiredFields = ['store_name', 'address', 'name', 'email'];
    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field.value.trim()) {
            hasErrors = true;
            errorMessages.push(`${fieldName} is required`);
        }
    });

    // ✅ no need to check if image is required anymore

    // if anything failed, stop form from submitting
    if (hasErrors) {
        e.preventDefault();
        alert('Please fix the following errors:\n' + errorMessages.join('\n'));
        return false;
    }

    // otherwise everything's good
    return true;
});


// make sure image validity is updated on page load (in case file is preloaded)
document.addEventListener('DOMContentLoaded', function() {
    if (companyImageInput.files[0]) {
        isImageValid = true;
    }
});
