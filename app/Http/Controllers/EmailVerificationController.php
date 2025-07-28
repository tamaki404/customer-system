<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice
     */
    public function show()
    {
        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/dashboard')->with('success', 'Email already verified!');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            
            // Update user status to active after verification
            $request->user()->update(['acc_status' => 'Active']);
        }

        return redirect('/dashboard')->with('success', 'Email verified successfully!');
    }

    /**
     * Send a new verification email
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/dashboard')->with('info', 'Email already verified!');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent!');
    }
}