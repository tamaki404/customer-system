<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Required</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffde59 0%, #ffd54f 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        p {
            color: #666;
            margin: 0 0 30px 0;
            line-height: 1.5;
        }
        
        .login-button {
            background: #ffde59;
            color: #333;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(255, 222, 89, 0.3);
        }
        
        .login-button:hover {
            background: #ffd54f;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 222, 89, 0.4);
        }
        
        .register-link {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .register-link a {
            color: #ffde59;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="icon">🔐</div>
        <h1>Login Required</h1>
        <p>You need to be logged in to access this page. Please sign in to continue.</p>
        
        <a href="{{ route('login') }}" class="login-button">
            Sign In
        </a>
        
        <div class="register-link">
            Don't have an account? <a href="{{ url('/register-view') }}">Register here</a>
        </div>
    </div>
    
    <script>
        // Auto-redirect to login after 3 seconds
        setTimeout(function() {
            window.location.href = '{{ route("login") }}';
        }, 3000);
    </script>
</body>
</html> 