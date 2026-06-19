<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $staff = auth()->guard('staff')->user();

        if (! $staff || ! $staff->isSuperAdmin()) {
            abort(403, 'Only the super administrator can access website management.');
        }

        return $next($request);
    }
}
