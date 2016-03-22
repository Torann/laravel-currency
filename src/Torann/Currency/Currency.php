<?php

namespace Torann\Currency;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Cache\Factory as FactoryContract;

class Currency
{
    /**
     * Currency configuration.
     *
     * @var array
     */
    public $config = [];

    /**
     * Laravel application
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    public $cache;

    /**
     * Session manager instance.
     *
     * @var \Illuminate\Session\SessionManager
     */
    public $session;

    /**
     * Default currency
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
     * All currencies
     *
     * @var array
     */
    protected $currencies = [];

    /**
     * Create a new instance.
     *
     * @param array                               $config
     * @param \Illuminate\Contracts\Cache\Factory $cache
     * @param \Illuminate\Session\SessionManager  $session
     * @param \Illuminate\Http\Request            $request
     */
    public function __construct(array $config, FactoryContract $cache, SessionManager $session, Request $request)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->session = $session;

        // Initialize Currencies
        $this->setCacheCurrencies();

        // Check for a user defined currency
        if ($request->get('currency') && $this->hasCurrency($request->get('currency'))) {
            $this->setCurrency($request->get('currency'));
        }
        elseif ($this->session->get('currency') && $this->hasCurrency($this->session->get('currency'))) {
            $this->setCurrency($this->session->get('currency'));
        }
        else {
            $this->setCurrency($this->getConfig('default'));
        }
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

        $symbolLeft = $this->currencies[$currency]['symbol_left'];
        $symbolRight = $this->currencies[$currency]['symbol_right'];

        if (is_null($decimalPlace)) {
            $decimalPlace = $this->currencies[$currency]['decimal_place'];
        }

        $decimalPoint = $this->currencies[$currency]['decimal_point'];
        $thousandPoint = $this->currencies[$currency]['thousand_point'];

        if ($value = $this->currencies[$currency]['value']) {
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
        $value = $this->currencies[$this->code]['value'];

        if ($value) {
            $value = $number * $value;
        }
        else {
            $value = $number;
        }

        if (!$dec) {
            $dec = $this->currencies[$this->code]['decimal_place'];
        }

        return number_format(round($value, (int)$dec), (int)$dec, '.', '');
    }

    /**
     * Get currency number.
     *
     * @param bool $right
     *
     * @return mixed
     */
    public function getCurrencySymbol($right = false)
    {
        if ($right) {
            return $this->currencies[$this->code]['symbol_right'];
        }

        return $this->currencies[$this->code]['symbol_left'];
    }

    /**
     * Determine if given currency exists.
     *
     * @param string $currency
     *
     * @return bool
     */
    public function hasCurrency($currency)
    {
        return array_key_exists($currency, $this->currencies);
    }

    /**
     * Set currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->code = $currency;

        if ($this->session) {
            $this->session->set('currency', $currency);
        }
    }

    /**
     * Return the current currency code
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
     * @param string $currency
     *
     * @return array
     */
    public function getCurrency($currency = '')
    {
        if ($currency && $this->hasCurrency($currency)) {
            return $this->currencies[$currency];
        }
        else {
            return $this->currencies[$this->code];
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
     * Set cached currencies.
     *
     * @return array
     */
    public function setCacheCurrencies()
    {
        if (config('app.debug', false) === true) {
            return $this->currencies = $this->getDriver()->all();
        }

        return $this->currencies = $this->cache->rememberForever('torann.currency', function () {
            return $this->getDriver()->all();
        });
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
}
