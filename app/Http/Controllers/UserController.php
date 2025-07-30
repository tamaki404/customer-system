<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller 
{

//email verification






    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:4|max:15|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'nullable|digits:11|starts_with:09',
            'telephone' => 'nullable|regex:/^0\d{1,3}-\d{6,7}$/',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:100|confirmed',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'user_type' => 'required|string',
            'store_name' => 'required|string|max:255',
            'acc_status' => 'required|string|max:255',
            'action_by' => 'nullable|string|max:255',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = $imageName;
        }

        // Create user with unverified email
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'telephone' => $validated['telephone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'image' => $validated['image'] ?? null,
            'user_type' => $validated['user_type'],
            'store_name' => $validated['store_name'],
            'acc_status' => 'Pending', 
            'action_by' => $validated['action_by'],
            'email_verified_at' => null, 
        ]);

        $token = bin2hex(random_bytes(32));
        EmailVerificationToken::createToken($validated['email']);

        Auth::login($user);

        
        Mail::to($user->email)->send(new EmailVerification($user, $token));

        return redirect('/email/verify')->with('success', 'Registration successful! Please check your email to verify your account.');
    }


       public function verifyEmail($token)
    {
        $email = request('email');
        
        if (!$email || !EmailVerificationToken::validateToken($email, $token)) {
            return redirect('/login')->with('error', 'Invalid or expired verification link.');
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update([
                'email_verified_at' => now(),
                'acc_status' => 'Active' 
            ]);
            
            return redirect('/login')->with('success', 'Email verified successfully! You can now login.');
        }

        return redirect('/login')->with('error', 'User not found.');
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)
                   ->whereNull('email_verified_at')
                   ->first();
        
        if (!$user) {
            return back()->with('error', 'User not found or already verified.');
        }

        // Generate new token and send email
        $token = bin2hex(random_bytes(32));
        EmailVerificationToken::createToken($user->email);
        Mail::to($user->email)->send(new EmailVerification($user, $token));

        return back()->with('success', 'Verification email resent!');
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
        'password' => 'required|string|min:8|max:100',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
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
        'name' => $validated['name'],
        'email' => $validated['email'],

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


    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type === 'Customer') {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:20',
                'telephone' => 'nullable|string|max:20'

            ]);
            $user->name = $request->input('name');
            $user->address = $request->input('address');
            $user->mobile = $request->input('mobile');
            $user->telephone = $request->input('telephone');

            $user->save();
        } elseif ($user->user_type === 'Admin') {
            $request->validate([
                'name' => 'required|string|max:255',
                'store_name' => 'nullable|string|max:255',
                'mobile' => 'nullable|string|max:20',
            ]);
            $user->name = $request->input('name');
            $user->store_name = $request->input('store_name');
            $user->mobile = $request->input('mobile');
            $user->save();
        }
        // Staff: no update allowed

        return back()->with('success', 'Profile updated successfully.');
    }


}





