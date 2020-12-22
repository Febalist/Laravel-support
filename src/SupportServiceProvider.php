<?php

namespace Febalist\Laravel\Support;

use Blade;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use ReflectionMethod;
use Validator;

class SupportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerMacro(Blueprint::class, MacroBlueprint::class);
        $this->registerMacro(Collection::class, MacroCollection::class);
    }

    public function boot()
    {
        $this->bootBlade();
        $this->bootCarbon();
        $this->bootValidator();
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
        Carbon::useMonthsOverflow(false);
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

    protected function registerMacro($class, $macro)
    {
        $methods = (new ReflectionClass($macro))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);
            $class::macro($method->name, function (...$args) use ($macro, $method) {
                if ($method->isStatic()) {
                    return $macro::{$method->name}(...$args);
                } else {
                    return $this->{$method->name}(...$args);
                }
            });
        }
    }
}
