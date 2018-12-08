<?php

namespace Febalist\Laravel\Support;

use Auth;
use Illuminate\Http\Request;
use Raven_Client;

class Sentry
{
    /** @var Raven_Client */
    protected static $client;
    protected static $app_user_context;
    protected static $app_tags_context;

    public static function user(callable $callback)
    {
        static::$app_user_context = $callback;
    }

    public static function tags(callable $callback)
    {
        static::$app_tags_context = $callback;
    }

    public static function boot()
    {
        static::$client = app()->bound('sentry') && config('sentry.dsn') ? app('sentry') : null;

        if (static::$client) {
            static::$client->setRelease(config('app.version'));

            static::add_tags([
                'name' => config('app.name'),
                'host' => str_after(config('app.url'), '://'),
                'console' => app()->runningInConsole(),
                'command' => implode(' ', request()->server('argv', [])) ?: null,
            ]);

            if ($app_tags_context = static::$app_tags_context) {
                static::add_tags($app_tags_context());
            }
        }
    }

    public static function middleware(Request $request)
    {
        if (static::$client) {
            $user = Auth::user();

            static::add_tags([
                'route' => $request->route()->getName(),
                'action' => $request->route()->getActionName(),
            ]);

            static::add_user([
                'id' => $user->id ?? 0,
                'guest' => !$user,
                'ip_address' => $request->ip(),
            ]);

            if ($user) {
                if ($app_user_context = static::$app_user_context) {
                    static::add_user($app_user_context($user));
                }
            }

            if ($dsn = static::public_dsn()) {
                javascript('sentry', [
                    'dsn' => $dsn,
                    'context' => static::$client->context,
                    'debug' => config('app.debug'),
                    'release' => config('version.number'),
                    'environment' => config('app.env'),
                ]);
            }
        }
    }

    protected static function add_tags(array $data)
    {
        static::$client->tags_context(array_filter($data));
    }

    protected static function add_user(array $data)
    {
        static::$client->user_context(array_filter($data));
    }

    protected static function public_dsn()
    {
        if ($dsn = config('sentry.dsn')) {
            preg_match_all('/^https:\/\/(\w+):(\w+)@(.+)$/', $dsn, $matches, PREG_SET_ORDER, 0);
            if ($matches) {
                return "https://{$matches[0][1]}@{$matches[0][3]}";
            }
        }

        return null;
    }
}
