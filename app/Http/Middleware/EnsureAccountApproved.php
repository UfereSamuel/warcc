<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();

            // Admins bypass approval check
            if ($staff->is_admin) {
                return $next($request);
            }

            // Exempt routes
            $exemptRoutes = [
                'staff.pending-approval',
                'auth.logout'
            ];

            if ($request->route() && !in_array($request->route()->getName(), $exemptRoutes)) {
                if ($staff->isPending()) {
                    return redirect()->route('staff.pending-approval');
                }

                if ($staff->status === 'inactive' || $staff->status === 'suspended') {
                    auth()->guard('staff')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('auth.login')
                        ->with('error', 'Your account is inactive or suspended. Please contact an administrator.');
                }
            }
        }

        return $next($request);
    }
}
