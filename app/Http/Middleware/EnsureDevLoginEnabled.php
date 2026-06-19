<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDevLoginEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('warcc.dev_login.enabled')) {
            abort(404);
        }

        return $next($request);
    }
}
