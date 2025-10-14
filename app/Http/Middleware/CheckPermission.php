<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Representatives;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // Check if the logged-in user is a representative
        if (session('is_representative') && session('rep_id')) {
            $rep = Representatives::find(session('rep_id'));

            if (!$rep) {
                abort(403, 'Representative not found.');
            }

            $permissions = json_decode($rep->permissions, true);

            // Check if the permission exists and is true
            if (empty($permissions[$permission]) || !$permissions[$permission]) {
                abort(403, 'Access denied: You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}
