<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    DashboardController,
    RegistrationController,
    UserController,
    ProductController,
    CategoryController,
    OrderController,
    PurchaseOrderController,
    ReceiptController,
    CreditsController,
    ProfileController,
    GroupsController,
    CustomersController,
    StaffsController,
    LogsController,
    ProductSettingController,
    ProductSalesController,
    GlobalCeilingController,
    DeliveryController,
    SaleDiscountController,
    ErrorController,
    PurchaseRequestController,
    CreditsRequestController,
    DeliveryRequestController,
    PaymentsController,
    PromosController,
    CabraController,
    ReturnsController,

};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::get('/registration/signin', fn () => view('registration.signin'))->name('signin');
Route::get('/login', fn () => redirect()->route('signin'))->name('login');

Route::post('/logout-user', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Registration & Account Selection
|--------------------------------------------------------------------------
*/
Route::get('/choose-account', [RegistrationController::class, 'chooseAccount'])
    ->middleware('auth')
    ->name('choose.accounts');

Route::post('/account/signin-representative', [UserController::class, 'signinRepresentative'])
    ->name('account.signin-representative');

Route::post('/account/customer/registration', [UserController::class, 'registerCustomer'])
    ->name('registration.customer.register');

Route::get('/account/customer/registration', [UserController::class, 'showSignupForm'])
    ->name('registration.signup');

Route::post('/account.signin', [UserController::class, 'signin'])->name('account.signin');
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/notice', fn () => view('verification.notice'))->name('verification.notice');
Route::get('/email/verify', [UserController::class, 'verifyEmail'])->name('verification.verify');


/*
|--------------------------------------------------------------------------
| Customer & Shared Routes (Customer, Admin, Staff)
|--------------------------------------------------------------------------
*/

    Route::get('/error/declined-request', [ErrorController::class, 'declined'])
        ->name('error.declined');
    Route::post('/declined/update', [ErrorController::class, 'updateDeclined'])->name('declined.update');    
    Route::post('/declined/update', [ErrorController::class, 'updateDeclined'])->name('declined.bank.update');    
    Route::post('/declined/update', [ErrorController::class, 'updateDeclined'])->name('declined.docx.update');    
    Route::post('/declined/update', [ErrorController::class, 'updateDeclined'])->name('declined.bank.update');    

    Route::get('/submitted/success', [ErrorController::class, 'success'])->name('error.success');    


Route::get('/locked', function () {
    return view('lock.locked'); 
})->name('locked.page');

Route::middleware(['auth', 'role:Customer', 'check.customer'])->group(function () {
    Route::get('/products/list', [ProductController::class, 'productList'])
        ->middleware('check.rep.permission:Products')
        ->name('products.list');
    Route::get('/products/product/view/{product_id}', [ProductController::class, 'productView'])->name('products.product');
    Route::get('/products/{product_id}/info', [ProductController::class, 'info'])->name('products.info');
    Route::get('/orders/list', [OrderController::class, 'orderList'])
        ->middleware('check.rep.permission:Orders')
        ->name('orders.list');

    Route::get('/receipts/list', [ReceiptController::class, 'receiptList'])
        ->middleware('check.rep.permission:POP')
        ->name('receipts.list');

    Route::get('/credits/list', [CreditsController::class, 'creditsList'])
        ->middleware('check.rep.permission:Credits')
        ->name('credits.list');

    Route::get('/receipts/list/receipt/{receipt_id}', [ReceiptController::class, 'receiptView'])->name('receipts.receipt');

    /*
    |-------------------------
    | Customer-only (Creating orders / POs)
    |-------------------------
    */
    Route::post('/orders/order/create', [OrderController::class, 'createOrder'])->middleware('check.rep.permission:Order')->name('order.create');



    Route::post('/purchase-orders/create', [PurchaseOrderController::class, 'createPurchaseOrder'])->middleware('check.rep.permission:PO')->name('purchaseorders.create');
    Route::post('/receipts/create', [ReceiptController::class, 'receiptUpload'])->middleware('check.rep.permission:POP')->name('receipt.create');

    /*
    |-------------------------
    | Error
    |-------------------------
    */




});

Route::middleware(['auth', 'role:Customer|Admin|Staff'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | !! - PR REQ
    |--------------------------------------------------------------------------
    */

    Route::get('/requests/list', [PurchaseRequestController::class, 'list'])
        ->name('pr.list');

    Route::post('/requests/create/purchase', [PurchaseRequestController::class, 'create'])
        ->name('pr.create');

    
    // Purchase Request Routes
    Route::prefix('purchase-requests')->name('pr.')->group(function () {
        
        Route::get('/collection/{po_id}', [PurchaseRequestController::class, 'collection'])
            ->name('collection');

        // Create new purchase request
        Route::post('/create', [PurchaseRequestController::class, 'create'])
            ->name('create')
            ->middleware('role:Customer'); // Only customers can create
        
        // View single purchase request detailsd
        Route::get('/list/request/{po_id}', [PurchaseRequestController::class, 'request'])
            ->name('request');
        Route::post('/{po_id}/confirm', [PurchaseRequestController::class, 'confirm'])->name('confirm');

        // // Update purchase request status (for staff)
        // Route::patch('/{poId}/status', [PurchaseRequestController::class, 'updateStatus'])
        //     ->name('update.status')
        //     ->middleware('role:Staff');
        
        // // Cancel purchase request (for customers)
        // Route::patch('/{poId}/cancel', [PurchaseRequestController::class, 'cancel'])
        //     ->name('cancel')
        //     ->middleware('role:Customer');
    });
    
    Route::prefix('credit')->name('crd.')->group(function () {
        
        Route::get('/list', [CreditsRequestController::class, 'list'])
            ->name('list');
        Route::get('/download-image/{payment_id}', [CreditsRequestController::class, 'download'])->name('download');

    });
    Route::prefix('delivery')->name('dlv.')->group(function () {
        Route::get('/delivery/{delivery_id}', [DeliveryRequestController::class, 'delivery'])
            ->name('delivery');
        Route::post('/delivery/receive-order/', [DeliveryRequestController::class, 'receive'])
            ->name('receive');
        Route::get('/list', [DeliveryRequestController::class, 'list'])
            ->name('list');
    });
    Route::prefix('payment')->name('pym.')->group(function (): void {
        Route::post('/payment/create/', [PaymentsController::class, 'create'])
            ->name('create');
        Route::get('/payment/list/', [PaymentsController::class, 'list'])
            ->name('list');
        Route::get('/payment/list/payment/{payment_id}', [PaymentsController::class, 'payment'])
            ->name('payment');
        Route::get('/payment/collection/{po_id}', [PaymentsController::class, 'collection'])
            ->name('collection');
        Route::post('/payment/list/action/', [PaymentsController::class, 'verify'])
            ->name('verify');
    });
    Route::prefix('promo')->name('prm.')->group(function () {
        Route::get('/list', [PromosController::class, 'list'])
            ->name('list');
        Route::post('/create', [PromosController::class, 'create'])
            ->name('create');
    });
    Route::prefix('prd')->name('prd.')->group(function () {
        Route::get('/list', [CabraController::class, 'list'])
            ->name('list');
    });
    Route::prefix('return')->name('rtn.')->group(function () {
        Route::get('/list', [ReturnsController::class, 'list'])
            ->name('list');
        Route::post('/create', [ReturnsController::class, 'create'])->name('create');
    });
    /*
    |--------------------------------------------------------------------------
    | Dashboard (with Representative Permission Check)
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/view', [DashboardController::class, 'dashboardView'])
        ->middleware(['auth', 'check.rep.permission:Dashboard'])
        ->name('dashboard.view');

    Route::get('/dashboard/layout', [DashboardController::class, 'layoutView'])
        ->middleware('auth')
        ->name('dashboard.layout');


    /*
    |-------------------------
    | Products & Categories
    |-------------------------
    */
    Route::get('/purchase-orders/list', [PurchaseOrderController::class, 'purchaseOrderList'])
        ->middleware('check.rep.permission:PO')
        ->name('purchaseorders.list');
    Route::get('/purchase-orders/list/view/{po_id}', [PurchaseOrderController::class, 'purchaseOrderView'])->middleware('check.rep.permission:PO')->name('purchaseorders.purchaseorder');
    Route::get('/orders/list/view/{order_id}', [OrderController::class, 'orderView'])->middleware('check.rep.permission:Orders')->name('orders.order');




    Route::get('/products/list', [ProductController::class, 'productList'])
        ->middleware(['check.rep.permission:Products', 'check.customer'])
        ->name('products.list');

    Route::get('/products/product/view/{product_id}', [ProductController::class, 'productView'])
        ->middleware(['check.rep.permission:Products', 'check.customer'])
        ->name('products.product');
    Route::get('/products/{product_id}/info', [ProductController::class, 'info'])->name('products.info');

    Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
    Route::get('/categories/{parentId}/children', [CategoryController::class, 'children'])->name('categories.children');
    Route::get('/products/tree', [CategoryController::class, 'productTree'])->name('products.tree');
    Route::get('/products/{parentProductId}/children', [CategoryController::class, 'productChildren'])->name('products.children');

    /*
    |-------------------------
    | Orders & Receipts & Credits
    |-------------------------
    */
    Route::get('/orders/list', [OrderController::class, 'orderList'])
        ->middleware(['check.rep.permission:Orders', 'check.customer'])
        ->name('orders.list');

    Route::get('/receipts/list', [ReceiptController::class, 'receiptList'])
        ->middleware(['check.rep.permission:POP', 'check.customer'])
        ->name('receipts.list');

    Route::get('/credits/list', [CreditsController::class, 'creditsList'])
        ->middleware(['check.rep.permission:Credits', 'check.customer'])
        ->name('credits.list');

    Route::get('/receipts/list/receipt/{receipt_id}', [ReceiptController::class, 'receiptView'])
        ->middleware(['check.rep.permission:POP', 'check.customer'])
        ->name('receipts.receipt');

    /*
    |-------------------------
    | Profile & Groups
    |-------------------------
    */
    Route::get('/profile/view', [ProfileController::class, 'profileView'])
        ->middleware('check.rep.permission:Profile')
        ->name('profile.view');

    Route::get('/groups/view', [GroupsController::class, 'groupsView'])->middleware('check.rep.permission:Groups')->name('groups.view');
    Route::post('/groups/modify/account', [GroupsController::class, 'modifyAccount'])->middleware('check.rep.permission:Groups')->name('group.modify');

    /*
    |-------------------------
    | Delivery
    |-------------------------
    */
    Route::post('/delivery/confirm', [DeliveryController::class, 'confirmDelivery'])->name('delivery.confirm');
    Route::get('/order/deliveries/{delivery_id}', [DeliveryController::class, 'deliveryView'])->name('order.delivery_items');

    /*
    |-------------------------
    | Credits
    |-------------------------
    */
    Route::get('/orders/receipt/{order_id}', [ReceiptController::class, 'orderReceipts'])->middleware('check.rep.permission:Credits')->name('orders.receipt');


});

/*
|--------------------------------------------------------------------------
| Admin & Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin|Staff'])->group(function () {

    // Customers & Staffs
    Route::get('/customers/list', [CustomersController::class, 'customersList'])->name('customers.list');
    Route::get('/customers/list/customer/{customer_id}', [CustomersController::class, 'customerView'])->name('customers.customer');

    Route::get('/staffs/list', [StaffsController::class, 'staffsList'])->name(name: 'staffs.list');
    Route::get('/staffs/list/staff/{staff_id}', [StaffsController::class, 'staffView'])->name('staffs.staff');
    Route::post('/customer/confirm', [CustomersController::class, 'customerConfirm'])->name('customer.confirm');
    Route::post('/review/confirm', [ErrorController::class, 'reviewConfirm'])->name('review.confirm');

    Route::get('/logs/list', [LogsController::class, 'logsList'])->name('logs.list');

    // Product & Category Management
    Route::post('/products/add', [ProductController::class, 'addProduct'])->name('product.add');
    Route::get('/categories/manage', [CategoryController::class, 'manage'])->name('categories.manage');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
    Route::post('/products/setting/modify', [ProductSettingController::class, 'modifyProduct'])->name('productset.modify');

    // Delivery
    Route::get('/deliveries/list', [DeliveryController::class, 'deliveryList'])->name('deliveries.list');

    // Product Sale
    Route::post('/products/setting/sale', [ProductSalesController::class, 'addSale'])->name('productset.sale');

    // Global Ceiling
    Route::post('/products/set/global-ceiling', [GlobalCeilingController::class, 'setGlobalCeiling'])->name('set.global_ceiling');

    // PO & Order Actions
    Route::post('/purchase-orders/purchase/view/place', [OrderController::class, 'placeOrderItems'])->name('purchaseorders.place');
    Route::post('/purchase-orders/{po_id}/confirm', [PurchaseOrderController::class, 'confirmPurchaseOrder'])->name('purchaseorders.confirm');

    Route::post('/receipts/action/{receipt_id}', [ReceiptController::class, 'receiptAction'])->name('receipts.action');
    Route::post('/order/action', [OrderController::class, 'orderAction'])->name('order.action');

    Route::post('/order/action/delivery/process', [DeliveryController::class, 'orderProcess'])->name('order.process');

    // PDF views
    Route::get('/orders/{order_id}/customer-order', [OrderController::class, 'customerOrderPdf'])->name('orders.customer.pdf');
    Route::get('/orders/{order_id}/delivery-receipt', [OrderController::class, 'deliveryReceiptPdf'])->name('orders.delivery.pdf');
    Route::get('/orders/{order_id}/sales-invoice', [OrderController::class, 'salesInvoicePdf'])->name('orders.invoice.pdf');
    Route::get('/orders/{order_id}/return-slip', [OrderController::class, 'returnSlip'])->name('orders.return-slip.pdf');

    Route::get('/purchase-orders/{po_id}/pdf', [PurchaseOrderController::class, 'purchaseOrderPdf'])->name('purchaseorders.pdf');

    Route::post('/products/add-sub/{product_id}', [ProductController::class, 'addSub'])->name('add.subproduct');
    Route::post('/products/update-parent', [ProductController::class, 'updateParent'])->name('products.updateParent');


    // products
    Route::put('/products/{product_id}/update', [ProductController::class, 'update'])->name('product.update');

    //sale/discount
    Route::post('/set-sale-discount', [SaleDiscountController::class, 'store'])->name('set.sale_discount');

    Route::get('/review/changes/{user_id}', [ErrorController::class, 'review'])->name('error.changes');

});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::post('/account/staff/registration', [UserController::class, 'registerStaff'])->name('registration.staff.register');
    Route::post('/staffs/modify', [StaffsController::class, 'modifyStaff'])->name('staff.modify');
});
