<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h2>Hi {{ $user->name }},</h2>
    
    <p>Thank you for registering! Please click the button below to verify your email address:</p>
    
    <a href="{{ $verificationUrl }}" 
       style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
        Verify Email Address
    </a>
    
    <p>Or copy and paste this URL into your browser:</p>
    <p>{{ $verificationUrl }}</p>
    
    <p>This link will expire in 24 hours.</p>
    
    <p>If you didn't create an account, please ignore this email.</p>
</body>
</html>