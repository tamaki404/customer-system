

<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\EmailVerificationController;


Route::post('/login-user', [UserController::class, 'login']);
Route::post('/logout-user', [UserController::class, 'logout']);
Route::get('/logout', [UserController::class, 'logout']);

Route::get('/success-signup', function () {
    return view('verified');
});
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::get('/', function () {
    return view('login');
});

Route::get('/receipts_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipts.view');


Route::post('/register-user', [UserController::class, 'register']);
Route::get('/register-view', function () {
    return view('registration');
});

Route::get('/check-username', [UserController::class, 'checkUsername']);

// views
// Route::get('/dashboard', [ViewController::class, 'dashboard'])->name('dashboard');
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::get('/tickets', function () {
    return view('tickets');
})->name('tickets');
Route::get('/receipts', [ReceiptController::class, 'showUserReceipts'])->name('receipts');


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
Route::get('/submit-receipt', [ReceiptController::class, 'submitReceipt'])->name('submit.receipt');

Route::get('/customers', action: [ViewController::class, 'showCustomers'])->name('customers');
Route::get('/customer_view/{customer_id}', action: [ViewController::class, 'viewCustomer'])->name('customer.view');
Route::get('/receipt_image/{receipt_id}', [ReceiptController::class, 'getReceiptImage'])->name('receipt.image');

Route::get('/date-search', [ReceiptController::class, 'dateSearch'])->name('date.search');

// Receipt verification and cancellation
Route::post('/receipts/verify/{receipt_id}', [ReceiptController::class, 'verifyReceipt'])->name('receipts.verify');
Route::post('/receipts/cancel/{receipt_id}', [ReceiptController::class, 'cancelReceipt'])->name('receipts.cancel');

// Customer accept/suspend actions
Route::post('/customer/accept/{customer_id}', [ViewController::class, 'acceptCustomer'])->name('customer.accept');
Route::post('/customer/suspend/{customer_id}', [ViewController::class, 'suspendCustomer'])->name('customer.suspend');



// Dashboard (only one route, with correct middleware)
Route::get('/dashboard', [ViewController::class, 'showDashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/receipt_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipt_view');




// Email verification routes
Route::get('/email/verify', [EmailVerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail'])->name('verify.email');
Route::post('/resend-verification', [UserController::class, 'resendVerification'])->name('verification.resend')->middleware('throttle:3,1');

// Laravel's built-in resend verification route
Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

