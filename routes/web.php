<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ViewController;

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

Route::get('/staffs', function () {
    return view('staffs');
})->name('staffs');