<?php

namespace Febalist\Laravel\Support;

use Blade;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
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
        $this->bootCarbon();
        $this->bootValidator();
        $this->bootCollections();
        $this->bootBlade();
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

    protected function bootBlade()
    {
        $this->publishes([__DIR__.'/../views' => resource_path('views/vendor/support')]);
        $this->loadViewsFrom(__DIR__.'/../views', 'support');

        Blade::aliasComponent('support::components.alert', 'alert');
        Blade::aliasComponent('support::components.group', 'group');
        Blade::aliasComponent('support::components.delete', 'delete');

        Blade::if('debug', function () {
            return config('app.debug');
        });
    }

    protected function bootCarbon()
    {
        \Carbon\Carbon::useMonthsOverflow(false);
    }

    protected function bootCollections()
    {
        CollectionMacro::boot();
    }

    protected function bootValidator()
    {
        Validator::extend('float', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/\s/', '', $value);

            return preg_match('/^[-+]?\d+[.,]?\d*$/', $value);
        });

        Validator::extend('latin', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[a-zA-Z]/u', $value);
        });
    }
}
