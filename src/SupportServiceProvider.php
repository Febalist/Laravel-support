<?php

namespace Febalist\Laravel\Support;

use Blade;
use Illuminate\Support\ServiceProvider;
use Mingalevme\Illuminate\Lock\LaravelLockServiceProvider;
use Validator;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::useMonthsOverflow(false);

        Blade::if('debug', function () {
            return config('app.debug');
        });

        Validator::extend('float', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/\s/', '', $value);

            return preg_match('/^[-+]?\d+[.,]?\d*$/', $value);
        });

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'support');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Macro::register();

        $this->mergeConfigFrom(__DIR__.'/../config/javascript.php', 'javascript');

        $this->app->register(LaravelLockServiceProvider::class);
        $this->mergeConfigFrom(base_path('vendor/mingalevme/illuminate-lock/config/lock.php'), 'lock');

        require_once __DIR__.'/helpers.php';
    }
}
