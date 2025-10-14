<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRepresentativePermission
{
    public function handle($request, Closure $next, $permissionKey)
    {
        $user = Auth::user();

        // Bypass permission check for Admin & Staff
        if (in_array($user->role, ['Admin', 'Staff'])) {
            return $next($request);
        }

        // If Representative is authenticated
        if (auth('representative')->check()) {
            $rep = auth('representative')->user();

            //  Make sure $permissions is always an array
            $permissions = $rep->permissions;
            if (is_string($permissions)) {
                $permissions = json_decode($permissions, true);
            }

            if (is_array($permissions) && !empty($permissions[$permissionKey]) && $permissions[$permissionKey] === true) {
                return $next($request);
            }

            abort(403, 'Unauthorized');
        }

        abort(403, 'Unauthorized');
    }
}
