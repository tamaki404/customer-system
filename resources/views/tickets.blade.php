@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <title>Tickets</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>

    <div class="tickFrame" style="width: 80%; height: 80%; overflow-y: scroll; display: flex; flex-direction: column;">
        @if(auth()->user()->user_type === 'Customer')

                    <h2>Create a New Ticket</h2>
                    <form action="/submit-ticket" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                        <input type="text" name="title" placeholder="Ticket Title" required>
                        <input type="text" name="body" placeholder="Ticket Body" required>
                        <input type="date" name="startDate" required>
                        <input type="date" name="endDate" required>
                        <input type="file" name="image" accept="image/*" required>
                    <button type="submit">Submit</button>
                </form>
        <h2>My Tickets</h2>
            @foreach ($tickets as $ticket)
                @if($ticket->id == auth()->user()->id)
 
                <a href="{{ route('specTicket', $ticket->ticketID) }}" style="border: 1px solid black">
                    <span><h3>{{ $ticket->title }}</h3> - <p>{{ $ticket->status }}</p> </span>
                    <p>{{ $ticket->body }}</p>
                    <p>Start: {{ $ticket->startDate }}</p>
                    <p>End: {{ $ticket->endDate }}</p>
                </a>  
                @endif
            
            @endforeach
        @elseif(auth()->user()->user_type === 'Staff' || auth()->user()->user_type === 'Admin')
            @foreach ($tickets as $ticket)
                <a href="{{ route('specTicket', $ticket->ticketID) }}" style="border: 1px solid black">
                    <span style="width: 100%; display: flex; flex-direction: row;"><h3>{{ $ticket->title }}</h3> - <p>{{ $ticket->status }}</p> </span>
                    <p>{{ $ticket->body }}</p>
                    <p>Start: {{ $ticket->startDate }}</p>
                    <p>End: {{ $ticket->endDate }}</p>
                </a>
            @endforeach
        @endif

    </div>



</body>
</html>


@endsection
