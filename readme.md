# Laravel support

```bash
composer require febalist/laravel-support
```

`app/Http/Kernel.php`:
```php
    protected $middlewareGroups = [
        'web' => [
            // ...
            \Febalist\Laravel\Support\Http\Middleware\SentryContext::class,
        ],

        'api' => [
            // ...
            \Febalist\Laravel\Support\Http\Middleware\SentryContext::class,
        ],
    ];
```

`app/Providers/AppServiceProvider.php`
```php
    public function boot()
    {
        // ...

        Sentry::user(function (User $user) {
            return [
                'name' => $user->name,
            ];
        });

        Sentry::tags(function () {
            return [
                'name' => config('app.name'),
            ];
        });
    }
```
