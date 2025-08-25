<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictSuperAdminFromStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block super admin from accessing staff functionality
        if (auth()->guard('staff')->check()) {
            $user = auth()->guard('staff')->user();
            
            // Super admin should not access staff routes
            if ($user->email === 'admin@africacdc.org') {
                abort(403, 'Super admin cannot access staff functionality. Please use the admin dashboard.');
            }
        }
        
        return $next($request);
    }
}
