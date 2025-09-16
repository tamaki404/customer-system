<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

Route::get('/registration/signin', function () {
    return view('registration.signin');
})->name('signin');



Route::get('/account/registration', [RegistrationController::class, 'showSignupForm'])->name('registration.signup');
Route::post('/account/supplier/registration', [UserController::class, 'registerSupplier'])->name('registration.supplier.register');
Route::post('/account/signin', [UserController::class, 'signin'])->name('account.signin');
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');