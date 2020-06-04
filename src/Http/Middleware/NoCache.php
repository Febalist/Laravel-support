<?php

namespace Febalist\Laravel\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class NoCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $response->header('Cache-Control', 'no-store');
        }

        return $response;
    }
}
