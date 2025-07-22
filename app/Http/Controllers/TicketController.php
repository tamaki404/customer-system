<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tickets;

use Illuminate\Support\Facades\Hash;
class TicketController extends Controller
{
public function submitTicket(Request $request)
{
    $validated = $request->validate([

    
        'title' => 'required|string|max:100',
        'body' => 'required|string|max:2000',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'startDate' => 'required|date|before_or_equal:endDate',
        'endDate' => 'required|date|after_or_equal:startDate',
        'id' => 'nullable|integer|exists:users,id', 
    

    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('ticketsImg'), $imageName);
        $validated['image'] = $imageName;
    } else {
        $validated['image'] = null;
    }

    // Create the user
    Tickets::create([
        'title' => $validated['title'],
        'body' => $validated['body'],
        'image' => $validated['image'],
        'startDate' => $validated['startDate'],
        'endDate' => $validated['endDate'],
        'id' => $validated['id'],
    ]);

    // Login the user

    return redirect('/tickets');
}


// public function showTickets()
// {
//     $userId = auth()->id(); 

//     $tickets = Tickets::where('id', $userId)->get();

//     return view('tickets', compact('tickets'));
// }
public function showTickets()
{
    $tickets = Tickets::all(); 
    return view('tickets', compact('tickets'));
}



public function specTicket($ticketID)
{
    $ticket = Tickets::where('ticketID', $ticketID)->firstOrFail();
    return view('specTicket', compact('ticket'));
}

public function ticketsUpdate(Request $request, $ticketID)
{
    $ticket = Tickets::where('ticketID', $ticketID)->firstOrFail();

    \Log::info("Updating ticket", ['id' => $ticket->ticketID, 'user' => auth()->user()->id]);

    $ticket->resolved_at = $request->input('resolved_at');
    $ticket->status = $request->input('status');
    $ticket->received_by = $request->input('received_by');
    $ticket->save();

    return redirect()->back()->with('success', 'Ticket updated successfully');
}

}
?>