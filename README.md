## Currency for Laravel 4 - Beta

This provides Laravel 4 with currency functions.

### Installation

- [Currency on GitHub](https://github.com/torann/laravel-currency)

To get the latest version of Currency simply require it in your `composer.json` file.

~~~
"torann/laravel-currency": "dev-master"
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

### Updating exchange rate from Yahoo

~~~
php artisan currency:update
~~~

### Rendering

Using the Blade helper

~~~html
@currency(12.00, 'USD')
~~~

- The first parameter is the amount.
- *optional* The second parameter is the ISO 4217 currency code. If not set it will use the default set in the config file.

~~~php
echo Currency::format(12.00, 'USD');
~~~
