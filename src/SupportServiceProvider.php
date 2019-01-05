<?php

namespace Febalist\Laravel\Support;

use Blade;
use Illuminate\Support\Collection;
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
        javascript([
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'version' => config('version.number'),
            'csrf_token' => csrf_token(),
        ]);

        Sentry::instance()->boot();

        \Carbon\Carbon::useMonthsOverflow(false);

        Blade::if('debug', function () {
            return config('app.debug');
        });

        Validator::extend('float', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/\s/', '', $value);

            return preg_match('/^[-+]?\d+[.,]?\d*$/', $value);
        });

        if (!Collection::hasMacro('append')) {
            Collection::macro('append', function ($source) {
                foreach ($source as $item) {
                    $this->push($item);
                }

                return $this;
            });
        }

        if (!Collection::hasMacro('without')) {
            Collection::macro('without', function ($values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    $values = func_get_args();
                }

                $items = array_without($this->all(), $values);

                return collect($items);
            });
        }

        if (!Collection::hasMacro('remove')) {
            Collection::macro('remove', function ($values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    $values = func_get_args();
                }

                $collection = collect($this->all())->without($values);

                $this->replace($collection);

                return $this;
            });
        }

        if (!Collection::hasMacro('replace')) {
            Collection::macro('replace', function ($values) {
                if ($values instanceof Collection) {
                    $values = $values->all();
                } elseif (!is_array($values)) {
                    $values = func_get_args();
                }

                $this->splice(0);
                $this->append($values);

                return $this;
            });
        }

        $this->bootBlade();

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
        $this->mergeConfigFrom(__DIR__.'/../config/version.php', 'version');
        $this->mergeConfigFrom(__DIR__.'/../config/sentry.php', 'sentry');

        $this->app->register(LaravelLockServiceProvider::class);
        $this->mergeConfigFrom(base_path('vendor/mingalevme/illuminate-lock/config/lock.php'), 'lock');

        require_once __DIR__.'/helpers.php';
    }

    protected function bootBlade()
    {
        $this->publishes([__DIR__.'/../views' => resource_path('views/vendor/support')]);
        $this->loadViewsFrom(__DIR__.'/../views', 'support');

        Blade::component('support::components.alert', 'alert');
        Blade::component('support::components.group', 'group');
        Blade::component('support::components.delete', 'delete');
    }
}
