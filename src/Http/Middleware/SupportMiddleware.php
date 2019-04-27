<?php

namespace Febalist\Laravel\Support\Http\Middleware;

use Closure;
use Febalist\Laravel\Support\Sentry;

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
        $broadcasting = [
            'driver' => config('broadcasting.default'),
        ];

        if ($broadcasting['driver'] == 'pusher') {
            $broadcasting['key'] = config('broadcasting.connections.pusher.key');
            $broadcasting['cluster'] = config('broadcasting.connections.pusher.options.cluster');
        }

        javascript([
            'csrf_token' => csrf_token(),
            'broadcasting' => $broadcasting,
        ]);

        Sentry::instance()->middleware($request);

        return $next($request);
    }
}
