<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if ($user && (int)$user->status === 0) {
            Auth::guard('admin')->logout();

            return redirect()
                ->route('login')
                ->withErrors(['locked' => 'Tài khoản của bạn đã bị khóa.']);
        }

        return $next($request);
    }
}
