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
    $validated = $request->validate([
        'username' => 'required|string|min:3|max:50|unique:users,username',
        'password' => 'required|string|min:6|max:100',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'user_type' => 'required',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $validated['image'] = $imageName;
    } else {
        $validated['image'] = null;
    }

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'password' => Hash::make($validated['password']),
        'image' => $validated['image'],
        'user_type' => $validated['user_type'],
    ]);

    // Login the user
    auth()->login($user);
    return redirect('/dashboard');
}

public function login(Request $request)
{
    $incomingFields = $request->validate([
        'username' => ['required'],
        'password' => ['required'],
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

public function logout()
{
    auth()->logout();
    return redirect('/');
}

public function dashboard()
{
    $user = auth()->user(); 

    return view('dashboard', ['user' => $user]);
}


public function addStaff(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|string|min:3|max:50|unique:users,username',
        'password' => 'required|string|min:6|max:100',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'user_type' => 'required',
        'acc_status' => 'required',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $validated['image'] = $imageName;
    } else {
        $validated['image'] = null;
    }

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'password' => Hash::make($validated['password']),
        'image' => $validated['image'],
        'user_type' => $validated['user_type'],
        'acc_status' => $validated['acc_status'],
    ]);

    // Login the user
    auth()->login($user);
    return redirect('/staffs');
}



}
