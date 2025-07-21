@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
    <title>Tickets</title>
</head>
<body>


@if(auth()->user()->user_type === 'customer')
<div class="ticketsBody">
    <h3>Tickets you've sent</h3>
    <div style="border:2px solid black">
        @foreach ($tickets as $ticket)
    <div>
        <h3>{{ $ticket->title }}</h3>
        <p>{{ $ticket->body }}</p>
        <p>Start: {{ $ticket->startDate }}</p>
        <p>End: {{ $ticket->endDate }}</p>
    </div>
       @endforeach

    </div>



    <h3>Submit a Ticket</h3>
    <p>Make your message detailed so the staff can understand it</p>

     <div class="sentTickets">

     </div>

     <form class="ticketForm" action="/submit-ticket" method="POST" enctype="multipart/form-data">

        @csrf
        <input type="text" name="title" placeholder="title">
        <input type="text" name="body" placeholder="Body">
        <input type="file" name="ticketImg" accept="image/*" required>
        <input type="text" name="id" value="{{ auth()->user()->id }}" hidden>
        <input type="date" name="startDate">
        <input type="date" name="endDate">

        <button  type="submit">Send Ticket</button>
     </form>

</div>

@elseif(auth()->user()->user_type === 'staff')
<div class="ticketsBody">
    <h3>Tickets</h3>
    <div style="border:2px solid black">
        @foreach ($tickets as $ticket)
    <div>
        <h3>{{ $ticket->title }}</h3>
        <p>{{ $ticket->body }}</p>
        <p>Start: {{ $ticket->startDate }}</p>
        <p>End: {{ $ticket->endDate }}</p>
    </div>
       @endforeach

    </div>



    <h3>Submit a Ticket</h3>
    <p>Make your message detailed so the staff can understand it</p>

     <div class="sentTickets">

     </div>

     <form class="ticketForm" action="/submit-ticket" method="POST" enctype="multipart/form-data">

        @csrf
        <input type="text" name="title" placeholder="title">
        <input type="text" name="body" placeholder="Body">
        <input type="file" name="ticketImg" accept="image/*" required>
        <input type="text" name="id" value="{{ auth()->user()->id }}" hidden>
        <input type="date" name="startDate">
        <input type="date" name="endDate">

        <button  type="submit">Send Ticket</button>
     </form>

</div>

@endif

</body>
</html>


 
@endsection
