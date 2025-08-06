
 @extends('layout')

@section('content')



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/view-order.css') }}">
    <title>View Order</title>
</head>
<body>

    <div class="viewFrame">

            <div class="titleCount"> 
                <h2 style="margin: 0">Your orders</h2> 
                <p style="margin: 0">These are the orders you've made</p>
            </div>    
    </div>

</body>
</html>


@endsection