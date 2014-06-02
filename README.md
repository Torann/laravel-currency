# Currency for Laravel 4 - Beta

[![Latest Stable Version](https://poser.pugx.org/torann/currency/v/stable.png)](https://packagist.org/packages/torann/currency) [![Total Downloads](https://poser.pugx.org/torann/currency/downloads.png)](https://packagist.org/packages/torann/currency)

This provides Laravel 4 with currency functions.

----------

## Installation

- [Currency on Packagist](https://packagist.org/packages/torann/currency)
- [Currency on GitHub](https://github.com/torann/laravel-currency)

To get the latest version of Currency simply require it in your `composer.json` file.

~~~
"torann/currency": "dev-master"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once Currency is installed you need to register the service provider with the application. Open up `app/config/app.php` and find the `providers` key.

~~~php
'providers' => array(

    'Torann\Currency\CurrencyServiceProvider',

)
~~~

Currency also ships with a facade which provides the static syntax for creating collections. You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~php
'aliases' => array(

    'Currency' => 'Torann\Currency\Facades\Currency',

)
~~~

Create configuration file using artisan

~~~
$ php artisan config:publish torann/currency
~~~

Generate the table by running

~~~
$ php artisan migrate --package=torann/currency
~~~

## Artisan Commands

### Updating Exchange

By default exchange rates are updated from Finance Yahoo.com.

~~~
php artisan currency:update
~~~

To upate from OpenExchangeRates.org

~~~
php artisan currency:update --openexchangerates
~~~

 > Note: An API key is needed to use [OpenExchangeRates.org](http://OpenExchangeRates.org). Add yours to the config file.

### Cleanup

Used to clean the Laravel cached exchanged rates and refresh it from the database. Note that cached exchanged rates are cleared after they are updated using one of the command above.

~~~
php artisan currency:cleanup
~~~

## Rendering

Using the Blade helper

~~~html
@currency(12.00, 'USD')
~~~

- The first parameter is the amount.
- *optional* The second parameter is the ISO 4217 currency code. If not set it will use the default set in the config file.

~~~php
echo Currency::format(12.00, 'USD');
~~~

## Change Log

#### v0.1.1

- Added support for OpenExchangeRates.org
- Added a cleanup Artisan command
- Refactored caching
- Fixed bug in the commands

#### v0.1.0

- First release