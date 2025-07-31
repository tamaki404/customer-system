<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();

        // Check if user account is active
        if ($user->acc_status !== 'Active' && $user->acc_status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is not active. Please contact administrator.');
        }

        // Check if email is verified (optional - you can remove this if not needed)
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('error', 'Please verify your email address.');
        }

        return $next($request);
    }
}
