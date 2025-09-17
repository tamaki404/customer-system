<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    
        public function handle($request, Closure $next, ...$roles)
        {
            if (!Auth::check()) {
                return redirect('login');
            }

            // Handle pipe-separated roles (e.g., 'Admin|Staff')
            $allowedRoles = [];
            foreach ($roles as $role) {
                if (strpos($role, '|') !== false) {
                    $allowedRoles = array_merge($allowedRoles, explode('|', $role));
                } else {
                    $allowedRoles[] = $role;
                }
            }

            if (!in_array(Auth::user()->role, $allowedRoles)) {
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        }
    

}
