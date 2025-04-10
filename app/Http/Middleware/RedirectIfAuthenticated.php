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
        if (Auth::guard($guard)->check()) {
            $authUser = Auth::user();
            if($authUser->role_type == 1) {
                return redirect(RouteServiceProvider::HOME);
            } else if($authUser->role_type == 2) {
                return redirect(route('dealer.home'));
            } else if($authUser->role_type == 3){
                return redirect(route('customer.home'));
            }
        }

        return $next($request);
    }
}
