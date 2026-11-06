<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Support either array of roles or comma-separated roles
            $allowedRoles = [];
            foreach ($roles as $r) {
                $allowedRoles = array_merge($allowedRoles, array_map('trim', explode(',', $r)));
            }

            foreach ($allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    // If user accessed a section belonging to one of their valid roles, sync active_role
                    if (session('active_role') !== $role && in_array($role, $user->allRoles())) {
                        session(['active_role' => $role]);
                    }
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized access to this portal.');
    }
}
