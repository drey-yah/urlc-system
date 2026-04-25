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
    public function handle($request, Closure $next, $role)              /*✔ Checks if user is logged in | ✔ Checks user role | ✔ Allows access if role matches | ❌ Blocks access if not*/
    {
        if (auth()->check() && auth()->user()->role == $role) {
        return $next($request);
    }

    abort(403, 'Unauthorized');
    }
}
