<?php

namespace Febalist\Laravel\Support\Http\Middleware;

use Closure;
use Febalist\Laravel\Support\Sentry;

/** @deprecated */
class SupportMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        javascript([
            'csrf_token' => csrf_token(),
        ]);

        return $next($request);
    }
}
