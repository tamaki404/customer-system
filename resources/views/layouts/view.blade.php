<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunny & Scramble</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/layout/search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/date-range.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/pagination.css') }}">

    <link rel="stylesheet" href="{{ asset('css/layout/view.css') }}">

     @stack('styles')

</head>
<body>
    @auth

        <div class="mainFrame">
            <div>

            </div>

            <div class="showScreen" id="showScreen">
                @yield('content')

            </div>
            
        </div>

    @else
        <script>window.location.href = '{{ route("login") }}';</script>
    @endauth
    
    @stack('scripts')

</body>
</html>