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
            flex-direction: column;
            align-content: center;
            justify-content: center;
            background-color: #fff;
            padding: 30px;
            gap: 10px;
            width: 97%;
            height: 97%;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
            border-radius: 15px;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            flex-direction: row
        }
        
        .error-message {
            font-size: 35px;
            font-weight: bold
            margin: 20px 0;
            font-weight: 600;
            text-align: center
        }
        
        .error-description {
            font-size: 16px;
            margin: 10px 0 30px 0;
            width: 50%;
            text-align: center;
            align-self: center;;
            justify-self: center
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
            align-self: center;;
            justify-self: center
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
        span {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
        }

        span h2{
            font-size: 100px;
        }

        span h1{
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            flex-direction: row;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <span><h2>🚫</h2><h1>403</h1></span>
        <h2 class="error-message">Access Denied</h2>
        <p class="error-description">
            Sorry, you don't have permission to access this page. 
            Please contact your administrator if you believe this is an error.
        </p>
        {{-- <a href="{{ route('dashboard') }}" class="back-button">
            Back to Dashboard
        </a> --}}
    </div>
</body>
</html>
@endsection 