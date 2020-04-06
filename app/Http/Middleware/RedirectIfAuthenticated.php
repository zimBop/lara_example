<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $redirects = [
            'web' => '/account',
            'admin' => route(R_ADMIN_DASHBOARD)
        ];

        if (Auth::guard($guard)->check()) {
            return redirect($redirects[$guard] ?? $redirects['web']);
        }

        return $next($request);
    }
}
