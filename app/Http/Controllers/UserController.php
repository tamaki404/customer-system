<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{



public function register(Request $request)
{
    $incomingFields = $request->validate([
        'username' => 'required|string|min:3|max:50|unique:users,username',
        'password' => 'required|string|min:6|max:100',
    ]);

    $user = User::create([
        'username' => $incomingFields['username'],
        'password' => Hash::make($incomingFields['password']), 
    ]);

    auth()->login($user);
    return redirect('/dashboard');
}

public function login(Request $request)
{
    $incomingFields = $request->validate([
        'username' => ['required'],
        'password' => ['required']
    ]);

    $user = User::where('username', $incomingFields['username'])->first();

    if (!$user) {
        return back()->withErrors([
            'loginError' => "User not found"
        ])->withInput();
    }

    if (!Hash::check($incomingFields['password'], $user->password)) {
        return back()->withErrors([
            'loginError' => "Incorrect password"
        ])->withInput();
    }

    auth()->login($user);
    $request->session()->regenerate();

    return redirect('/dashboard');
}


}
