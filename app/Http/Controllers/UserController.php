<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{



// public function register(Request $request)
// {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'username' => 'required|string|min:3|max:50|unique:users,username',
//         'email' => 'required|email|max:255|unique:users,email',
//         'mobile' => 'required|digits:11|starts_with:09',
//         'telephone' => 'nullable|regex:/^0\d{1,3}-\d{6,7}$/',
//         'address' => 'required|string|max:255',
//         'password' => 'required|string|min:6|max:100|confirmed',
//         'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
//         'user_type' => 'required|string',
//         'store_name' => 'nullable|string|max:255',
//         'acc_status' => 'required|string|max:255',
//         'action_by' => 'nullable|string|max:255',
//     ]);

//     // Handle image upload
//     if ($request->hasFile('image')) {
//         $imageName = time() . '.' . $request->image->extension();
//         $request->image->move(public_path('images'), $imageName);
//         $validated['image'] = $imageName;
//     } else {
//         $validated['image'] = null;
//     }

//     // Create the user
//     $user = User::create([
//         'name' => $validated['name'],
//         'username' => $validated['username'],
//         'email' => $validated['email'],
//         'mobile' => $validated['mobile'],
//         'telephone' => $validated['telephone'],
//         'address' => $validated['address'],
//         'password' => Hash::make($validated['password']),
//         'image' => $validated['image'],
//         'user_type' => $validated['user_type'],
//         'store_name' => $validated['store_name'],
//         'acc_status' => $validated['acc_status'],
//         'action_by' => $validated['action_by'],
//     ]);

//     auth()->login($user);
//     return redirect('/dashboard');
// }



public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|min:4|max:15|unique:users,username', // Changed to match frontend
        'email' => 'required|email|max:255|unique:users,email',
        'mobile' => 'required|digits:11|starts_with:09', // Keep required since it's marked required in HTML
        'telephone' => 'required|regex:/^0\d{1,3}-\d{6,7}$/', // Changed to required to match HTML
        'address' => 'required|string|max:255',
        'password' => 'required|string|min:8|max:100|confirmed', // Changed to min:8 to match frontend
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // Changed to required since frontend requires it
        'user_type' => 'required|string',
        'store_name' => 'required|string|max:255', // Changed to required since frontend requires it
        'acc_status' => 'required|string|max:255',
        'action_by' => 'nullable|string|max:255',
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
        'name' => $validated['name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'mobile' => $validated['mobile'],
        'telephone' => $validated['telephone'],
        'address' => $validated['address'],
        'password' => Hash::make($validated['password']),
        'image' => $validated['image'],
        'user_type' => $validated['user_type'],
        'store_name' => $validated['store_name'],
        'acc_status' => $validated['acc_status'],
        'action_by' => $validated['action_by'],
    ]);

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
        'action_by' => 'string',
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
        'action_by' => $validated['action_by'],

    ]);


    return redirect('/staffs');
}


public function checkUsername(Request $request)
{
    $username = $request->query('username');
    
    if (empty($username)) {
        return response()->json(['available' => false, 'message' => 'Username is required']);
    }
    
    if (strlen($username) < 4 || strlen($username) > 15) {
        return response()->json(['available' => false, 'message' => 'Username must be between 4-15 characters']);
    }
    
    $exists = User::where('username', $username)->exists();
    
    return response()->json([
        'available' => !$exists,
        'message' => $exists ? 'Username is already taken' : 'Username is available'
    ]);
}


}
