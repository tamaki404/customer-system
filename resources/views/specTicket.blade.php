@extends('layout')

@section('content')
    <style>
        .ticketHead{
            display: flex; flex-direction: row; justify-content: space-between; width:100%
        }
    </style>
    <div class="ticket" style="width: 50%; padding: 20px; display: flex; flex-direction: column;">


        <form action="{{ route('tickets.update', ['ticketID' => $ticket->ticketID]) }}" method="POST">
             @csrf
            @method('PUT')
            <input type="hidden" name="ticketID" value="{{ $ticket->ticketID }}">
            <input type="hidden" name="received_by" value="{{ auth()->user()->id }}">
            <input type="hidden" name="status" value="changed">
            <button type="submit">Received</button>
        </form>

        <a href="/tickets"><- Tickets</a>
        <span class="ticketHead"> <h1>Ticket Details</h1> <p>{{ $ticket->created_at }}</p> </span>
        <span>Title:{{ $ticket->title }} {{ $ticket->status }}</span>
        <p>{{ $ticket->body }}</p>
        <p>Start Date: {{ $ticket->startDate }}</p>
        <p>End Date: {{ $ticket->endDate }}</p>
        <img src="{{ asset('ticketsImg/' . $ticket->image) }}" alt="Ticket Image" style="max-width: 100%;">
    </div>
@endsection