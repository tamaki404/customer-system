
<div style="max-width:500px;margin:80px auto;padding:40px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;text-align:center;">
    <h2 style="color:#1976d2;font-size:2rem;font-weight:700;margin-bottom:24px;">Account Status: <span style="color:#d32f2f;">{{ auth()->user()->acc_status }}</span></h2>
    <p style="font-size:1.2rem;color:#333;margin-bottom:18px;">
        @if(auth()->user()->acc_status === 'pending')
            Your account is pending approval. Please wait for an admin to accept your registration.<br>
            You will be notified once your account is activated.
        @elseif(auth()->user()->acc_status === 'Suspended')
            Your account has been suspended. Please contact support or wait for further instructions.
        @endif
    </p>
    <a href="/logout-user" style="background:#1976d2;color:#fff;padding:10px 28px;border:none;border-radius:6px;font-weight:600;cursor:pointer;text-decoration:none;">Logout</a>
</div>
