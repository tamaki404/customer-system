@extends('layout')

@section('content')


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipt_image.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Document</title>
</head>
<body>
    <div class="receiptFrame">
            <a href="{{ url()->previous() }}" class="returnBtn">&larr; Return</a>

    @if($receipt && $receipt->receipt_image)
        <img src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image" style="max-width:100%;max-height:500px;display:block;margin:0 auto 24px;">
        <br>
        <a href="{{ asset('images/' . $receipt->receipt_image) }}" download class="downloadBtn"><i class="fas fa-download"></i> Download Image</a>
    @else
        <p>Receipt image not found.</p>
    @endif
</div>
</body>
</html>

@endsection
