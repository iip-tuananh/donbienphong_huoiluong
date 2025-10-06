<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class RecordVisit
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
        if ($request->isMethod('get')) {
            $key = 'visited:'.$request->ip().':'.now()->toDateString();
            if (! \Cache::has($key)) {
                DB::table('visits')->insert([
                    'url'        => $request->path(),
                    'ip'         => $request->ip(),
                    'visited_at' => now(),
                ]);

                \Cache::put($key, true, 86400);
            }
        }
        return $next($request);
    }
}
