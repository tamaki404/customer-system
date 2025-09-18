<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;

use App\Models\Suppliers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\SupplierController;


Route::get('/registration/signin', function () {
    return view('registration.signin');
})->name('signin');



Route::get('/account/registration', [RegistrationController::class, 'showSignupForm'])->name('registration.signup');
Route::post('/account/supplier/registration', [UserController::class, 'registerSupplier'])->name('registration.supplier.register');
Route::post('/account/signin', [UserController::class, 'signin'])->name('account.signin');
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



Route::middleware(['auth', 'role:Admin|Staff'])->group(function () {
    Route::get('/customers/list',  [CustomersController::class, 'customersList'])->name('customers.list');
    Route::get('/customers/list/customer/{supplier_id}',  [CustomersController::class, 'customerView'])->name('customers.customer');
    Route::get('/staffs/list',  [StaffsController::class, 'staffsList'])->name('staffs.list');
    Route::get('/staffs/list/staff/{staff_id}',  [StaffsController::class, 'staffView'])->name('staffs.staff');

    Route::post('/supplier/confirm/account', [SupplierController::class, 'supplier.confirm.account'])->name('supplier.confirm.account');

});


Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::post('/account/staff/registration', [UserController::class, 'registerStaff'])->name('registration.staff.register');
    Route::post('/staffs/modify', [StaffsController::class, 'modifyStaff'])->name('staff.modify');

});

