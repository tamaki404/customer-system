@extends('layout')

@section('content')
<div style="max-width:700px;margin:40px auto;padding:30px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;text-align:center;">
    <h2>Receipt Image</h2>
    @if($receipt && $receipt->receipt_image)
        <img src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image" style="max-width:100%;max-height:500px;display:block;margin:0 auto 24px;">
    @else
        <p>Receipt image not found.</p>
    @endif
    <a href="{{ url()->previous() }}" class="btn" style="display:inline-block;margin-top:24px;padding:10px 24px;background:#f59c00;color:#fff;border-radius:5px;text-decoration:none;">&larr; Return</a>
</div>
@endsection
