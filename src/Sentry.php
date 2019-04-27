<?php

namespace Febalist\Laravel\Support;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Raven_Client;

class Sentry
{
    /** @var static */
    protected static $instance;
    /** @var Raven_Client */
    protected $client;
    protected $app_tags_callback;
    protected $app_user_callback;

    protected function __construct()
    {
        $this->client = static::enabled() ? app('sentry') : null;
    }

    public static function enabled()
    {
        return app()->bound('sentry') && config('sentry.dsn');
    }

    public static function report(Exception $exception)
    {
        if (static::enabled()) {
            static::instance()->capture($exception);
        }
    }

    public static function instance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function user(callable $callback)
    {
        static::instance()->setAppUserCallback($callback);
    }

    public static function tags(callable $callback)
    {
        static::instance()->setAppTagsCallback($callback);
    }

    public function setAppTagsCallback(callable $callback)
    {
        $this->app_tags_callback = $callback;
    }

    public function setAppUserCallback(callable $callback)
    {
        $this->app_user_callback = $callback;
    }

    public function boot()
    {
        if ($this->client) {
            $this->client->setRelease(config('app.version'));

            $this->add_tags([
                'name' => config('app.name'),
                'host' => str_after(config('app.url'), '://'),
                'console' => app()->runningInConsole(),
                'command' => implode(' ', request()->server('argv', [])) ?: null,
            ]);

            if ($app_tags_callback = $this->app_tags_callback) {
                $this->add_tags($app_tags_callback());
            }
        }
    }

    public function middleware(Request $request)
    {
        if ($this->client) {
            $user = Auth::user();

            $this->add_tags([
                'route' => $request->route()->getName(),
                'action' => $request->route()->getActionName(),
            ]);

            $this->add_user([
                'id' => $user->id ?? 0,
                'guest' => !$user,
                'ip_address' => $request->ip(),
            ]);

            if ($user) {
                if ($app_user_callback = $this->app_user_callback) {
                    $this->add_user($app_user_callback($user));
                }
            }

            if ($dsn = $this->public_dsn()) {
                javascript('sentry', [
                    'dsn' => $dsn,
                    'context' => $this->client->context,
                ]);
            }
        }
    }

    protected function capture(Exception $exception)
    {
        $this->client->captureException($exception);
    }

    protected function add_tags(array $data)
    {
        $this->client->tags_context(array_filter($data));
    }

    protected function add_user(array $data)
    {
        $this->client->user_context(array_filter($data));
    }

    protected function public_dsn()
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
