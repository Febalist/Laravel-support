<?php

namespace Febalist\Laravel\Support\Http\Middleware;

use Closure;
use Febalist\Laravel\Support\Sentry;

class SentryContext
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
        Sentry::middleware($request);

        return $next($request);
    }
}
