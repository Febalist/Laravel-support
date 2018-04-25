<?php

namespace Febalist\LaravelSupport;

use Blade;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Validator;

class ServiceProvider extends IlluminateServiceProvider
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
            $value = preg_replace('\s', '', $value);

            return preg_match('/^[-+]?\d+[.,]?\d*$/', $value);
        });
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
    }
}
