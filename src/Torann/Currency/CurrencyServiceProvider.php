<?php

namespace Torann\Currency;

use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/../../config/currency.php' => config_path('currency.php'),
            ]);

            $this->mergeConfigFrom(
                __DIR__ . '/../../config/currency.php', 'currency'
            );
        }

        $this->publishes([
            __DIR__.'/../../migrations' => base_path('/database/migrations'),
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCurrency();
        $this->registerCurrencyCommands();
    }

    /**
     * Register currency provider.
     *
     * @return void
     */
    public function registerCurrency()
    {
        $this->app->singleton('currency', function ($app) {
            $config = $app->config->get('currency', []);
            return new Currency($config, $app['cache']);
        });
    }

    /**
     * Register generator of Currency.
     *
     * @return void
     */
    public function registerCurrencyCommands()
    {
        $this->app['currency.update'] = $this->app->share(function ($app) {
            return new Commands\CurrencyUpdateCommand($app['currency']);
        });

        $this->app['currency.cleanup'] = $this->app->share(function ($app) {
            return new Commands\CurrencyCleanupCommand($app['currency']);
        });

        $this->commands(
            'currency.update',
            'currency.cleanup'
        );
    }

    /**
     * Check if package is running under Lumen app
     *
     * @return bool
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen') === true;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'currency',
        ];
    }
}