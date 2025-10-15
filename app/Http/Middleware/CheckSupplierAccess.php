<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSupplierAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Only apply lock check for Suppliers
        if ($user && $user->role === 'Supplier') {
            if (empty($user->acc_status->staff_id)) {
                return redirect()->route('locked.page')
                    ->with('error', 'Your account is currently locked. Please wait for a staff member to set it up.');
            }
        }

        // Allow Admin and Staff without restrictions
        return $next($request);
    }
}
