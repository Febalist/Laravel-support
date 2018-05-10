<?php

namespace Febalist\Laravel\Support;

use Blade;
use Illuminate\Support\ServiceProvider;
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
        \Carbon\Carbon:: useMonthsOverflow(false);

        Blade::if('debug', function () {
            return config('app.debug');
        });

        Validator::extend('float', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/\s/', '', $value);

            return preg_match('/^[-+]?\d+[.,]?\d*$/', $value);
        });

        $this->loadViewsFrom(__DIR__.'/../views', 'support');
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

        if ($this->app->environment('local')) {
            $this->app->singleton(\Faker\Generator::class, function () {
                return \Faker\Factory::create(language());
            });
        }

        $this->publishes([
            __DIR__.'/../views/publishes' => base_path('resources/views'),
        ]);

        require_once __DIR__.'/helpers.php';
    }
}
