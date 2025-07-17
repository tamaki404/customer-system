<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('login');
});

Route::post('/register-user', [UserController::class, 'register']);
Route::get('/register-view', function () {
    return view('registration');
});
