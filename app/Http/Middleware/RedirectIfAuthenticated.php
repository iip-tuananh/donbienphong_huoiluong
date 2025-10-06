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
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'customer') {
                    return redirect()->route('front.home-page');
                }
                return redirect('admin/common/dashboard');
            }
        }

        return $next($request);

//        if (Auth::guard($guard)->check()) {
//            return redirect('admin/common/dashboard');
//        }
//
//        return $next($request);
    }
}
