# Laravel support

```bash
composer require febalist/laravel-support
```

`app/Http/Kernel.php`:
```php
    protected $middlewareGroups = [
        'web' => [
            // ...
            \Febalist\Laravel\Support\Http\Middleware\SupportMiddleware::class,
        ],

        'api' => [
            // ...
            \Febalist\Laravel\Support\Http\Middleware\SupportMiddleware::class,
        ],
    ];
```

## Sentry

```bash
npm install --save-dev raven-js@^3.27
```

`app/Providers/AppServiceProvider.php`
```php
use Febalist\Laravel\Support\Sentry;

//...

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

`app/Exceptions/Handler.php`
```php
use Febalist\Laravel\Support\Sentry;

//...

    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception)) {
            Sentry::report($exception);
        }

        parent::report($exception);
    }
```

```javascript
window.Vue = require('vue');
require('./../../vendor/febalist/laravel-support/js/raven');

raven.context(function() {
  // ...
});
```
