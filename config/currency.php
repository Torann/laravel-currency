<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Currency
    |--------------------------------------------------------------------------
    |
    | The application currency determines the default currency that will be
    | used by the currency service provider. You are free to set this value
    | to any of the currencies which will be supported by the application.
    |
    */

    'default' => env('CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency Rates Source
    |--------------------------------------------------------------------------
    |
    | You could choose a preferred source to get actual exchange rates for
    | your currencies list. By default you could use a default free-based source.
    |
    | Supported: "currencylayer", "exchangeratesapi", "fixer", "openexchangerates"
    |
    */

    'source' => env('CURRENCY_SOURCE', 'exchangeratesapi'),

    /*
    |--------------------------------------------------------------------------
    | Sources Configuration
    |--------------------------------------------------------------------------
    |
    | Sources like "fixer", "currencylayer" and "openexchangerates" are required
    | to have API token keys. You can always just use "exchangeratesapi" source,
    | the default source, but it has limited supported currencies list.
    |
    */

    'sources' => [

        'fixer' => [
            'key' => env('CURRENCY_FIXER_KEY'),
        ],

        'currencylayer' => [
            'key' => env('CURRENCY_CURRENCYLAYER_KEY'),
        ],

        'openexchangerates' => [
            'key' => env('CURRENCY_OPENEXCHANGERATES_KEY'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Storage Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default storage driver that should be used
    | by the framework.
    |
    | Supported: "database", "filesystem"
    |
    */

    'driver' => env('CURRENCY_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Storage Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many storage drivers as you wish.
    |
    */

    'drivers' => [

        'database' => [
            'class' => \Torann\Currency\Drivers\Database::class,
            'connection' => null,
            'table' => 'currencies',
        ],

        'filesystem' => [
            'class' => \Torann\Currency\Drivers\Filesystem::class,
            'disk' => null,
            'path' => 'currencies.json',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Cache Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default cache driver that should be used
    | by the framework.
    |
    | Supported: all cache drivers supported by Laravel
    |
    */

    'cache_driver' => env('CURRENCY_CACHE_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | Currency Formatter
    |--------------------------------------------------------------------------
    |
    | Here you may configure a custom formatting of currencies. The reason for
    | this is to help further internationalize the formatting past the basic
    | format column in the table. When set to `null` the package will use the
    | format from storage.
    |
    | More info:
    | http://lyften.com/projects/laravel-currency/doc/formatting.html
    |
    */

    'formatter' => null,

    /*
    |--------------------------------------------------------------------------
    | Currency Formatter Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many currency formatters as you wish.
    |
    */

    'formatters' => [

        'php_intl' => [
            'class' => \Torann\Currency\Formatters\PHPIntl::class,
        ],

    ],
];
