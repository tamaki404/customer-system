<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
    <title>Verify Your Email</title>

</head>
<body>


    <div class="verifyFrame">
        <img src="{{ asset(path: 'assets/sunnyLogo1.png') }}" alt="Logo" class="logo">
        <h2>Verify your email</h2> 
        <p>We've sent you an email, click on it to verify your account</p>

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




        <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn" id="resendBtn">
                Resend Verification Email
            </button>
        </form>

        <script>
        // disable resend button for 5 minutes after click (persisted in localStorage)
        const RESEND_KEY = 'verifyEmailResendTimestamp';
        const btn = document.getElementById('resendBtn');
        const form = document.getElementById('resendForm');
        const DISABLE_MS = 5 * 60 * 1000; // 5 minutes

        function updateResendBtn() {
            const last = localStorage.getItem(RESEND_KEY);
            if (last) {
                const diff = Date.now() - parseInt(last, 10);
                if (diff < DISABLE_MS) {
                    btn.disabled = true;
                    const mins = Math.floor((DISABLE_MS - diff) / 60000);
                    const secs = Math.floor(((DISABLE_MS - diff) % 60000) / 1000);
                    btn.textContent = `Wait ${mins}:${secs.toString().padStart(2, '0')} to resend`;
                    setTimeout(updateResendBtn, 1000);
                    return;
                }
            }
            btn.disabled = false;
            btn.textContent = 'Resend Verification Email';
        }

        form.addEventListener('submit', function(e) {
            localStorage.setItem(RESEND_KEY, Date.now().toString());
            btn.disabled = true;
            btn.textContent = 'Wait 5:00 to resend';
            setTimeout(updateResendBtn, 1000);
        });

        updateResendBtn();
        </script>

        <div class="logoutFrame">
            <form action="/logout-user" method="post" class="logoutForm">
                @csrf
                <button class="logoutButton">Logout</button>
            </form>
        </div>
        
        <div class="infoFrame">
            <p class="infoText">After verifying your email, you'll need to wait for admin confirmation before you can access the system.</p>
        </div>
    </div>


</body>
</html>