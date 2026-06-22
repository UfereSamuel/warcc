<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated staff users
        if (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();
            
            // Skip the check if already on profile completion pages or logout
            $exemptRoutes = [
                'staff.profile.complete',
                'staff.profile.complete.post',
                'auth.logout'
            ];
            
            if (!in_array($request->route()->getName(), $exemptRoutes)) {
                if ($staff->needsProfileCompletion()) {
                    return redirect()->route('staff.profile.complete')
                        ->with('info', 'Please complete your profile to access the system.');
                }
            }
        }

        return $next($request);
    }
}
