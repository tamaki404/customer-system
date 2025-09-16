<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="{{ asset('css/registration/signup.css') }}">
</head>
<body>
    <div class="log-form-bg" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
        <p style="font-size: 16px; color: #333;">
            U have to evrify your emial first to continue
        </p>
        @if(session('success'))
            <div class="alert alert-success auto-hide" style="margin-left: 10px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger auto-hide" style="margin-left: 10px;">{{ session('error') }}</div>
        @endif
    </div>
</body>
</html>


