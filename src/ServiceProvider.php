<?php

namespace Febalist\LaravelSupport;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Macro::register();
    }
}
