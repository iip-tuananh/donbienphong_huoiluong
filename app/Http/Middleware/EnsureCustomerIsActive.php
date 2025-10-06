<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerIsActive
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('customer')->user();

        if ($user && (int)$user->status === 2) {
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('front.home-page')
                ->withErrors(['locked' => 'Tài khoản của bạn đã bị khóa.']);
        }

        return $next($request);
    }
}
