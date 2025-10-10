<?php

use App\Http\Controllers\ProductSettingController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Models\Orders;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductSetting;
use App\Models\Suppliers;
use App\Models\Products;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CreditsController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductSalesController;
use App\Http\Controllers\GlobalCeilingController;
use App\Http\Controllers\DeliveryController;

Route::get('/registration/signin', function () {
    return view('registration.signin');
})->name('signin');


Route::post('/logout-user', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login'); 
})->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/account/registration', [RegistrationController::class, 'showSignupForm'])->name('registration.signup');
Route::post('/account/supplier/registration', [UserController::class, 'registerSupplier'])->name('registration.supplier.register');
Route::post('/account.signin', [UserController::class, 'signin'])->name('account.signin');
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');

// Email verification
Route::get('/email/verify/notice', function () {
    return view('verification.notice');
})->name('verification.notice');

Route::get('/email/verify', [UserController::class, 'verifyEmail'])->name('verification.verify');

// Login alias
Route::get('/login', function () {
    return redirect()->route('signin');
})->name('login');

// Authenticated dashboard
Route::get('/dashboard/view',  [DashboardController::class, 'dashboardView'])->middleware('auth')->name('dashboard.view');
Route::get('/dashboard/layout',  [DashboardController::class, 'layoutView'])->middleware('auth')->name('dashboard.layout');

Route::middleware(['auth', 'role:Supplier|Admin|Staff'])->group(function () {

    Route::get('/products/list',  [ProductController::class, 'productList'])->name('products.list');
    Route::get('/products/product/view/{product_id}',  [ProductController::class, 'productView'])->name('products.product');
    Route::get('/products/{product_id}/info', [ProductController::class, 'info'])->name('products.info');
    // Categories API
    Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
    Route::get('/categories/{parentId}/children', [CategoryController::class, 'children'])->name('categories.children');
    // Product hierarchy API
    Route::get('/products/tree', [CategoryController::class, 'productTree'])->name('products.tree');
    Route::get('/products/{parentProductId}/children', [CategoryController::class, 'productChildren'])->name('products.children');

    Route::get('/orders/list',  [OrderController::class, 'orderList'])->name('orders.list');
    Route::get('/receipts/list',  [ReceiptController::class, 'receiptList'])->name('receipts.list');


    Route::get('/credits/list',  [CreditsController::class, 'creditsList'])->name('credits.list');
    // Route::get('/credits/list/view/{credit_id}',  [CreditsController::class, 'creditsView'])->name('credits.view');
    Route::get('/receipts/list/receipt/{receipt_id}',  [ReceiptController::class, 'receiptView'])->name('receipts.receipt');


    // supplier only
    Route::post('/orders/order/create',  [OrderController::class, 'createOrder'])->name('order.create');
    Route::get('/purchase-orders/list',  [PurchaseOrderController::class, 'purchaseOrderList'])->name('purchaseorders.list');
    Route::post('/purchase-orders/create',  [PurchaseOrderController::class, 'createPurchaseOrder'])->name('purchaseorders.create');
    Route::post('/receipts/create',  [ReceiptController::class, 'receiptUpload'])->name('receipt.create');
    Route::get('/purchase-orders/list/view/{po_id}',  [PurchaseOrderController::class, 'purchaseOrderView'])->name('purchaseorders.purchaseorder');
    Route::get('/orders/list/view/{order_id}',  [OrderController::class, 'orderView'])->name('orders.order');

    Route::get('/profile/view', [ProfileController::class, 'profileView'])->name('profile.view');
    Route::post('/delivery/confirm', [DeliveryController::class, 'confirmDelivery'])->name('delivery.confirm');


    
    Route::get('/order/deliveries/{delivery_id}',  [DeliveryController::class, 'deliveryView'])->name('order.delivery_items');

});

Route::middleware(['auth', 'role:Admin|Staff'])->group(function () {
    Route::get('/customers/list',  [CustomersController::class, 'customersList'])->name('customers.list');
    Route::get('/customers/list/customer/{supplier_id}',  [CustomersController::class, 'customerView'])->name('customers.customer');

    Route::get('/staffs/list',  [StaffsController::class, 'staffsList'])->name('staffs.list');
    Route::get('/staffs/list/staff/{staff_id}',  [StaffsController::class, 'staffView'])->name('staffs.staff');
    Route::post('/supplier/confirm', [CustomersController::class, 'supplierConfirm'])->name('supplier.confirm');

    Route::get('/logs/list',  [LogsController::class, 'logsList'])->name('logs.list');

    Route::post('/products/add', [ProductController::class, 'addProduct'])->name('product.add');
    // Category management
    Route::get('/categories/manage', [CategoryController::class, 'manage'])->name('categories.manage');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
    Route::post('/products/setting/modify', [ProductSettingController::class, 'modifyProduct'])->name('productset.modify');

    Route::get('/deliveries/list',  [DeliveryController::class, 'deliveryList'])->name('deliveries.list');

    //product sale
    Route::post('/products/setting/sale', [ProductSalesController::class, 'addSale'])->name('productset.sale');

    //set ceiling
    Route::post('/products/set/global-ceiling', [GlobalCeilingController::class, 'setGlobalCeiling'])->name('set.global_ceiling');


    Route::post('/purchase-orders/purchase/view/place', [OrderController::class, 'placeOrderItems'])->name('purchaseorders.place');
    Route::post('/purchase-orders/{po_id}/confirm', [PurchaseOrderController::class, 'confirmPurchaseOrder'])->name('purchaseorders.confirm');

    Route::post('/receipts/action/{receipt_id}', [ReceiptController::class, 'receiptAction'])->name('receipts.action');
    Route::post('/order/action', [OrderController::class, 'orderAction'])->name('order.action');


    //delivery
    Route::post('/order/action/delivery/process', [DeliveryController::class, 'orderProcess'])->name('order.process');


    // order pdf views
    Route::get('/orders/{order_id}/customer-order', [OrderController::class, 'customerOrderPdf'])->name('orders.customer.pdf');
    Route::get('/orders/{order_id}/delivery-receipt', [OrderController::class, 'deliveryReceiptPdf'])->name('orders.delivery.pdf');
    Route::get('/orders/{order_id}/sales-invoice', [OrderController::class, 'salesInvoicePdf'])->name('orders.invoice.pdf');
    
    // purchase order pdf view
    Route::get('/purchase-orders/{po_id}/pdf', [PurchaseOrderController::class, 'purchaseOrderPdf'])->name('purchaseorders.pdf');

    Route::post('/products/add-sub/{product_id}', [ProductController::class, 'addSub'])->name(name: 'add.subproduct');

    // Product parent update
    Route::post('/products/update-parent', [ProductController::class, 'updateParent'])->name('products.updateParent');



});


Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::post('/account/staff/registration', [UserController::class, 'registerStaff'])->name('registration.staff.register');
    Route::post('/staffs/modify', [StaffsController::class, 'modifyStaff'])->name('staff.modify');

});

