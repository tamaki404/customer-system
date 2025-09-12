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

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:4|max:15|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'nullable|digits:11|starts_with:09|unique:users,mobile',
            'telephone' => 'nullable|regex:/^0\d{1,3}-\d{6,7}$/',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:100|confirmed',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 
            'user_type' => 'required|string',
            'store_name' => 'required|string|max:255',
            'acc_status' => 'required|string|max:255',
            'action_by' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $imageData = file_get_contents($request->file('image')->getRealPath());
            $validated['image'] = base64_encode($imageData);
            $validated['image_mime'] = $request->file('image')->getMimeType();
        } else {
            $defaultImagePath = public_path('assets/default/store-default.jpg');
            $imageData = file_get_contents($defaultImagePath);
            $validated['image'] = base64_encode($imageData);
            $validated['image_mime'] = mime_content_type($defaultImagePath);
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'telephone' => $validated['telephone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'image' => $validated['image'],
            'image_mime' => $validated['image_mime'],
            'user_type' => $validated['user_type'],
            'store_name' => $validated['store_name'],
            'acc_status' => 'Pending',
            'action_by' => $validated['action_by'],
            'email_verified_at' => null,
        ]);

        $tokenRecord = EmailVerificationToken::createToken($validated['email']);
        $token = $tokenRecord->token;

        Mail::to($user->email)->send(new EmailVerification($user, $token));

        return redirect('/login')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');
    }


    public function updateImage(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = User::findOrFail($request->id);

        $imageData = file_get_contents($request->file('image')->getRealPath());
        $user->image = base64_encode($imageData);
        $user->image_mime = $request->file('image')->getMimeType();

        $user->save();

        return redirect()->back()->with('success', 'Profile image updated successfully!');
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
                'email_verified_at' => now()
            ]);
            
            // Clear any pending verification session data
            session()->forget(['pending_verification_user_id', 'pending_verification_email']);
            
            return redirect('/login')->with('success', 'Email verified successfully! Please wait for admin confirmation before you can login.');
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
        $tokenRecord = EmailVerificationToken::createToken($user->email);
        $token = $tokenRecord->token;
        Mail::to($user->email)->send(new EmailVerification($user, $token));

        return back()->with('success', 'Verification email resent!');
    }



// public function login(Request $request)
// {
//     $incomingFields = $request->validate([
//         'username' => ['required'],
//         'password' => ['required'],
//     ]);

//     $user = User::where('username', $incomingFields['username'])->first();
//     $user = User::where('username', $incomingFields['username'])
//         ->orWhere('email', $incomingFields['username'])
//         ->first();

//     if (!$user) {
//         return back()->withErrors([
//             'loginError' => "User not found"
//         ])->withInput();
//     }

//     if (!Hash::check($incomingFields['password'], $user->password)) {
//         return back()->withErrors([
//             'loginError' => "Incorrect password"
//         ])->withInput();
//     }

//     // Check if email is verified first
//     if (!$user->hasVerifiedEmail()) {
//         // Store user info in session for verification page
//         session(['pending_verification_user_id' => $user->id]);
//         session(['pending_verification_email' => $user->email]);
//         return redirect('/verify-email-pending')->with('error', 'Please verify your email address before logging in.');
//     }

//     // Check if account is active (only after email verification)
//     if ($user->acc_status !== 'Active' && $user->acc_status !== 'active') {
//         return back()->withErrors([
//             'loginError' => "Your account is not active. Please wait for admin confirmation."
//         ])->withInput();
//     }

//     auth()->login($user);
//     $request->session()->regenerate();

//     return redirect('/dashboard');
// }


public function login(Request $request)
{
    $incomingFields = $request->validate([
        'username' => ['required'], // Can be username or email
        'password' => ['required'],
    ]);

    $loginValue = $incomingFields['username'];

    // Find user by username or email
    $user = User::where('username', $loginValue)
        ->orWhere('email', $loginValue)
        ->first();

    if (!$user || !Hash::check($incomingFields['password'], $user->password)) {
        // Generic to avoid user enumeration
        return back()->withErrors([
            'loginError' => "Invalid credentials"
        ])->withInput();
    }

    if (!$user->hasVerifiedEmail()) {
        session([
            'pending_verification_user_id' => $user->id,
            'pending_verification_email' => $user->email
        ]);
        return redirect('/verify-email-pending')->with('error', 'Please verify your email address before logging in.');
    }

    if (strtolower($user->acc_status) !== 'active') {
        return back()->withErrors([
            'loginError' => "Your account is not active. Please wait for admin confirmation."
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
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'user_type' => 'required',
        'acc_status' => 'required',
        'action_by' => 'string',
            'mobile' => 'nullable|string|max:15',
            'store_name' => 'nullable|string|max:255',
    ]);
    

    // Handle image upload to base64
    if ($request->hasFile('image')) {
        $imageData = file_get_contents($request->file('image')->getRealPath());
        $validated['image'] = base64_encode($imageData);
        $validated['image_mime'] = $request->file('image')->getMimeType();
    } else {
        $validated['image'] = null;
        $validated['image_mime'] = null;
    }

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'password' => Hash::make($validated['password']),
        'image' => $validated['image'],
        'image_mime' => $validated['image_mime'] ?? null,
        'user_type' => $validated['user_type'],
        'acc_status' => $validated['acc_status'],
        'action_by' => $validated['action_by'],
        'name' => $validated['name'],
        'email' => $validated['email'],
        'mobile' => $validated['mobile'] ?? 'n/a',
        'store_name' => $validated['store_name'] ?? null,

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
        try {
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

        return redirect()->back()->with('success', 'Changes saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage());
        }


}

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Reuse same checks as login: email verified and active status
        if (!$user->hasVerifiedEmail()) {
            return back()->with('error', 'Please verify your email before changing password.');
        }
        if (strtolower($user->acc_status) !== 'active') {
            return back()->with('error', 'Your account is not active.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Optionally log user out of other sessions by regenerating session
        $request->session()->regenerate();

        return back()->with('success', 'Password changed successfully.');
    }

    public function checkCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        $user = auth()->user();
        $matches = Hash::check($request->current_password, $user->password);

        return response()->json([
            'valid' => $matches,
            'message' => $matches ? '' : 'Current password is incorrect.'
        ]);
    }

}




