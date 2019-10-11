<?php

namespace Torann\Currency;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class SourceManager extends Manager
{
    /**
     * The default channel used to deliver messages.
     *
     * @var string
     */
    protected $defaultSource;

    /**
     * Update the currencies exchange rates.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function update()
    {
        (new CurrencyRatesUpdater($this))->update();
    }

    /**
     * Get a channel instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function source($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an instance of the ExchangeRatesApi.io driver.
     *
     * @return \Torann\Currency\Sources\ExchangeratesapiSource
     */
    protected function createExchangeratesapiDriver()
    {
        return $this->app->make(Sources\ExchangeratesapiSource::class);
    }

    /**
     * Create an instance of the Fixer.io driver.
     *
     * @return \Torann\Currency\Sources\FixerSource
     */
    protected function createFixerDriver()
    {
        return $this->app->make(Sources\FixerSource::class);
    }

    /**
     * Create an instance of the CurrencyLayer driver.
     *
     * @return \Torann\Currency\Sources\CurrencylayerSource
     */
    protected function createCurrencylayerDriver()
    {
        return $this->app->make(Sources\CurrencylayerSource::class);
    }

    /**
     * Create an instance of the OpenExchangeRates driver.
     *
     * @return \Torann\Currency\Sources\OpenexchangeratesSource
     */
    protected function createOpenexchangeratesDriver()
    {
        return $this->app->make(Sources\OpenexchangeratesSource::class);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $e) {
            if (class_exists($driver)) {
                return $this->app->make($driver);
            }

            throw $e;
        }
    }

    /**
     * Get the default source driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultSource ?? $this->app['config']['currency.source'];
    }

    /**
     * Get the default source driver name.
     *
     * @return string
     */
    public function fetchesFrom()
    {
        return $this->getDefaultDriver();
    }

    /**
     * Set the default source driver name.
     *
     * @param  string  $source
     * @return $this
     */
    public function fetchFrom($source)
    {
        $this->defaultSource = $source;

        return $this;
    }
}
