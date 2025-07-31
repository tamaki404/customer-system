@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            background: linear-gradient(135deg, #ffde59 0%, #ffd54f 100%);
            color: #333;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .error-message {
            font-size: 24px;
            margin: 20px 0;
            font-weight: 600;
        }
        
        .error-description {
            font-size: 16px;
            margin: 10px 0 30px 0;
            max-width: 500px;
            line-height: 1.5;
        }
        
        .back-button {
            background: #333;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .back-button:hover {
            background: #555;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🚫</div>
        <h1 class="error-code">403</h1>
        <h2 class="error-message">Access Denied</h2>
        <p class="error-description">
            Sorry, you don't have permission to access this page. 
            Please contact your administrator if you believe this is an error.
        </p>
        <a href="{{ route('dashboard') }}" class="back-button">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
@endsection 