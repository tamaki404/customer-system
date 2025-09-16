<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify your email</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .btn { background:#f5922a;color:#fff;padding:10px 16px;text-decoration:none;border-radius:4px;display:inline-block; }
    </style>
    </head>
<body>
    <p>Thanks for registering. Please verify your email by clicking the button below:</p>
    <p><a class="btn" href="{{ $verifyUrl }}" target="_blank">Verify Email</a></p>
    <p>If the button doesn't work, copy and paste this link into your browser:</p>
    <p><a href="{{ $verifyUrl }}" target="_blank">{{ $verifyUrl }}</a></p>
</body>
</html>


