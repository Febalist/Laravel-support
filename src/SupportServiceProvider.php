<?php

namespace Febalist\LaravelSupport;

use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use View as ViewFactory;

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

        ViewFactory::composer('support::layouts.master', function (View $view) {
            $transfer = array_merge_recursive([
                'csrfToken' => csrf_token(),
                'auth'      => Auth::check(),
            ], array_get($view->getData(), 'transfer', []));

            $view->with('transfer', $transfer);
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
