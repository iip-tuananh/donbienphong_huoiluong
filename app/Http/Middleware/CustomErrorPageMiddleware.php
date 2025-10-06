<?php

namespace App\Http\Middleware;

use Closure;

class CustomErrorPageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // if ($response->status() == 404 || $response->status() == 500) {
        //     return response()->view('site.errors', [], $response->status());
        // }

        return $response;
    }
}
