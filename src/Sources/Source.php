<?php

namespace Torann\Currency\Sources;

use \DateTime;
use \DateTimeInterface;
use Illuminate\Support\Str;
use Torann\Currency\Contracts\SourceInterface;
use Torann\Currency\Exceptions\InvalidArgumentException;

abstract class Source implements SourceInterface
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
     *
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     */
    public function __construct()
    {
        $this->currency = app('currency');

        $this->baseUrl = rtrim($this->baseUrl, '/');

        $this->setDefaultCurrency();

        if ($this->mustHaveAccessKey()) {
            $this->setAccessKey();
        }
    }

    /**
     * Set a default currency.
     *
     * @return void
     *
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     */
    protected function setDefaultCurrency()
    {
        $this->defaultCurrency = config('currency.default');

        if (! $this->defaultCurrency) {
            throw new InvalidArgumentException("Currency [{$this->defaultCurrency}] is not defined.");
        }
    }

    /**
     * Determine the source must have an access key.
     *
     * @return bool
     */
    protected function mustHaveAccessKey()
    {
        return array_key_exists('key', config("currency.sources.{$this->name()}", []));
    }

    /**
     * Set the access key if necessary.
     *
     * @return void
     *
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     */
    protected function setAccessKey()
    {
        $this->accessKey = config("currency.sources.{$this->name()}.key");

        if (! $this->accessKey) {
            throw new InvalidArgumentException("API key is required for [{$this->name()}] source.");
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
        return collect($this->currency->getDriver()->all())
            ->reject(function ($currency) {
                return $currency['code'] === $this->defaultCurrency;
            })
            ->map(function ($currency) {
                return $currency['code'];
            })
            ->values()
            ->all();
    }

    /**
     * Format the given currency code, rate and timestamp.
     *
     * @param  string  $currencyCode
     * @param  mixed  $rate
     * @param  \DateTimeInterface|null  $updatedAt
     * @return array
     *
     * @throws \Exception
     */
    protected function formatRate($currencyCode, $rate, DateTimeInterface $updatedAt = null)
    {
        $updatedAt = $updatedAt ?: new DateTime('now');

        return [
            $currencyCode => [
                'code' => $currencyCode,
                'rate' => $rate,
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
        $className = class_basename($this);

        if (substr($className, -6) === 'Source') {
            $className = substr($className, 0, -6);
        }

        return Str::snake($className);
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

    /**
     * Parse the response to array.
     *
     * @param  mixed  $response
     * @return array
     */
    protected function responseToArray($response)
    {
        return json_decode($response, true);
    }
}
