<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via staff guard
        if (!auth()->guard('staff')->check()) {
            return redirect()->route('auth.admin.login')->with('error', 'Please login to access this area.');
        }

        // Check if the authenticated staff has admin privileges
        $staff = auth()->guard('staff')->user();

        if (!$staff->is_admin) {
            return redirect()->route('staff.dashboard')->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
