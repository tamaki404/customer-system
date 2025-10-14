<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRepresentativePermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $rep = Auth::guard('representative')->user();

        // If no rep logged in, deny access
        if (!$rep) {
            return redirect()->route('login')->withErrors(['loginError' => 'Please sign in as representative.']);
        }

        // Check if permission exists and is true
        $permissions = $rep->permissions ?? [];
        if (!isset($permissions[$permission]) || $permissions[$permission] !== true) {
            abort(403, 'Unauthorized access — you do not have permission to view this page.');
        }

        return $next($request);
    }
}
