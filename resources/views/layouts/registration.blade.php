<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunny&Scramble</title>
    @stack('styles')
</head>
<body>
    
    <div style="display: flex; flex-direction: column; align-items: center; background-color: #ffde59; padding: 20px;">
        <h1>Welcome to Sunny&Scramble</h1>
        <p>Your one-stop solution for customer management.</p>
        <div>
            @yield('registration-content')
        </div>
    </div>


    @stack('scripts')
</body>
</html>