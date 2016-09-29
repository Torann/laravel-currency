# Get Started

## Installation

- [Currency on Packagist](https://packagist.org/packages/torann/currency)
- [Currency on GitHub](https://github.com/torann/laravel-currency)

### Composer

From the command line run:

```
$ composer require torann/currency
```

### Laravel

Once installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

``` php
'providers' => [

    \Torann\Currency\CurrencyServiceProvider::class,

]
```

This package also comes with a facade, which provides an easy way to call the the class. Open up `config/app.php`` and find the aliases key.

'aliases' => [

    'Currency' => Torann\Currency\Facades\Currency::class,

];

### Publish the configurations

Run this on the command line from the root of your project:

```bash
php artisan vendor:publish --provider="Torann\Currency\CurrencyServiceProvider"
```

A configuration file will be publish to `config/currency.php`.

### Migration

If currencies are going to be stored in the database. Run migrate to setup the database table [see [Storage Drivers](/projects/laravel-currency/doc/storage-drivers.html)]. Run this on the command line from the root of your project:

Run this on the command line from the root of your project to generate the table for storing currencies:

```bash
$ php artisan migrate
```

### Middleware

Once installed you need to append the middleware class within the Http kernel. This allows visitors to change the viewed currency using the query parameter `?currency=usd`.

Open up `app/Http/Kernel.php` and find the `$middleware` variable.

```php
protected $middleware = [

    \Torann\Currency\Middleware\CurrencyMiddleware::class,

]
```