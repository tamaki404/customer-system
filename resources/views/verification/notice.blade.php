<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/registration/signup.css') }}">
</head>
<body>
    <div class="log-form-bg" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">

        <div class="email-display">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image" width="100" class="ownerImage">
            <strong style="display: flex; flex-direction: row; align-items: center; justfy-contents: center; gap: 5px;">
                <span class="material-symbols-outlined">mark_email_unread</span>
                Verify your email address
            </strong>
            <p>Thankyou for registering with us! Kindly check your email for verification.</p>

            <p style="margin-top: 10px;">Dont see any email? check you spam! or request a new one</p>
            <button class="signin-btn" onclick="location.href='{{ route('signin') }}'">
                <p>
                    <span class="material-symbols-outlined" style="font-size: 14px;">arrow_back_ios</span>
                    <span>Signin page</span>
                </p>
            </button>
        </div>

    </div>
</body>
</html>


