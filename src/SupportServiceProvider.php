<?php

namespace Febalist\LaravelSupport;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'support');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/support'),
        ]);

        \View::composer('support::layouts.master', function (View $view) {
            transfer('csrfToken', csrf_token());
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
