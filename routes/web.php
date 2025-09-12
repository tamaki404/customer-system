<?php

use App\Http\Controllers\OrderReceiptController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TicketController;
use App\Models\Orders;
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
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PurchaseOrderItemController;
// ================================
// PUBLIC ROUTES (No Authentication Required)
// ================================
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

// ================================
// AUTHENTICATION ROUTES (No Auth Required)
// ================================
Route::post('/login-user', [UserController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register-user', [UserController::class, 'register']);
Route::get('/check-username', [UserController::class, 'checkUsername'])->middleware('throttle:30,1');

// Email verification routes
Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail'])->name('verify.email');

// Password reset routes
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// ================================
// AUTHENTICATED ROUTES (Basic Auth Required)
// ================================
Route::middleware(['auth'])->group(function () {
    
    // Logout routes
    Route::post('/logout-user', [UserController::class, 'logout']);
    Route::get('/logout', [UserController::class, 'logout']);
    
    // Email verification routes (authenticated)
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/resend-verification', [UserController::class, 'resendVerification'])
        ->name('verification.resend')
        ->middleware('throttle:3,1');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// ================================
// AUTHENTICATED & ACTIVE USERS (Auth + Status Check)
// ================================
Route::middleware(['auth', 'check.status'])->group(function () {
    
    // ================================
    // GENERAL USER ROUTES (All authenticated users)
    // ================================
    
    // Dashboard
    Route::get('/dashboard', [ViewController::class, 'showDashboard'])->name('dashboard');
    Route::get('/dashboard', [ViewController::class, 'dashboardData'])->name('dashboard');

    // Profile management
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    Route::post('/edit-profile', [UserController::class, 'updateProfile']);
    Route::post('/update-image', [UserController::class, 'updateImage']);

    // Address lookups (All users)
    Route::get('/regions/{region}/provinces', [AddressController::class, 'provinces'])->name('address.provinces');
    Route::get('/provinces/{province}/municipalities', [AddressController::class, 'municipalities'])->name('address.municipalities');
    Route::get('/municipalities/{municipality}/barangays', [AddressController::class, 'barangays'])->name('address.barangays');
    

    Route::get('/purchase_orders/receipts/{po_id}', [PurchaseOrderController::class, 'purchaseReceipts']);


    // ================================
    // CUSTOMER SPECIFIC ROUTES
    // ================================
    
    // Store and Orders (Customer functionality)
    Route::get('/store', [OrderController::class, 'store'])->name('store');
    Route::get('/customer_orders', [OrderController::class, 'customerOrders'])->name('customer_orders');
    Route::get('/view-order/{id}', [OrderController::class, 'viewOrder'])->name('orders.view');
    Route::get('/order/view/{id}', [OrderController::class, 'orderView'])->name('order.view');
    Route::post('/order/cancel/{order_id}', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/customer/purchase-order/cancel', [PurchaseOrderController::class, 'cancelPOStatus'])
        ->name('customer.po_status');
    // Product viewing
    Route::get('/product-image/{id}', [ProductController::class, 'showImage'])->name('product.image');

    Route::get('/product/{id}', [ProductController::class, 'productView'])->name('product_view.view');

    // Purchase Orders (Customer functionality)
    Route::get('/purchase_order', [PurchaseOrderController::class, 'purchaseOrder'])->name('purchase_order');
    Route::post('/purchase-order/store', [PurchaseOrderController::class, 'store'])->name('purchase_orders.store');
    Route::get('/purchase-order/create/purchase-orderForm/{po_id}', [PurchaseOrderController::class, 'purchaseOrderForm'])->name('purchase_order.create');
    Route::get('/purchase-order/store/order', [PurchaseOrderController::class, 'storeOrderView'])->name('purchase_order.store.order');
    Route::get('/purchase_order/view/{po_id}', [PurchaseOrderController::class, 'purchaseOrderView'])->name('purchase_order.view');
    Route::get('/purchase-order/{po_id}/pdf', [PurchaseOrderController::class, 'downloadPDF'])->name('purchase_order.pdf');
    Route::get('/product-search', [PurchaseOrderController::class, 'productSearch'])->name('product-search');
    
    // Receipts (Customer functionality)
    Route::get('/receipts', [ReceiptController::class, 'showUserReceipts'])->name('receipts');
    Route::get('/receipts_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipts.view');
    Route::get('/receipt_view/{receipt_id}', [ReceiptController::class, 'viewReceipt'])->name('receipt_view');
    Route::get('/receipt_image/{receipt_id}', [ReceiptController::class, 'getReceiptImage'])->name('receipt.image');
    Route::post('/receipt/cancel/{receipt_id}', [ReceiptController::class, 'cancelReceipt'])->name('receipt.cancel');
    Route::post('/submit-receipt', [ReceiptController::class, 'submitReceipt'])->name('submit.receipt');
    Route::get('/date-search', [ReceiptController::class, 'dateSearch'])->name('date.search');
    Route::get('/check-po-number', [ReceiptController::class, 'checkPONumber']);

    // Customer Reports (Customer functionality)
    Route::get('/reports/customers', [ReportsController::class, 'customerReports'])->name('customer_reports');
    Route::get('/reports/customers/dateFilter', [ReportsController::class, 'customerDateFilter'])->name('customer_dateFilter');


    Route::get('/purchase_order/order/receive', [OrderReceiptController::class, 'receivedOrder'])->name('customer.received');
    Route::get('/purchase_order/order/report', [OrderReceiptController::class, 'receivedReportOrder'])->name('customer.received-report');

    Route::get('/purchase_order/order/receipts/report/view/{or_id}', action: [OrderReceiptController::class, 'orderReceipt'])->name('report.view');

    // ================================
    // ADMIN & STAFF ROUTES
    // ================================
    Route::middleware(['check.role:Admin,Staff'])->group(function () {
        

        
        // Order Management
        Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
        Route::post('/order/accept/{order_id}', [OrderController::class, 'acceptOrder'])->name('orders.accept');
        Route::post('/order/mark-done/{order_id}', [OrderController::class, 'markOrderDone'])->name('orders.mark.done');
        Route::post('/order/reject/{order_id}', [OrderController::class, 'rejectOrder'])->name('orders.reject');
        
        // Receipt Management
        Route::post('/receipts/verify/{receipt_id}', [ReceiptController::class, 'verifyReceipt'])->name('receipts.verify');
        Route::post('/receipts/cancel/{receipt_id}', [ReceiptController::class, 'cancelReceipt'])->name('receipts.cancel');
        Route::post('/receipts/reject/{receipt_id}', [ReceiptController::class, 'rejectReceipt'])->name('receipts.reject');

        Route::post('/receipts/receipt_status/{po_id}', [ReceiptController::class, 'fileReceipt'])->name('receipts.receipt_status');

        
        // Customer Management
        Route::get('/customers', [ViewController::class, 'showCustomers'])->name('customers');
        Route::get('/customer_view/{customer_id}', [ViewController::class, 'viewCustomer'])->name('customer.view');

        //Purchase order management
        Route::post('/product_order/orders/change_status', [PurchaseOrderController::class, 'changeStatus'])
            ->name('change.po_status');
        Route::post('/product_order/orders/quantity', [PurchaseOrderItemController::class, 'changeQuantity'])
            ->name('change.quantity');
        //po invoice
        Route::get('/purchase-order/invoice/display/{po_id}', [PurchaseOrderController::class, 'invoiceView'])->name(name: 'invoice.view');




        
        // Reports (Admin/Staff)
        Route::get('/reports', [ReportsController::class, 'reports'])->name('reports');
        Route::get('/reports/dateFilter', [ReportsController::class, 'dateFilter'])->name('dateFilter');
        Route::get('/reports/export', [ReportsController::class, 'exportReports'])->name('reports.export');
        Route::get('/reports/export/customers', [ReportsController::class, 'exportCustomers'])->name('reports.customers');
        Route::get('/reports/customer-analytics', [ReportsController::class, 'customerAnalytics'])->name('customer.analytics');
        Route::get('/reports/products', [ReportsController::class, 'exportProducts'])->name('reports.products');
        Route::get('/reports/orders', [ReportsController::class, 'exportOrders'])->name('reports.orders');
        Route::get('/reports/receipts', [ReportsController::class, 'exportReceipts'])->name('reports.receipts');
        Route::get('/reports/purchase-orders/preview', [ReportsController::class, 'purchaseOrdersPreview'])->name('reports.purchase_orders.preview');
    });
    
    // ================================
    // ADMIN ONLY ROUTES
    // ================================
    Route::middleware(['check.role:Admin'])->group(function () {
        
        // Staff management
        Route::get('/staffs', [ViewController::class, 'showStaffs'])->name('staffs');
        Route::post('/add-staff', [UserController::class, 'addStaff']);
        Route::get('/staff_view/{staff_id}', [ViewController::class, 'viewStaff'])->name('staff.view');
        Route::post('/staff/update-profile/{id}', [ViewController::class, 'updateStaffProfile'])->name('staff.update.profile');
        Route::post('/staff/change-password/{id}', [ViewController::class, 'changeStaffPassword'])->name('staff.change.password');
        Route::post('/staff/upload-image/{id}', [ViewController::class, 'uploadStaffImage'])->name('staff.upload.image');
        Route::post('/staff/update-status/{id}', [ViewController::class, 'updateStaffStatus'])->name('staff.update.status');
        Route::post('/staff/deactivate/{id}', [ViewController::class, 'deactivateStaff'])->name('staff.deactivate');
        Route::delete('/staff/delete/{id}', [ViewController::class, 'deleteStaff'])->name('staff.delete');

        // Product management
        Route::post('/add-product', [ProductController::class, 'store']);
        Route::post('/product/unlist/{product_id}', [ProductController::class, 'unlistProduct'])->name('products.unlist');
        Route::post('/products/{product_id}/addStock', [ProductController::class, 'addStock'])->name('products.addStock');
        Route::post('/products/{product_id}/editProduct', [ProductController::class, 'editProduct'])->name('products.editProduct');
        Route::post('/product/list/{product_id}', [ProductController::class, 'listProduct'])->name('products.list');
        Route::delete('/product/delete/{product_id}', [ProductController::class, 'deleteProduct'])->name('products.deleteProduct');
        
        //Customer management
        Route::post('/customer/accept/{id}', [ViewController::class, 'acceptCustomer'])->name('customer.accept');
        Route::post('/customer/activate/{id}', [ViewController::class, 'activateCustomer'])->name('customer.activate');
        Route::post('/customer/suspend/{id}', [ViewController::class, 'suspendCustomer'])->name('customer.suspend');
        
    });
});