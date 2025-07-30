<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>

</head>
<body>
    <div class="container">
        <h1>Verify Your Email Address</h1>
        
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <p>Thank you for registering! Before getting started, please verify your email address by clicking on the link we just emailed to you.</p>

        <p>If you didn't receive the email, click the button below to request another:</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">
                Resend Verification Email
            </button>
        </form>

        {{-- <p style="margin-top: 20px;">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Sign Out
            </a>
        </p> --}}

        {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form> --}}
    </div>
</body>
</html>