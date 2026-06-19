<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdminPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Staff|null $staff */
        $staff = auth()->guard('staff')->user();

        if (! $staff) {
            abort(403);
        }

        if ($staff->isSuperAdmin() || $staff->hasRole('Super Admin')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $permission = $this->permissionForRoute($routeName);

        if ($permission === null) {
            return $next($request);
        }

        if ($staff->can($permission)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this area.');
    }

    private function permissionForRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        $map = config('admin_permissions', []);

        if (isset($map[$routeName])) {
            return $map[$routeName];
        }

        foreach ($map as $pattern => $permission) {
            if (Str::is($pattern, $routeName)) {
                return $permission;
            }
        }

        return null;
    }
}
