# Upgrade

## Upgrading To 0.2 From 0.3

There was a move to middleware in v0.3 for handling user currency switching. Using the middleware, this fixes [Bug 33](https://github.com/Torann/laravel-currency/issues/33).

### Add Middleware

Append the middleware class to the `$middleware` variable within `app/Http/Kernel.php`.

```php
protected $middleware = [

    \Torann\Currency\Middleware\CurrencyMiddleware::class,

]
```