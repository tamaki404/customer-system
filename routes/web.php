



<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Http\Controllers\OrderController;
use App\Models\Order;

// Public routes (no authentication required)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', function () {
    return view('login');
});

Route::get('/register-view', function () {
    return view('registration');
});

Route::get('/success-signup', function () {
    return view('verified');
});

Route::get('/verify-email-pending', function () {
    return view('auth.verify-email-pending');
})->name('verification.pending');

// Authentication routes
Route::post('/login-user', [UserController::class, 'login']);
Route::post('/logout-user', [UserController::class, 'logout'])->middleware('auth');
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth');

Route::post('/register-user', [UserController::class, 'register']);
Route::get('/check-username', [UserController::class, 'checkUsername']);

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
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Password reset routes
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


// Product add route (Admin only, but form already checks user_type)
Route::post('/add-product', [ProductController::class, 'store'])->middleware(['auth', 'check.status']);

// Protected routes (require authentication and active status)
Route::middleware(['auth', 'check.status'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [ViewController::class, 'showDashboard'])->name('dashboard');
    
    // Profile management
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    
    Route::post('/edit-profile', [UserController::class, 'updateProfile']);
    
    // Receipts management
    Route::get('/receipts', [ReceiptController::class, 'showUserReceipts'])->name('receipts');
    Route::get('/receipts_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipts.view');
    Route::get('/receipt_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipt_view');
    Route::get('/receipt_image/{receipt_id}', [ReceiptController::class, 'getReceiptImage'])->name('receipt.image');
    
    Route::post('/submit-receipt', [ReceiptController::class, 'submitReceipt'])->name('submit.receipt');
    Route::get('/submit-receipt', [ReceiptController::class, 'submitReceipt'])->name('submit.receipt');
    
    Route::get('/date-search', [ReceiptController::class, 'dateSearch'])->name('date.search');
    
    // Receipt verification and cancellation (Admin/Staff only)
    Route::middleware(['check.role:Admin,Staff'])->group(function () {
        Route::post('/receipts/verify/{receipt_id}', [ReceiptController::class, 'verifyReceipt'])->name('receipts.verify');
        Route::post('/receipts/cancel/{receipt_id}', [ReceiptController::class, 'cancelReceipt'])->name('receipts.cancel');
        Route::post('/receipts/reject/{receipt_id}', [ReceiptController::class, 'rejectReceipt'])->name('receipts.reject');
        Route::post('/product/unlist/{product_id}', [ProductController::class, 'unlistProduct'])->name('products.unlist');
        Route::post('/product/list/{product_id}', [ProductController::class, 'listProduct'])->name('products.list');
        Route::delete('/product/delete/{product_id}', [ProductController::class, 'deleteProduct'])->name('products.delete');

    });
    
    // Tickets management
    Route::get('/tickets', [TicketController::class, 'showTickets'])->name('tickets');
    Route::post('/submit-ticket', [TicketController::class, 'submitTicket']);
    Route::get('/specTicket/{ticketID}', [TicketController::class, 'specTicket'])->name('specTicket');
    Route::put('/tickets/update/{ticketID}', [TicketController::class, 'ticketsUpdate'])->name('tickets.update');
    
    // Staff management (Admin only)
    Route::middleware(['check.role:Admin'])->group(function () {
        Route::get('/staffs', [ViewController::class, 'showStaffs'])->name('staffs');
        Route::post('/add-staff', [UserController::class, 'addStaff']);
        
        // Staff Management Routes
        Route::get('/staff_view/{staff_id}', [ViewController::class, 'viewStaff'])->name('staff.view');
        Route::post('/staff/update-profile/{id}', [ViewController::class, 'updateStaffProfile'])->name('staff.update.profile');
        Route::post('/staff/change-password/{id}', [ViewController::class, 'changeStaffPassword'])->name('staff.change.password');
        Route::post('/staff/upload-image/{id}', [ViewController::class, 'uploadStaffImage'])->name('staff.upload.image');
        Route::post('/staff/update-status/{id}', [ViewController::class, 'updateStaffStatus'])->name('staff.update.status');
        Route::post('/staff/deactivate/{id}', [ViewController::class, 'deactivateStaff'])->name('staff.deactivate');
        Route::delete('/staff/delete/{id}', [ViewController::class, 'deleteStaff'])->name('staff.delete');
    });
    
    // Customer management (Admin/Staff only)
    Route::middleware(['check.role:Admin,Staff'])->group(function () {
        Route::get('/customers', [ViewController::class, 'showCustomers'])->name('customers');
        Route::get('/customer_view/{customer_id}', [ViewController::class, 'viewCustomer'])->name('customer.view');
        
        // Customer accept/suspend actions
        Route::post('/customer/accept/{id}', [ViewController::class, 'acceptCustomer'])->name('customer.accept');
        Route::post('/customer/activate/{id}', [ViewController::class, 'activateCustomer'])->name('customer.activate');
        Route::post('/customer/suspend/{id}', [ViewController::class, 'suspendCustomer'])->name('customer.suspend');
    });


    Route::get('/ordering', [ViewController::class, 'ordering'])->name('ordering');
    Route::get('/reports', [ViewController::class, 'reports'])->name('reports');

    Route::get('/product-image/{id}', [ProductController::class, 'showImage'])->name('product.image');
    // Product detail view
    Route::get('/product/{id}', function($id) {
        $product = Product::findOrFail($id);
        return view('product_view', compact('product'));
    })->middleware(['auth', 'check.status'])->name('product_view.view');

    // Route::middleware(['auth', 'check.status'])->post('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::post('/checkout', [OrderController::class, 'checkout']);

});


    // Customer routes
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
    
    Route::get('orders', [ViewController::class, 'allOrders'])->name('all-orders');   
    Route::get('spec-orders/{id}', [ViewController::class, 'specOrders'])->name('spec-orders');