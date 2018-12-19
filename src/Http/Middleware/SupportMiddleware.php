<?php

namespace Febalist\Laravel\Support\Http\Middleware;

use Closure;
use Febalist\Laravel\Support\Sentry;

class SupportMiddleware
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
        Sentry::instance()->middleware($request);

        return $next($request);
    }
}