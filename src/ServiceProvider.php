<?php

namespace Febalist\LaravelSupport;

use Illuminate\Database\Schema\Blueprint;
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
        Blueprint::macro('model', function (...$arguments) {
            return Foreign::create($this, ...$arguments);
        });

        Blueprint::macro('dropModel', function (...$arguments) {
            return Foreign::drop($this, ...$arguments);
        });
    }
}
