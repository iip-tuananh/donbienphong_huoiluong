<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = $request->user();

        if ($user && $user->can($permission) ) {
            return $next($request);
        }
        return $request->expectsJson()
            ? response()->json(['message' => 'Không có quyền!'], 403)
            : abort(403, 'Không có quyền!');


//        if (Auth::user() && Auth::user()->can($permission) || Auth::user()->id == 1) {
//            return $next($request);
//        } else {
//			if ($request->ajax()){
//                return errorResponse("Không có quyền!");
//            }
//            return response()->view('not_found');
//        }
    }
}
