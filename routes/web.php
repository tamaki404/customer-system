<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\ReceiptController;

Route::post('/login-user', [UserController::class, 'login']);
Route::post('/logout-user', [UserController::class, 'logout']);


Route::get('/', function () {
    return view('login');
});



Route::post('/register-user', [UserController::class, 'register']);
Route::get('/register-view', function () {
    return view('registration');
});

Route::get('/check-username', function (\Illuminate\Http\Request $request) {
    $exists = DB::table('users')->where('username', $request->username)->exists();
    return response()->json(['available' => !$exists]);
});

// views
Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::get('/tickets', function () {
    return view('tickets');
})->name('tickets');
Route::get('/receipts', function () {
    return view('receipts');
})->name('receipts');


// call the func for showStaffs to display her ein vieww
Route::get('/staffs', [ViewController::class, 'showStaffs'])->name('staffs');

//add staffs and admin
Route::post('/add-staff', [UserController::class, 'addStaff']);

Route::post('/submit-ticket', [TicketController::class, 'submitTicket']);
Route::get('/specTicket/{ticketID}', [TicketController::class, 'specTicket'])->name('specTicket');

// tickets
// Route::get('/all-tickets', [TicketController::class, 'showAllTickets']);

Route::put('/tickets/update/{ticketID}', [TicketController::class, 'ticketsUpdate'])->name('tickets.update');
Route::get('/tickets', [TicketController::class, 'showTickets'])->name('tickets');

Route::post('/submit-receipt', [ReceiptController::class, 'submitReceipt'])->name('submit.receipt');
