<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('registration.signin');
});
Route::get('/account/registration', [RegistrationController::class, 'showSignupForm'])->name('registration.signup');