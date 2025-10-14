<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRepresentativePermission
{
    public function handle($request, Closure $next, $permissionKey)
    {
        $user = Auth::user();

        // If Admin or Staff — skip permission check
        if (in_array($user->role, ['Admin', 'Staff'])) {
            return $next($request);
        }

        // If Representative is authenticated separately
        if (auth('representative')->check()) {
            $rep = auth('representative')->user();
            $permissions = json_decode($rep->permissions, true);

            if (!empty($permissions[$permissionKey]) && $permissions[$permissionKey] === true) {
                return $next($request);
            }

            abort(403, 'Unauthorized');
        }

        // Supplier but no representative selected
        abort(403, 'Unauthorized');
    }
}
