<?php

namespace Torann\Currency;

use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCurrency();

        $this->registerManager();

        if ($this->app->runningInConsole()) {
            $this->registerResources();
            $this->registerCurrencyCommands();
        }
    }

    /**
     * Register currency provider.
     *
     * @return void
     */
    public function registerCurrency()
    {
        $this->app->singleton('currency', function ($app) {
            return new Currency(
                $app->config->get('currency', []),
                $app['cache']
            );
        });
    }

    /**
     * Register currency manager.
     *
     * @return void
     */
    public function registerManager()
    {
        $this->app->singleton(SourceManager::class, function ($app) {
            return new SourceManager($app);
        });
    }

    /**
     * Register currency resources.
     *
     * @return void
     */
    public function registerResources()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/../config/currency.php' => config_path('currency.php'),
            ], 'config');

            $this->mergeConfigFrom(
                __DIR__ . '/../config/currency.php', 'currency'
            );
        }

        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path('/database/migrations'),
        ], 'migrations');
    }

    /**
     * Register currency commands.
     *
     * @return void
     */
    public function registerCurrencyCommands()
    {
        $this->commands([
            Console\Cleanup::class,
            Console\Manage::class,
            Console\Update::class,
        ]);
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
}
