<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

Route::post('/login-user', [UserController::class, 'login']);


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


Route::get('/dashboard', function () {
    return view('dashboard');
});
