<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>Sign Up</title>
</head>
<body>
<form method="POST" action="/register-user" class="logForm" id="registerForm" enctype="multipart/form-data">
    @csrf
    <h2>Create Account</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod</p>
    <span><p>Already have an account?</p> <a href="/">Sign In</a></span>

    <input type="text" name="username" id="username" placeholder="Username" required>
    <small id="username-feedback" style="color:red;"></small>

    <input type="text" name="store_name" id="store_name" placeholder="Store name" required>
    <small id="store-name-feedback" style="color:red;"></small>


    


    <input type="password" name="password" id="password" placeholder="Password" required>
    <small id="password-feedback" style="color:red;"></small>

    <input type="text" name="user_type" value="Customer" hidden>

    <input type="file" name="image" accept="image/*" required>
    <input type="text" name="acc_status"  value="Pending" hidden>

    <input type="text" name="action_by" value="Admin" hidden>

    <button type="submit">Sign Up</button>
</form>

<script>
    const passwordInput = document.getElementById('password');
    const passwordFeedback = document.getElementById('password-feedback');
    const usernameInput = document.getElementById('username');
    const usernameFeedback = document.getElementById('username-feedback');
    const form = document.getElementById('registerForm');

    passwordInput.addEventListener('input', function () {
        if (passwordInput.value.length < 8) {
            passwordFeedback.textContent = "Password must be at least 8 characters.";
        } else {
            passwordFeedback.textContent = "";
        }
    });

    usernameInput.addEventListener('blur', function () {
        const username = usernameInput.value;
        fetch(`/check-username?username=${username}`)
            .then(res => res.json())
            .then(data => {
                if (!data.available) {
                    usernameFeedback.textContent = "Username already taken.";
                } else {
                    usernameFeedback.textContent = "";
                }
            });
    });

    form.addEventListener('submit', function (e) {
        if (passwordInput.value.length < 8) {
            e.preventDefault();
            passwordFeedback.textContent = "Password must be at least 8 characters.";
        }
    });
</script>


</body>
</html>