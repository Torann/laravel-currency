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
    protected $code;

    /**
     * Currency driver instance.
     *
     * @var Drivers\AbstractDriver
     */
    protected $driver;

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
     * @param float  $number
     * @param null   $currency
     * @param string $symbolStyle
     * @param bool   $inverse
     * @param string $roundingType
     * @param null   $precision
     * @param null   $decimalPlace
     *
     * @return string
     */
    public function format($number, $currency = null, $symbolStyle = '%symbol%', $inverse = false, $roundingType = '', $precision = null, $decimalPlace = null)
    {
        if (!$currency || !$this->hasCurrency($currency)) {
            $currency = $this->code;
        }

        $symbolLeft = $this->getCurrencyProp($currency, 'symbol_left');
        $symbolRight = $this->getCurrencyProp($currency, 'symbol_right');

        if (is_null($decimalPlace)) {
            $decimalPlace = $this->getCurrencyProp($currency, 'decimal_place');
        }

        $decimalPoint = $this->getCurrencyProp($currency, 'decimal_point');
        $thousandPoint = $this->getCurrencyProp($currency, 'thousand_point');

        if ($value = $this->getCurrencyProp($currency, 'value')) {
            if ($inverse) {
                $value = $number * (1 / $value);
            }
            else {
                $value = $number * $value;
            }
        }
        else {
            $value = $number;
        }

        $string = '';

        if ($symbolLeft) {
            $string .= str_replace('%symbol%', $symbolLeft, $symbolStyle);

            if ($this->getConfig('use_space')) {
                $string .= ' ';
            }
        }

        switch ($roundingType) {
            case 'ceil':
            case 'ceiling':
                if ($precision != null) {
                    $multiplier = pow(10, -(int)$precision);
                }
                else {
                    $multiplier = pow(10, -(int)$decimalPlace);
                }

                $string .= number_format(ceil($value / $multiplier) * $multiplier, (int)$decimalPlace, $decimalPoint, $thousandPoint);
                break;

            case 'floor':
                if ($precision != null) {
                    $multiplier = pow(10, -(int)$precision);
                }
                else {
                    $multiplier = pow(10, -(int)$decimalPlace);
                }

                $string .= number_format(floor($value / $multiplier) * $multiplier, (int)$decimalPlace, $decimalPoint, $thousandPoint);
                break;

            default:
                if ($precision == null) {
                    $precision = (int)$decimalPlace;
                }

                $string .= number_format(round($value, (int)$precision), (int)$decimalPlace, $decimalPoint, $thousandPoint);
                break;
        }

        if ($symbolRight) {
            if ($this->getConfig('use_space')) {
                $string .= ' ';
            }

            $string .= str_replace('%symbol%', $symbolRight, $symbolStyle);
        }

        return $string;
    }

    /**
     * Normalize number
     *
     * @param float $number
     * @param bool  $dec
     *
     * @return string
     */
    public function normalize($number, $dec = false)
    {
        $value = $this->getCurrencyProp($this->code, 'value');

        $value = $value ? ($number * $value) : $number;

        if ($dec === false) {
            $dec = $this->getCurrencyProp($this->code, 'decimal_place');
        }

        return number_format(round($value, (int)$dec), (int)$dec, '.', '');
    }

    /**
     * Get currency symbol.
     *
     * @param bool $right
     *
     * @return mixed
     */
    public function getCurrencySymbol($right = false)
    {
        if ($right) {
            return $this->getCurrencyProp($this->code, 'symbol_right');
        }

        return $this->getCurrencyProp($this->code, 'symbol_left');
    }

    /**
     * Determine if given currency exists.
     *
     * @param string $code
     *
     * @return bool
     */
    public function hasCurrency($code)
    {
        return $this->getCurrencyValues($code) !== null;
    }

    /**
     * Set user's currency.
     *
     * @param string $code
     */
    public function setCurrency($code)
    {
        $this->code = $code;
    }

    /**
     * Return the user's currency code.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->code;
    }

    /**
     * Return the current currency if the
     * one supplied is not valid.
     *
     * @param string $code
     *
     * @return array
     */
    public function getCurrency($code = null)
    {
        if ($code && $this->hasCurrency($code)) {
            return $this->getCurrencyValues($code);
        }
        else {
            return $this->getCurrencyValues($this->code);
        }
    }

    /**
     * Get moderation driver.
     *
     * @return Drivers\AbstractDriver
     */
    public function getDriver()
    {
        if ($this->driver) {
            return $this->driver;
        }

        // Get driver configuration
        $config = $this->getConfig('drivers.' . $this->getConfig('driver'), []);

        // Get driver class
        $driver = Arr::pull($config, 'class');

        // Create driver instance
        return $this->driver = app($driver, [$config]);
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
    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get values for given currency.
     *
     * @param string $currency
     *
     * @return array|null
     */
    protected function getCurrencyValues($currency)
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

        return array_key_exists($currency, $this->currencies_cache)
            ? $this->currencies_cache[$currency]
            : null;
    }

    /**
     * Get a property from currency.
     *
     * @param string $currency
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    protected function getCurrencyProp($currency, $key, $default = null)
    {
        return Arr::get($this->getCurrencyValues($currency), $key, $default);
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
