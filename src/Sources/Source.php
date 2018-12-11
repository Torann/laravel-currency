<?php

namespace Torann\Currency\Sources;

use \DateTime;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Torann\Currency\Contracts\Source as SourceContract;

abstract class Source implements SourceContract
{
    /**
     * The Currency instance.
     *
     * @var \Torann\Currency\Currency
     */
    protected $currency;

    /**
     * Base URL of external API service.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Code of default currency.
     *
     * @var string
     */
    protected $defaultCurrency;

    /**
     * API access key.
     *
     * @var string
     */
    protected $accessKey;

    /**
     * Create an instance of source.
     *
     * @return void
     */
    public function __construct()
    {
        $this->currency = app('currency');

        $this->baseUrl = rtrim($this->baseUrl, '/');

        $this->defaultCurrency = config('currency.default');

        if (! $this->defaultCurrency) {
            throw new InvalidArgumentException("Currency [{$this->defaultCurrency}] is not defined.");
        }

        if ($this->mustHaveAccessKey()) {
            $this->setAccessKey();
        }
    }

    /**
     * Determine the source must have an access key.
     *
     * @return bool
     */
    protected function mustHaveAccessKey()
    {
        return array_key_exists(
            'key', config("currency.sources.{$this->name()}", [])
        );
    }

    /**
     * Set the access key if necessary.
     *
     * @return void
     */
    protected function setAccessKey()
    {
        $this->accessKey = config("currency.sources.{$this->name()}.key");

        if (! $this->accessKey) {
            throw new InvalidArgumentException(
                "API key is required for [{$this->name()}] source."
            );
        }
    }

    /**
     * Fetch the exchange rates for the default currency.
     *
     * @return array
     */
    abstract public function fetch();

    /**
     * Get an array of currency codes to fetching.
     *
     * @return array
     */
    protected function currencies()
    {
        $currencies = array_map(function ($currency) {
            return $currency['code'];
        }, $this->currency->getDriver()->all());

        return array_values(array_filter($currencies, function ($currency) {
            return $currency !== $this->defaultCurrency;
        }));
    }

    /**
     * Format the given currency code, rate and timestamp.
     *
     * @param  string  $currencyCode
     * @param  mixed  $rate
     * @param  \DateTime|null  $updatedAt
     * @return array
     *
     * @throws \Exception
     */
    protected function formatRate($currencyCode, $rate, DateTime $updatedAt = null)
    {
        $updatedAt = $updatedAt ?: new DateTime('now');

        return [
            $currencyCode => [
                'code' => $currencyCode,
                'rate' => (float) $rate,
                'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Get the name of the source.
     *
     * @return string
     */
    public function name()
    {
        return Str::snake(str_replace('Source', '', class_basename($this)));
    }

    /**
     * Make a request to the external API service.
     *
     * @param  string  $url
     * @return string
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
}
