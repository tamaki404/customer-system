<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fa; margin:0; padding:0; }
        .container { max-width:600px; margin:30px auto; background:#ffffff; padding:30px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        .header { text-align:center; padding-bottom:20px; }
        .header img { max-width:120px; }
        h1 { font-size:20px; color:#333; }
        p { font-size:14px; color:#555; line-height:1.6; }
        .btn { display:inline-block; background:#f5922a; color:#fff; padding:12px 24px; text-decoration:none; border-radius:4px; font-weight:bold; margin:20px 0; }
        .btn:hover { background:#d9791d; }
        .footer { font-size:12px; color:#888; text-align:center; margin-top:30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- <img src="https://sunny&scramble.com/logo.png" alt="Company Logo"> --}}
        </div>
        <h1>Welcome to YourCompany!</h1>
        <p>Thanks for registering. Please confirm your email address to activate your account:</p>
        <p style="text-align:center;">
            <a class="btn" href="{{ $verifyUrl }}" target="_blank">Verify Email</a>
        </p>
        <p>If the button doesn’t work, copy and paste this link into your browser:</p>
        <p><a href="{{ $verifyUrl }}" target="_blank">{{ $verifyUrl }}</a></p>
        <div class="footer">
            <p>If you did not create this account, you can safely ignore this email.</p>
            <p>© {{ date('Y') }} SUnny&Scramble. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
