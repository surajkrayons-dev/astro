<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = auth()->user();

        // ✅ admin full access
        if ($user->type === 'admin') {
            return $next($request);
        }

        if ($user->type === 'employee') {

            $route = $request->route()?->getName();

            $module = explode('.', $route)[1] ?? null;

            // 🔥 ALWAYS ALLOW THESE MODULES
            $alwaysAllowed = ['dashboard', 'profile'];

            if (in_array($module, $alwaysAllowed)) {
                return $next($request);
            }

            // 🔒 permission check
            if (!$user->hasAccess($module)) {
                abort(403);
            }
        }

        return $next($request);
    }
}