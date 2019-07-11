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
        $this->bootJavascript();
        $this->bootSentry();
        $this->bootCarbon();
        $this->bootValidator();
        $this->bootCollections();
        $this->bootBlade();
        $this->bootQueue();

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
        $this->mergeConfigFrom(__DIR__.'/../config/sentry.php', 'sentry');

        require_once __DIR__.'/helpers.php';
    }

    protected function bootBlade()
    {
        $this->publishes([__DIR__.'/../views' => resource_path('views/vendor/support')]);
        $this->loadViewsFrom(__DIR__.'/../views', 'support');

        Blade::component('support::components.alert', 'alert');
        Blade::component('support::components.group', 'group');
        Blade::component('support::components.delete', 'delete');

        Blade::if('debug', function () {
            return config('app.debug');
        });
    }

    protected function bootQueue()
    {
        Queue::before(function (JobProcessing $event) {
            $class = $event->job->resolveName();

            $this->app->bindMethod("$class@handle", function ($job, $app) {
                if (method_exists($job, 'beforeHandle')) {
                    $job->beforeHandle();
                }

                $job->handle();

                if (method_exists($job, 'afterHandle')) {
                    $job->afterHandle();
                }
            });
        });
    }

    protected function bootSentry()
    {
        Sentry::instance()->boot();
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

    protected function bootJavascript()
    {
        javascript([
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'version' => config('sentry.release'),
        ]);
    }
}
