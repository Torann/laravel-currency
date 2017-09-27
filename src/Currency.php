<?php

namespace Torann\Currency;

use DateTime;
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
    protected $active_cache;

    /**
     * Create a new instance.
     *
     * @param array           $config
     * @param FactoryContract $cache
     */
    public function __construct(array $config, FactoryContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache->store($this->config('cache_driver'));
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
        $this->activateCurrencyIfNeeded($from);
        $this->activateCurrencyIfNeeded($to);

        // Get currencies involved
        $from = $from ?: $this->config('default');
        $to = $to ?: $this->getUserCurrency();

        // Get exchange rates
        $from_rate = $this->getCurrencyProp($from, 'exchange_rate');
        $to_rate = $this->getCurrencyProp($to, 'exchange_rate');

        // Skip invalid to currency rates
        if (! $to_rate || ! $from_rate) {
            return null;
        }

        // Convert amount
        $value = $amount * $to_rate * (1 / $from_rate);

        // Should the result be formatted?
        if ($format) {
            return $this->format($value, $to);
        }

        // Return value
        return $value;
    }

    /**
     * Active currency and update exchange rate if neeed.
     *
     * @param  string  $code
     * @return void
     *
     * @throws \Exception
     */
    protected function activateCurrencyIfNeeded($code)
    {
        if (! $this->isValidCurrency($code)) {
            throw new \Exception("Given currency is not valid: {$code}");
        }

        if ($this->hasCurrency($code)) {
            return;
        }

        $this->getDriver()->activate($code);

        $this->updateRates();
    }

    /**
     * Format the value into the desired currency.
     *
     * @param float  $value
     * @param string $code
     * @param bool   $include_symbol
     *
     * @return string
     */
    public function format($value, $code = null, $include_symbol = true)
    {
        // Get default currency if one is not set
        $code = $code ?: $this->config('default');

        // Remove unnecessary characters
        $value = preg_replace('/[\s\',!]/', '', $value);

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

        // Do we have a negative value?
        if ($negative = $value < 0 ? '-' : '') {
            $value = $value * -1;
        }

        // Format the value
        $value = number_format($value, $decimals, $decimal, $thousand);

        // Apply the formatted measurement
        if ($include_symbol) {
            $value = preg_replace($valRegex, $value, $format);
        }

        // Return value
        return $negative.$value;
    }

    /**
     * Update exchange rates from Yahoo or OpenExchangeRates.
     *
     * @param bool $openexchangerates
     * @return bool
     */
    public function updateRates($openexchangerates = false)
    {
        return $openexchangerates ? $this->updateFromOpenExchangeRates() : $this->updateFromYahoo();
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
     * Determine if the provided currency is exist.
     *
     * @param string $code
     *
     * @return bool
     */
    public function hasCurrency($code)
    {
        return array_key_exists(strtoupper($code), $this->getCurrencies());
    }

    /**
     * Determine if the provided currency is valid.
     *
     * @param string $code
     *
     * @return bool
     */
    public function isValidCurrency($code)
    {
        return array_key_exists(strtoupper($code), $this->getAllCurrencies());
    }

    /**
     * Return the current currency if the one supplied is not valid.
     *
     * @param string $code
     *
     * @return array|null
     */
    public function getCurrency($code = null)
    {
        $code = $code ?: $this->getUserCurrency();

        return Arr::get($this->getCurrencies(), strtoupper($code));
    }

    /**
     * Return all existed currencies.
     *
     * @return array
     */
    public function getAllCurrencies()
    {
        return $this->getDriver()->all();
    }

    /**
     * Return all supported currencies.
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->getDriver()->allActive();
    }

    /**
     * Get storage driver.
     *
     * @return \Torann\Currency\Contracts\DriverInterface
     */
    public function getDriver()
    {
        if (! $this->driver) {
            // Get driver configuration
            $config = $this->config('drivers.'.$this->config('driver'), []);

            // Get driver class
            $driver = Arr::pull($config, 'class');

            // Create driver instance
            $this->driver = new $driver($config);
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
            $config = $this->config('formatters.'.$this->config('formatter'), []);

            // Get formatter class
            $class = Arr::pull($config, 'class');

            // Create formatter instance
            $this->formatter = new $class(array_filter($config));
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
        if (! $key) {
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
     * Update exchange rates from Yahoo.
     *
     * @return bool
     */
    protected function updateFromYahoo()
    {
        // Get Settings
        $defaultCurrency = $this->config('default');

        $data = [];

        // Get all currencies
        foreach ($this->getDriver()->allActive() as $code => $value) {
            $data[] = "{$defaultCurrency}{$code}=X";
        }

        // Ask Yahoo for exchange rate
        if ($data) {
            $content = $this->request('http://download.finance.yahoo.com/d/quotes.csv?s='.implode(',', $data).'&f=sl1&e=.csv');

            $lines = explode("\n", trim($content));

            // Update each rate
            foreach ($lines as $line) {
                $code = substr($line, 4, 3);
                $value = substr($line, 11, 6) * 1.00;

                if ($value) {
                    $this->getDriver()->update($code, [
                        'exchange_rate' => $value,
                    ]);
                }
            }

            // Clear cache
            $this->clearCache();

            // Force the system to rebuild cache
            $this->getCurrencies();
        }

        return true;
    }

    /**
     * Update exchange rates from OpenExchangeRates.
     *
     * @return bool
     */
    protected function updateFromOpenExchangeRates()
    {
        if (!$api = $this->config('api_key')) {
            return false;
        }

        // Get Settings
        $defaultCurrency = $this->config('default');

        // Make request
        $content = json_decode($this->request("http://openexchangerates.org/api/latest.json?base={$defaultCurrency}&app_id={$api}"));

        // Error getting content?
        if (isset($content->error)) {
            // TODO: Return the description of error ($content->description) maybe?
            return false;
        }

        // Parse timestamp for DB
        $timestamp = new DateTime(strtotime($content->timestamp));

        // Update each rate
        foreach ($content->rates as $code => $value) {
            $this->getDriver()->update($code, [
                'exchange_rate' => $value,
                'updated_at' => $timestamp,
            ]);
        }

        $this->clearCache();

        return true;
    }

    /**
     * Make a GET request to given URL.
     *
     * @param string $url
     *
     * @return mixed
     */
    protected function request($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_MAXCONNECTS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Get a given value from the current currency.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return Arr::get($this->getCurrency(), $key);
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
