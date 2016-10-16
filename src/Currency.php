<?php

namespace Torann\Currency;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Cache\Factory as FactoryContract;

class Currency
{
    /**
     * Currency configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Laravel application
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * User's currency
     *
     * @var string
     */
    protected $user_currency;

    /**
     * Currency driver instance.
     *
     * @var Contracts\DriverInterface
     */
    protected $driver;

    /**
     * Formatter instance.
     *
     * @var Contracts\FormatterInterface
     */
    protected $formatter;

    /**
     * Cached currencies
     *
     * @var array
     */
    protected $currencies_cache;

    /**
     * Create a new instance.
     *
     * @param array           $config
     * @param FactoryContract $cache
     */
    public function __construct(array $config, FactoryContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Format given number.
     *
     * @param float  $amount
     * @param string $from
     * @param string $to
     * @param bool   $format
     *
     * @return string
     */
    public function convert($amount, $from = null, $to = null, $format = true)
    {
        // Get currencies involved
        $from = $from ?: $this->config('default');
        $to = $to ?: $this->getUserCurrency();

        // Get exchange rates
        $from_rate = $this->getCurrencyProp($from, 'exchange_rate');
        $to_rate = $this->getCurrencyProp($to, 'exchange_rate');

        // Skip invalid to currency rates
        if ($to_rate === null) {
            return null;
        }

        // Convert amount
        $value = $amount * $to_rate * (1 / $from_rate);

        // To format or not to format?
        return $format === true
            ? $this->format($value, $to)
            : $value;
    }

    /**
     * Format the value into the desired currency.
     *
     * @param float  $value
     * @param string $code
     *
     * @return string
     */
    public function format($value, $code = null)
    {
        // Get default currency if one is not set
        $code = $code ?: $this->config('default');

        // Check for a custom formatter
        if ($formatter = $this->getFormatter()) {
            return $formatter->format($value, $code);
        }

        // Get the measurement format
        $format = $this->getCurrencyProp($code, 'format');

        // Value Regex
        $valRegex = '/([0-9].*|)[0-9]/';

        // Match decimal and thousand separators
        preg_match_all('/[\s\',.!]/', $format, $separators);

        if ($thousand = array_get($separators, '0.0', null)) {
            if ($thousand == '!') {
                $thousand = '';
            }
        }

        $decimal = array_get($separators, '0.1', null);

        // Match format for decimals count
        preg_match($valRegex, $format, $valFormat);

        $valFormat = array_get($valFormat, 0, 0);

        // Count decimals length
        $decimals = $decimal ? strlen(substr(strrchr($valFormat, $decimal), 1)) : 0;

        // Format the value
        $value = number_format($value, $decimals, $decimal, $thousand);

        // Return the formatted measurement
        return preg_replace($valRegex, $value, $format);
    }

    /**
     * Set user's currency.
     *
     * @param string $code
     */
    public function setUserCurrency($code)
    {
        $this->user_currency = strtoupper($code);
    }

    /**
     * Return the user's currency code.
     *
     * @return string
     */
    public function getUserCurrency()
    {
        return $this->user_currency ?: $this->config('default');
    }

    /**
     * Determine if the provided currency is valid.
     *
     * @param string $code
     *
     * @return array|null
     */
    public function hasCurrency($code)
    {
        return array_key_exists(strtoupper($code), $this->getCurrencies());
    }

    /**
     * Determine if the provided currency is active.
     *
     * @param string $code
     *
     * @return bool
     */
    public function isActive($code)
    {
        return (bool) Arr::get($this->getCurrency($code), 'active', false);
    }

    /**
     * Return the current currency if the
     * one supplied is not valid.
     *
     * @param string $code
     *
     * @return array|null
     */
    public function getCurrency($code = null)
    {
        $code = $code ?: $this->config('default');

        return Arr::get($this->getCurrencies(), strtoupper($code));
    }

    /**
     * Return all currencies.
     *
     * @return array
     */
    public function getCurrencies()
    {
        if ($this->currencies_cache === null) {
            if (config('app.debug', false) === true) {
                $this->currencies_cache = $this->getDriver()->all();
            }
            else {
                $this->currencies_cache = $this->cache->rememberForever('torann.currency', function () {
                    return $this->getDriver()->all();
                });
            }
        }

        return $this->currencies_cache;
    }

    /**
     * Return all active currencies.
     *
     * @return array
     */
    public function getActiveCurrencies()
    {
        return array_filter($this->getCurrencies(), function($currency) {
            return $currency['active'] == true;
        });
    }

    /**
     * Get storage driver.
     *
     * @return \Torann\Currency\Contracts\DriverInterface
     */
    public function getDriver()
    {
        if ($this->driver === null) {
            // Get driver configuration
            $config = $this->config('drivers.' . $this->config('driver'), []);

            // Get driver class
            $driver = Arr::pull($config, 'class');

            // Create driver instance
            $this->driver = app($driver, [$config]);
        }

        return $this->driver;
    }

    /**
     * Get formatter driver.
     *
     * @return \Torann\Currency\Contracts\FormatterInterface
     */
    public function getFormatter()
    {
        if ($this->formatter === null && $this->config('formatter') !== null) {
            // Get formatter configuration
            $config = $this->config('formatters.' . $this->config('formatter'), []);

            // Get formatter class
            $class = Arr::pull($config, 'class');

            // Create formatter instance
            $this->formatter = app($class, array_filter([$config]));
        }

        return $this->formatter;
    }

    /**
     * Clear cached currencies.
     */
    public function clearCache()
    {
        $this->cache->forget('torann.currency');
    }

    /**
     * Get configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get the given property value from provided currency.
     *
     * @param string $code
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    protected function getCurrencyProp($code, $key, $default = null)
    {
        return Arr::get($this->getCurrency($code), $key, $default);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getDriver(), $method], $parameters);
    }
}
